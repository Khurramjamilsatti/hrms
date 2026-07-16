<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEmployeeResource;
use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\Notification;
use App\Services\PayrollGenerationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveApplicationController extends Controller
{
    use AuthorizesEmployeeResource;

    public function __construct(protected PayrollGenerationService $payrollService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = LeaveApplication::with(['employee.user', 'employee.department', 'leaveType', 'firstApprover', 'finalApprover']);

        $this->scopeToAccessibleEmployees($query, $request, 'leaves');

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
            'employee_id' => 'nullable|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'document_path' => 'nullable|string',
        ]);

        $user = $request->user()->loadMissing('employee');
        $canApplyForOthers = $user->hasPermission('leaves.manage');

        if (empty($validated['employee_id'])) {
            if (!$user->employee) {
                return response()->json([
                    'message' => 'No employee profile is linked to your account. Please select an employee.',
                ], 422);
            }
            $validated['employee_id'] = $user->employee->id;
        } elseif (!$canApplyForOthers && $user->employee?->id != $validated['employee_id']) {
            return response()->json(['message' => 'You can only apply leave for yourself'], 403);
        }

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

    public function show(Request $request, LeaveApplication $leaveApplication)
    {
        $this->assertCanAccessEmployeeRecord($request, $leaveApplication->employee_id, 'leaves');

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
        if (!$employee) {
            return response()->json(['message' => 'Employee record for this leave was not found'], 404);
        }

        // Section Head: First level approval for same department
        if ($user->hasRole('section_head') && $leaveApplication->approval_level === 'pending') {
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

        // Manager: First level approval for direct reports
        if ($user->hasRole('manager') && $leaveApplication->approval_level === 'pending') {
            if ($employee->manager_id !== $user->id) {
                return response()->json(['message' => 'You can only approve leaves for your team'], 403);
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

        // Admin/HR/Super Admin: Final approval (can finalize directly)
        if ($user->hasRole('admin') || $user->hasRole('hr_admin') || $user->hasRole('super_admin')) {
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

            // Sync approved leave days into attendance for payroll
            $this->payrollService->syncLeaveToAttendance($leaveApplication);

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

        if (!$employee) {
            return response()->json(['message' => 'Employee record for this leave was not found'], 404);
        }

        // Section Head / Manager / Admin can reject
        if ($user->hasRole('section_head')) {
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
        } elseif ($user->hasRole('manager')) {
            if ($employee->manager_id !== $user->id) {
                return response()->json(['message' => 'You can only reject leaves for your team'], 403);
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

    public function cancel(Request $request, LeaveApplication $leaveApplication)
    {
        $user = $request->user()->loadMissing('employee');
        $isOwner = $user->employee && $user->employee->id === $leaveApplication->employee_id;
        $isPrivileged = in_array($user->role, ['admin', 'hr_admin', 'super_admin'], true);

        if (!$isOwner && !$isPrivileged) {
            return response()->json(['message' => 'You can only cancel your own leave applications'], 403);
        }

        if (!in_array($leaveApplication->status, ['pending', 'approved'], true)) {
            return response()->json(['message' => 'Only pending or approved leave can be cancelled'], 400);
        }

        $wasFinallyApproved = $leaveApplication->status === 'approved'
            && $leaveApplication->approval_level === 'final_approved';

        if ($wasFinallyApproved) {
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

            $this->payrollService->unsyncLeaveFromAttendance($leaveApplication);
        }

        $leaveApplication->update([
            'status' => 'cancelled',
            'approval_level' => 'cancelled'
        ]);

        return response()->json($leaveApplication);
    }
}
