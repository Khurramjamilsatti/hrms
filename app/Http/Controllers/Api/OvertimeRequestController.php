<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEmployeeResource;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use Illuminate\Http\Request;

class OvertimeRequestController extends Controller
{
    use AuthorizesEmployeeResource;

    public function index(Request $request)
    {
        $user = $request->user();
        $query = OvertimeRequest::with(['employee.user', 'employee.department', 'firstApprover', 'finalApprover']);

        if ($user->hasRole('employee')) {
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('section_head')) {
            $teamEmployeeIds = Employee::where('manager_id', $user->id)->pluck('id')->toArray();
            if ($user->employee) {
                $teamEmployeeIds[] = $user->employee->id;
            }
            if (!empty($teamEmployeeIds)) {
                $query->whereIn('employee_id', $teamEmployeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('manager')) {
            $teamEmployeeIds = Employee::where('manager_id', $user->id)->pluck('id');
            $query->whereIn('employee_id', $teamEmployeeIds);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_level')) {
            $query->where('approval_level', $request->approval_level);
        }

        $requests = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.5|max:24',
            'reason' => 'required|string',
        ]);

        $user = $request->user()->loadMissing('employee');
        $isPrivileged = in_array($user->role, ['admin', 'hr_admin', 'super_admin', 'manager', 'section_head'], true);

        if (empty($validated['employee_id'])) {
            if (!$user->employee) {
                return response()->json([
                    'message' => 'No employee profile is linked to your account. Please select an employee when submitting overtime.',
                ], 422);
            }
            $validated['employee_id'] = $user->employee->id;
        } elseif (!$isPrivileged && $user->employee?->id != $validated['employee_id']) {
            return response()->json(['message' => 'You can only create overtime requests for yourself'], 403);
        }

        $validated['status'] = 'pending';
        $validated['approval_level'] = 'pending';

        $overtimeRequest = OvertimeRequest::create($validated);

        return response()->json(
            $overtimeRequest->load(['employee.user', 'employee.department']),
            201
        );
    }

    public function show(Request $request, OvertimeRequest $overtimeRequest)
    {
        $this->assertCanAccessEmployeeRecord($request, $overtimeRequest->employee_id);

        return response()->json($overtimeRequest->load([
            'employee.user',
            'employee.department',
            'firstApprover',
            'finalApprover',
        ]));
    }

    public function update(Request $request, OvertimeRequest $overtimeRequest)
    {
        if ($overtimeRequest->status !== 'pending' || $overtimeRequest->approval_level !== 'pending') {
            return response()->json(['message' => 'Cannot update a processed overtime request'], 400);
        }

        $user = $request->user();
        if ($user->hasRole('employee') && $user->employee?->id !== $overtimeRequest->employee_id) {
            return response()->json(['message' => 'You can only update your own overtime requests'], 403);
        }

        $validated = $request->validate([
            'date' => 'sometimes|date',
            'hours' => 'sometimes|numeric|min:0.5|max:24',
            'reason' => 'sometimes|string',
        ]);

        $overtimeRequest->update($validated);

        return response()->json($overtimeRequest->load(['employee.user', 'employee.department']));
    }

    public function destroy(Request $request, OvertimeRequest $overtimeRequest)
    {
        if ($overtimeRequest->status !== 'pending' || $overtimeRequest->approval_level !== 'pending') {
            return response()->json(['message' => 'Cannot delete a processed overtime request'], 400);
        }

        $user = $request->user();
        if ($user->hasRole('employee') && $user->employee?->id !== $overtimeRequest->employee_id) {
            return response()->json(['message' => 'You can only delete your own overtime requests'], 403);
        }

        $overtimeRequest->delete();

        return response()->json(['message' => 'Overtime request deleted successfully']);
    }

    public function approve(Request $request, OvertimeRequest $overtimeRequest)
    {
        $user = $request->user();

        if ($overtimeRequest->status === 'approved' || $overtimeRequest->status === 'rejected') {
            return response()->json(['message' => 'Overtime request already processed'], 400);
        }

        if ($user->hasRole('section_head') && $user->employee && $user->employee->id === $overtimeRequest->employee_id) {
            return response()->json(['message' => 'You cannot approve your own overtime request'], 403);
        }

        $remarks = $request->input('approval_remarks', $request->input('remarks'));

        $employee = $overtimeRequest->employee;
        if (!$employee) {
            return response()->json(['message' => 'Employee record for this overtime request was not found'], 404);
        }

        if ($user->hasRole('section_head') && $overtimeRequest->approval_level === 'pending') {
            if (!$user->employee || $user->employee->department_id !== $employee->department_id) {
                return response()->json(['message' => 'You can only approve overtime from your section'], 403);
            }

            $overtimeRequest->update([
                'approval_level' => 'first_approved',
                'first_approved_by' => $user->id,
                'first_approved_at' => now(),
                'first_approval_remarks' => $remarks,
            ]);

            return response()->json([
                'message' => 'Overtime approved at first level. Pending admin approval.',
                'overtime_request' => $overtimeRequest->load(['employee.user', 'employee.department', 'firstApprover']),
            ]);
        }

        if ($user->hasRole('manager') && $overtimeRequest->approval_level === 'pending') {
            if ($employee->manager_id !== $user->id) {
                return response()->json(['message' => 'You can only approve overtime for your team'], 403);
            }

            $overtimeRequest->update([
                'approval_level' => 'first_approved',
                'first_approved_by' => $user->id,
                'first_approved_at' => now(),
                'first_approval_remarks' => $remarks,
            ]);

            return response()->json([
                'message' => 'Overtime approved at first level. Pending admin approval.',
                'overtime_request' => $overtimeRequest->load(['employee.user', 'employee.department', 'firstApprover']),
            ]);
        }

        if ($user->hasRole('admin') || $user->hasRole('hr_admin') || $user->hasRole('super_admin')) {
            // Admins can finalize directly so OT is usable even without a section head chain.
            $overtimeRequest->update([
                'status' => 'approved',
                'approval_level' => 'final_approved',
                'final_approved_by' => $user->id,
                'final_approved_at' => now(),
                'final_approval_remarks' => $remarks,
            ]);

            return response()->json([
                'message' => 'Overtime request approved successfully',
                'overtime_request' => $overtimeRequest->load(['employee.user', 'employee.department', 'firstApprover', 'finalApprover']),
            ]);
        }

        return response()->json(['message' => 'Unauthorized to approve this overtime request'], 403);
    }

    public function reject(Request $request, OvertimeRequest $overtimeRequest)
    {
        $user = $request->user();

        if ($overtimeRequest->status === 'approved' || $overtimeRequest->status === 'rejected') {
            return response()->json(['message' => 'Overtime request already processed'], 400);
        }

        if ($user->hasRole('section_head') && $user->employee && $user->employee->id === $overtimeRequest->employee_id) {
            return response()->json(['message' => 'You cannot reject your own overtime request'], 403);
        }

        $remarks = $request->input('approval_remarks', $request->input('remarks'));
        if (!$remarks) {
            return response()->json(['message' => 'Rejection remarks are required'], 422);
        }

        $employee = $overtimeRequest->employee;
        if (!$employee) {
            return response()->json(['message' => 'Employee record for this overtime request was not found'], 404);
        }

        if ($user->hasRole('section_head')) {
            if (!$user->employee || $user->employee->department_id !== $employee->department_id) {
                return response()->json(['message' => 'You can only reject overtime from your section'], 403);
            }

            $overtimeRequest->update([
                'status' => 'rejected',
                'approval_level' => 'rejected',
                'first_approved_by' => $user->id,
                'first_approved_at' => now(),
                'first_approval_remarks' => $remarks,
            ]);
        } elseif ($user->hasRole('manager')) {
            if ($employee->manager_id !== $user->id) {
                return response()->json(['message' => 'You can only reject overtime for your team'], 403);
            }

            $overtimeRequest->update([
                'status' => 'rejected',
                'approval_level' => 'rejected',
                'first_approved_by' => $user->id,
                'first_approved_at' => now(),
                'first_approval_remarks' => $remarks,
            ]);
        } elseif ($user->hasRole('admin') || $user->hasRole('hr_admin') || $user->hasRole('super_admin')) {
            $overtimeRequest->update([
                'status' => 'rejected',
                'approval_level' => 'rejected',
                'final_approved_by' => $user->id,
                'final_approved_at' => now(),
                'final_approval_remarks' => $remarks,
            ]);
        } else {
            return response()->json(['message' => 'Unauthorized to reject this overtime request'], 403);
        }

        return response()->json($overtimeRequest->load([
            'employee.user',
            'employee.department',
            'firstApprover',
            'finalApprover',
        ]));
    }
}
