<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = LeaveApplication::with(['employee.user', 'employee.department', 'leaveType', 'firstApprover', 'finalApprover']);

        // Role-based data access control
        if ($user->hasRole('employee')) {
            // Employees can only see their own leave applications
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('section_head')) {
            // Section heads see leave applications from employees reporting to them + their own
            $teamEmployeeIds = Employee::where('manager_id', $user->id)->pluck('id')->toArray();
            
            // Include section head's own leave applications
            if ($user->employee) {
                $teamEmployeeIds[] = $user->employee->id;
            }
            
            if (!empty($teamEmployeeIds)) {
                $query->whereIn('employee_id', $teamEmployeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('manager')) {
            // Managers see their team's leave applications
            $teamEmployeeIds = Employee::where('manager_id', $user->id)->pluck('id');
            $query->whereIn('employee_id', $teamEmployeeIds);
        }
        // hr_admin, super_admin, and admin see all leave applications

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('approval_level')) {
            $query->where('approval_level', $request->approval_level);
        }

        $leaves = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($leaves);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'document_path' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $validated['total_days'] = $startDate->diffInDays($endDate) + 1;
        $validated['status'] = 'pending';
        $validated['approval_level'] = 'pending';

        // Check leave balance
        $balance = EmployeeLeaveBalance::where('employee_id', $validated['employee_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', $startDate->year)
            ->first();

        if (!$balance || $balance->remaining_days < $validated['total_days']) {
            return response()->json(['message' => 'Insufficient leave balance'], 400);
        }

        $leaveApplication = LeaveApplication::create($validated);

        // Create notification for section head/manager
        $employee = Employee::with('user')->find($validated['employee_id']);
        if ($employee && $employee->manager_id) {
            Notification::create([
                'user_id' => $employee->manager_id,
                'type' => 'leave_request',
                'title' => 'New Leave Request',
                'message' => "{$employee->full_name} has submitted a leave request for {$validated['total_days']} day(s)",
                'action_url' => '/leaves',
                'data' => [
                    'leave_application_id' => $leaveApplication->id,
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->full_name,
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'total_days' => $validated['total_days'],
                ],
                'priority' => 'normal',
                'is_read' => false,
            ]);
        }

        return response()->json($leaveApplication->load(['employee.user', 'employee.department', 'leaveType']), 201);
    }

    public function show(LeaveApplication $leaveApplication)
    {
        return response()->json($leaveApplication->load(['employee.user', 'employee.department', 'leaveType', 'firstApprover', 'finalApprover']));
    }

    public function approve(Request $request, LeaveApplication $leaveApplication)
    {
        $user = $request->user();
        
        // Cannot approve if already fully approved or rejected
        if ($leaveApplication->status === 'approved' || $leaveApplication->status === 'rejected') {
            return response()->json(['message' => 'Leave application already processed'], 400);
        }

        // Section Head cannot approve their own leave
        if ($user->hasRole('section_head') && $user->employee && $user->employee->id === $leaveApplication->employee_id) {
            return response()->json(['message' => 'You cannot approve your own leave application'], 403);
        }

        $validated = $request->validate([
            'approval_remarks' => 'nullable|string',
        ]);

        $employee = $leaveApplication->employee;
        $isSectionEmployee = $employee && $employee->department_id !== null;
        
        // Section Head: First level approval
        if ($user->hasRole('section_head') && $leaveApplication->approval_level === 'pending') {
            // Verify section head can approve this leave
            if (!$user->employee || $user->employee->department_id !== $employee->department_id) {
                return response()->json(['message' => 'You can only approve leaves from your section'], 403);
            }

            $leaveApplication->update([
                'approval_level' => 'first_approved',
                'first_approved_by' => $user->id,
                'first_approved_at' => now(),
                'first_approval_remarks' => $validated['approval_remarks'] ?? null,
            ]);

            return response()->json([
                'message' => 'Leave approved at first level. Pending admin approval.',
                'leave' => $leaveApplication->load(['employee.user', 'employee.department', 'leaveType', 'firstApprover'])
            ]);
        }

        // Admin/HR: Final approval
        if (($user->hasRole('admin') || $user->hasRole('hr_admin') || $user->hasRole('super_admin'))) {
            // Check if the employee is a section head
            $isEmployeeASectionHead = $employee->user && $employee->user->hasRole('section_head');
            
            // For regular section employees: Must have first approval from section head
            // For section heads' own leave: Can be approved directly by admin
            if ($isSectionEmployee && !$isEmployeeASectionHead && $leaveApplication->approval_level !== 'first_approved') {
                return response()->json(['message' => 'This leave requires section head approval first'], 400);
            }

            $leaveApplication->update([
                'status' => 'approved',
                'approval_level' => 'final_approved',
                'final_approved_by' => $user->id,
                'final_approved_at' => now(),
                'final_approval_remarks' => $validated['approval_remarks'] ?? null,
            ]);

            // Update leave balance
            $balance = EmployeeLeaveBalance::where('employee_id', $leaveApplication->employee_id)
                ->where('leave_type_id', $leaveApplication->leave_type_id)
                ->where('year', Carbon::parse($leaveApplication->start_date)->year)
                ->first();

            if ($balance) {
                $balance->used_days += $leaveApplication->total_days;
                $balance->remaining_days -= $leaveApplication->total_days;
                $balance->save();
            }

            return response()->json([
                'message' => 'Leave approved successfully',
                'leave' => $leaveApplication->load(['employee.user', 'employee.department', 'leaveType', 'firstApprover', 'finalApprover'])
            ]);
        }

        return response()->json(['message' => 'Unauthorized to approve this leave'], 403);
    }

    public function reject(Request $request, LeaveApplication $leaveApplication)
    {
        $user = $request->user();
        
        if ($leaveApplication->status === 'approved' || $leaveApplication->status === 'rejected') {
            return response()->json(['message' => 'Leave application already processed'], 400);
        }

        // Section Head cannot reject their own leave
        if ($user->hasRole('section_head') && $user->employee && $user->employee->id === $leaveApplication->employee_id) {
            return response()->json(['message' => 'You cannot reject your own leave application'], 403);
        }

        $validated = $request->validate([
            'approval_remarks' => 'required|string',
        ]);

        $employee = $leaveApplication->employee;
        $remarks = $validated['approval_remarks'];

        // Section Head or Admin can reject
        if ($user->hasRole('section_head')) {
            // Verify section head can reject this leave
            if (!$user->employee || $user->employee->department_id !== $employee->department_id) {
                return response()->json(['message' => 'You can only reject leaves from your section'], 403);
            }

            $leaveApplication->update([
                'status' => 'rejected',
                'approval_level' => 'rejected',
                'first_approved_by' => $user->id,
                'first_approved_at' => now(),
                'first_approval_remarks' => $remarks,
            ]);
        } elseif ($user->hasRole('admin') || $user->hasRole('hr_admin') || $user->hasRole('super_admin')) {
            $leaveApplication->update([
                'status' => 'rejected',
                'approval_level' => 'rejected',
                'final_approved_by' => $user->id,
                'final_approved_at' => now(),
                'final_approval_remarks' => $remarks,
            ]);
        } else {
            return response()->json(['message' => 'Unauthorized to reject this leave'], 403);
        }

        return response()->json($leaveApplication->load(['employee.user', 'employee.department', 'leaveType', 'firstApprover', 'finalApprover']));
    }

    public function cancel(LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->status === 'approved' && $leaveApplication->approval_level === 'final_approved') {
            // Restore leave balance only if finally approved
            $balance = EmployeeLeaveBalance::where('employee_id', $leaveApplication->employee_id)
                ->where('leave_type_id', $leaveApplication->leave_type_id)
                ->where('year', Carbon::parse($leaveApplication->start_date)->year)
                ->first();

            if ($balance) {
                $balance->used_days -= $leaveApplication->total_days;
                $balance->remaining_days += $leaveApplication->total_days;
                $balance->save();
            }
        }

        $leaveApplication->update([
            'status' => 'cancelled',
            'approval_level' => 'cancelled'
        ]);

        return response()->json($leaveApplication);
    }
}
