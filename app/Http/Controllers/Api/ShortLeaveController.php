<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEmployeeResource;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ShortLeave;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\ShortLeaveSyncService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShortLeaveController extends Controller
{
    use AuthorizesEmployeeResource;

    public function __construct(
        protected NotificationService $notifier,
        protected ShortLeaveSyncService $syncService
    ) {
    }

    public function index(Request $request)
    {
        $query = ShortLeave::with(['employee.user', 'employee.department', 'approver', 'leaveApplication']);

        $this->scopeToAccessibleEmployees($query, $request, 'short_leaves');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $shortLeaves = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($shortLeaves);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'category' => 'required|in:short_leave,exemption',
            'exemption_type' => 'required_if:category,exemption|nullable|in:late_arrival,early_departure,missed_punch,official_duty,other',
            'date' => 'required|date',
            'from_time' => 'required_if:category,short_leave|nullable|date_format:H:i',
            'to_time' => 'required_if:category,short_leave|nullable|date_format:H:i|after:from_time',
            'reason' => 'required|string|max:2000',
        ]);

        $user = $request->user()->loadMissing('employee');
        $canApplyForOthers = $user->hasPermission('short_leaves.manage');

        if (empty($validated['employee_id'])) {
            if (!$user->employee) {
                return response()->json([
                    'message' => 'No employee profile is linked to your account. Please select an employee.',
                ], 422);
            }
            $validated['employee_id'] = $user->employee->id;
        } elseif (!$canApplyForOthers && $user->employee?->id != $validated['employee_id']) {
            return response()->json(['message' => 'You can only apply for yourself'], 403);
        }

        $durationMinutes = 0;
        if (!empty($validated['from_time']) && !empty($validated['to_time'])) {
            $durationMinutes = Carbon::parse($validated['from_time'])
                ->diffInMinutes(Carbon::parse($validated['to_time']));

            // Short leaves are capped at half a working day
            if ($validated['category'] === 'short_leave' && $durationMinutes > 240) {
                return response()->json([
                    'message' => 'A short leave cannot exceed 4 hours. Please apply for a regular leave instead.',
                ], 422);
            }
        }

        // One pending/approved request per employee per date and category
        $duplicate = ShortLeave::where('employee_id', $validated['employee_id'])
            ->where('category', $validated['category'])
            ->whereDate('date', $validated['date'])
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'A pending or approved request of this type already exists for this date.',
            ], 422);
        }

        $shortLeave = DB::transaction(function () use ($validated, $durationMinutes, $user) {
            $shortLeave = ShortLeave::create([
                ...$validated,
                'duration_minutes' => $durationMinutes,
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            $this->syncService->syncLeaveApplication($shortLeave);

            return $shortLeave;
        });

        $employee = Employee::with(['user', 'department'])->find($validated['employee_id']);
        if ($employee) {
            $label = $this->categoryLabel($shortLeave);

            $approverIds = array_filter([$this->sectionHeadApproverId($employee)]);
            $approverIds = array_merge($approverIds, User::whereIn('role', ['hr_admin', 'super_admin', 'admin'])
                ->where('is_active', true)
                ->pluck('id')
                ->all());

            $this->notifier->notifyUsers(
                array_diff($approverIds, [$user->id]),
                'short_leave_request',
                "New {$label} Request",
                "{$employee->full_name} has requested a {$label} on " . $shortLeave->date->format('M j, Y'),
                $this->notificationData($shortLeave, $employee),
                '/short-leaves?id=' . $shortLeave->id
            );

            if ($employee->user_id && $employee->user_id !== $user->id) {
                $this->notifier->notifyUser(
                    $employee->user_id,
                    'short_leave_request',
                    "{$label} Request Submitted",
                    "A {$label} request for " . $shortLeave->date->format('M j, Y') . ' was submitted on your behalf',
                    $this->notificationData($shortLeave, $employee),
                    '/short-leaves?id=' . $shortLeave->id
                );
            }
        }

        return response()->json($shortLeave->load(['employee.user', 'employee.department', 'leaveApplication.leaveType']), 201);
    }

    public function show(Request $request, ShortLeave $shortLeave)
    {
        $this->assertCanAccessEmployeeRecord($request, $shortLeave->employee_id, 'short_leaves');

        return response()->json($shortLeave->load(['employee.user', 'employee.department', 'approver', 'creator', 'leaveApplication.leaveType']));
    }

    public function approve(Request $request, ShortLeave $shortLeave)
    {
        $user = $request->user();
        $this->assertCanAccessEmployeeRecord($request, $shortLeave->employee_id, 'short_leaves');

        if ($shortLeave->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 400);
        }

        if ($user->employee && $user->employee->id === $shortLeave->employee_id) {
            return response()->json(['message' => 'You cannot approve your own request'], 403);
        }

        $validated = $request->validate([
            'approval_remarks' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($shortLeave, $user, $validated) {
            $shortLeave->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'approval_remarks' => $validated['approval_remarks'] ?? null,
            ]);

            $this->syncService->syncLeaveApplication($shortLeave);
        });

        $employee = $shortLeave->employee;
        $label = $this->categoryLabel($shortLeave);

        $this->notifier->notifyUser(
            $employee?->user_id,
            'short_leave_approved',
            "{$label} Approved",
            "Your {$label} request for " . $shortLeave->date->format('M j, Y') . ' has been approved',
            $this->notificationData($shortLeave, $employee),
            '/short-leaves?id=' . $shortLeave->id
        );

        return response()->json([
            'message' => "{$label} approved successfully",
            'short_leave' => $shortLeave->load(['employee.user', 'employee.department', 'approver', 'leaveApplication.leaveType']),
        ]);
    }

    public function reject(Request $request, ShortLeave $shortLeave)
    {
        $user = $request->user();
        $this->assertCanAccessEmployeeRecord($request, $shortLeave->employee_id, 'short_leaves');

        if ($shortLeave->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 400);
        }

        if ($user->employee && $user->employee->id === $shortLeave->employee_id) {
            return response()->json(['message' => 'You cannot reject your own request'], 403);
        }

        $validated = $request->validate([
            'approval_remarks' => 'required|string|max:2000',
        ]);

        DB::transaction(function () use ($shortLeave, $user, $validated) {
            $shortLeave->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'approval_remarks' => $validated['approval_remarks'],
            ]);

            $this->syncService->syncLeaveApplication($shortLeave);
        });

        $employee = $shortLeave->employee;
        $label = $this->categoryLabel($shortLeave);

        $this->notifier->notifyUser(
            $employee?->user_id,
            'short_leave_rejected',
            "{$label} Rejected",
            "Your {$label} request for " . $shortLeave->date->format('M j, Y') . " was rejected: {$validated['approval_remarks']}",
            $this->notificationData($shortLeave, $employee),
            '/short-leaves?id=' . $shortLeave->id,
            'high'
        );

        return response()->json([
            'message' => "{$label} rejected",
            'short_leave' => $shortLeave->load(['employee.user', 'employee.department', 'approver', 'leaveApplication.leaveType']),
        ]);
    }

    public function cancel(Request $request, ShortLeave $shortLeave)
    {
        $user = $request->user()->loadMissing('employee');
        $isOwner = $user->employee && $user->employee->id === $shortLeave->employee_id;
        $canManage = $user->hasPermission('short_leaves.manage');

        if (!$isOwner && !$canManage) {
            return response()->json(['message' => 'You can only cancel your own requests'], 403);
        }

        if (!in_array($shortLeave->status, ['pending', 'approved'], true)) {
            return response()->json(['message' => 'Only pending or approved requests can be cancelled'], 400);
        }

        DB::transaction(function () use ($shortLeave) {
            $shortLeave->update(['status' => 'cancelled']);
            $this->syncService->syncLeaveApplication($shortLeave);
        });

        return response()->json($shortLeave->load('leaveApplication.leaveType'));
    }

    public function destroy(Request $request, ShortLeave $shortLeave)
    {
        $user = $request->user()->loadMissing('employee');
        $isOwner = $user->employee && $user->employee->id === $shortLeave->employee_id;

        if (!$isOwner && !$user->hasPermission('short_leaves.manage')) {
            return response()->json(['message' => 'You are not authorized to delete this request'], 403);
        }

        if ($shortLeave->status !== 'pending' && !$user->hasPermission('short_leaves.manage')) {
            return response()->json(['message' => 'Only pending requests can be deleted'], 400);
        }

        $shortLeave->delete();

        return response()->json(['message' => 'Request deleted successfully']);
    }

    private function sectionHeadApproverId(Employee $employee): ?int
    {
        if ($employee->manager_id) {
            return (int) $employee->manager_id;
        }

        $employee->loadMissing('department');

        return $employee->department?->manager_id ? (int) $employee->department->manager_id : null;
    }

    private function categoryLabel(ShortLeave $shortLeave): string
    {
        return $shortLeave->category === 'exemption' ? 'Exemption' : 'Short Leave';
    }

    private function notificationData(ShortLeave $shortLeave, ?Employee $employee): array
    {
        return [
            'short_leave_id' => $shortLeave->id,
            'category' => $shortLeave->category,
            'exemption_type' => $shortLeave->exemption_type,
            'employee_id' => $employee?->id,
            'employee_name' => $employee?->full_name,
            'date' => $shortLeave->date->toDateString(),
            'from_time' => $shortLeave->from_time,
            'to_time' => $shortLeave->to_time,
            'status' => $shortLeave->status,
        ];
    }
}
