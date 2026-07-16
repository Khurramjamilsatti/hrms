<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEmployeeResource;
use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use App\Models\HelpdeskTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class HelpdeskController extends Controller
{
    use AuthorizesEmployeeResource;

    // Categories
    public function getCategories()
    {
        $this->ensureDefaultCategories();

        $categories = TicketCategory::with('department')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'nullable|boolean',
        ]);

        $category = TicketCategory::create($validated);
        return response()->json($category->load('department'), 201);
    }

    // Statistics
    public function getStatistics(Request $request)
    {
        $query = HelpdeskTicket::query();
        
        if ($request->user()->role === 'employee' && $request->user()->employee) {
            $query->where('employee_id', $request->user()->employee->id);
        }

        return response()->json([
            'total_tickets' => (clone $query)->count(),
            'open_tickets' => (clone $query)->where('status', 'open')->count(),
            'in_progress_tickets' => (clone $query)->where('status', 'in_progress')->count(),
            'resolved_tickets' => (clone $query)->where('status', 'resolved')->count(),
            'closed_tickets' => (clone $query)->where('status', 'closed')->count(),
            'high_priority' => (clone $query)->where('priority', 'urgent')->count(),
        ]);
    }

    // Tickets
    public function getTickets(Request $request)
    {
        $query = HelpdeskTicket::with(['employee.user', 'category', 'assignedTo', 'resolvedBy']);

        if ($request->user()->role === 'employee' && $request->user()->employee) {
            $query->where('employee_id', $request->user()->employee->id);
        }

        // Case-insensitive search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhereHas('employee', function($q2) use ($search) {
                      $q2->where('first_name', 'ilike', "%{$search}%")
                         ->orWhere('last_name', 'ilike', "%{$search}%")
                         ->orWhere('employee_code', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $perPage = $request->get('per_page', 15);
        $tickets = $query->latest()->paginate($perPage);
        return response()->json($tickets);
    }

    public function storeTicket(Request $request)
    {
        $user = $request->user()->loadMissing('employee');

        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'category_id' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $canSelectEmployee = in_array($user->role, ['admin', 'hr_admin', 'super_admin', 'manager', 'section_head'], true);
        $employeeId = $validated['employee_id'] ?? null;

        if (!$employeeId) {
            $employeeId = $user->employee?->id;
        }

        if (!$employeeId) {
            return response()->json([
                'message' => 'Employee profile is required. Select an employee or link your account to an employee record.',
            ], 422);
        }

        // Regular employees can only create tickets for themselves
        if (!$canSelectEmployee && (int) $employeeId !== (int) $user->employee?->id) {
            return response()->json([
                'message' => 'You can only create tickets for yourself',
            ], 403);
        }

        $ticket = HelpdeskTicket::create([
            'employee_id' => $employeeId,
            'category_id' => $validated['category_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'] ?? 'medium',
            'ticket_number' => 'TKT-'.strtoupper(uniqid()),
            'status' => 'open',
        ]);

        return response()->json($ticket->load(['employee.user', 'category']), 201);
    }

    protected function ensureDefaultCategories(): void
    {
        if (TicketCategory::exists()) {
            return;
        }

        $defaults = [
            ['name' => 'Payroll Issues', 'description' => 'Salary, deductions, and payslip related queries'],
            ['name' => 'Leave & Attendance', 'description' => 'Leave applications and attendance queries'],
            ['name' => 'IT Support', 'description' => 'Technical issues and system access'],
            ['name' => 'HR Policies', 'description' => 'Questions about company policies and procedures'],
            ['name' => 'Benefits', 'description' => 'Health insurance, provident fund queries'],
            ['name' => 'Training', 'description' => 'Training enrollment and certificate issues'],
            ['name' => 'Equipment', 'description' => 'Office equipment and asset requests'],
            ['name' => 'Other', 'description' => 'General queries and other issues'],
        ];

        foreach ($defaults as $category) {
            TicketCategory::create([
                ...$category,
                'department_id' => null,
                'is_active' => true,
            ]);
        }
    }

    public function getTicket(Request $request, $id)
    {
        $ticket = HelpdeskTicket::with([
            'employee',
            'category',
            'assignedTo',
            'resolvedBy',
            'replies.user',
        ])->findOrFail($id);

        $this->assertCanAccessHelpdeskTicket($request, $ticket);

        return response()->json($ticket);
    }

    public function updateTicket(Request $request, $id)
    {
        $ticket = HelpdeskTicket::findOrFail($id);
        $this->assertCanAccessHelpdeskTicket($request, $ticket);

        $validated = $request->validate([
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'status' => 'sometimes|in:open,in_progress,resolved,closed,reopened',
            'category_id' => 'sometimes|exists:ticket_categories,id',
        ]);

        if (isset($validated['status']) && $validated['status'] !== $ticket->status) {
            if ($validated['status'] === 'resolved') {
                $validated['resolved_at'] = now();
                $validated['resolved_by'] = $request->user()->id;
            } elseif ($validated['status'] === 'closed') {
                $validated['closed_at'] = now();
                if (!$ticket->resolved_at) {
                    $validated['resolved_at'] = now();
                    $validated['resolved_by'] = $request->user()->id;
                }
            } elseif (in_array($validated['status'], ['open', 'reopened', 'in_progress'], true)) {
                $validated['closed_at'] = null;
            }
        }

        $ticket->update($validated);

        return response()->json($ticket->load(['employee.user', 'category', 'assignedTo', 'resolvedBy']));
    }

    public function assignTicket(Request $request, $id)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket = HelpdeskTicket::findOrFail($id);
        $ticket->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_at' => now(),
            'status' => 'in_progress',
        ]);

        return response()->json($ticket);
    }

    public function resolveTicket(Request $request, $id)
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $ticket = HelpdeskTicket::findOrFail($id);
        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $request->user()->id,
            'resolution_notes' => $validated['resolution_notes'],
        ]);

        return response()->json($ticket);
    }

    public function closeTicket($id)
    {
        $ticket = HelpdeskTicket::findOrFail($id);
        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return response()->json($ticket);
    }

    public function reopenTicket($id)
    {
        $ticket = HelpdeskTicket::findOrFail($id);
        $ticket->update([
            'status' => 'reopened',
            'resolved_at' => null,
            'closed_at' => null,
        ]);

        return response()->json($ticket);
    }

    public function rateTicket(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        $ticket = HelpdeskTicket::findOrFail($id);
        $ticket->update($validated);

        return response()->json($ticket);
    }

    // Replies
    public function addReply(Request $request, $ticketId)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'nullable|boolean',
            'attachment' => 'nullable|string',
        ]);

        $ticket = HelpdeskTicket::findOrFail($ticketId);
        $this->assertCanAccessHelpdeskTicket($request, $ticket);

        $reply = TicketReply::create([
            'ticket_id' => $ticketId,
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_internal' => $validated['is_internal'] ?? false,
            'attachment' => $validated['attachment'] ?? null,
        ]);

        return response()->json($reply->load('user'), 201);
    }

    public function getReplies(Request $request, $ticketId)
    {
        $ticket = HelpdeskTicket::findOrFail($ticketId);
        $this->assertCanAccessHelpdeskTicket($request, $ticket);

        $replies = TicketReply::where('ticket_id', $ticketId)
            ->with('user')
            ->oldest()
            ->get();

        return response()->json($replies);
    }

    public function deleteTicket($id)
    {
        $ticket = HelpdeskTicket::findOrFail($id);
        
        // Check authorization - only allow ticket owner, admin, or manager to delete
        $user = request()->user();
        if ($ticket->employee_id !== $user->employee?->id && !in_array($user->role, ['admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized to delete this ticket'], 403);
        }
        
        $ticket->delete();
        
        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
