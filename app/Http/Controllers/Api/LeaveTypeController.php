<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use App\Services\ShortLeaveSyncService;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveType::query();

        if (!$request->boolean('all')) {
            $query->where('is_active', true)
                ->whereNotIn('name', array_values(ShortLeaveSyncService::TYPE_NAMES));
        }

        $leaveTypes = $query->orderBy('name')->get();

        return response()->json($leaveTypes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days_per_year' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'is_carry_forward' => 'boolean',
            'max_carry_forward_days' => 'nullable|integer|min:0',
            'requires_document' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $leaveType = LeaveType::create($validated);

        return response()->json($leaveType, 201);
    }

    public function show(LeaveType $leaveType)
    {
        return response()->json($leaveType);
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'days_per_year' => 'sometimes|integer|min:0',
            'is_paid' => 'boolean',
            'is_carry_forward' => 'boolean',
            'max_carry_forward_days' => 'nullable|integer|min:0',
            'requires_document' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $leaveType->update($validated);

        return response()->json($leaveType);
    }

    public function destroy(LeaveType $leaveType)
    {
        if ($leaveType->leaveApplications()->exists()) {
            return response()->json([
                'message' => 'Cannot delete leave type with existing applications',
            ], 400);
        }

        $leaveType->leaveBalances()->delete();
        $leaveType->delete();

        return response()->json(['message' => 'Leave type deleted successfully']);
    }

    public function balances(Request $request)
    {
        $query = EmployeeLeaveBalance::with(['employee.user', 'leaveType']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        } else {
            $query->where('year', now()->year);
        }

        $balances = $query->orderBy('employee_id')->paginate($request->per_page ?? 20);

        return response()->json($balances);
    }

    public function allocateBalance(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'year' => 'required|integer|min:2000|max:2100',
            'total_days' => 'required|numeric|min:0',
            'used_days' => 'nullable|numeric|min:0',
        ]);

        $usedDays = $validated['used_days'] ?? 0;
        $remainingDays = $validated['total_days'] - $usedDays;

        if ($remainingDays < 0) {
            return response()->json(['message' => 'Used days cannot exceed total days'], 400);
        }

        $balance = EmployeeLeaveBalance::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'year' => $validated['year'],
            ],
            [
                'total_days' => $validated['total_days'],
                'used_days' => $usedDays,
                'remaining_days' => $remainingDays,
            ]
        );

        return response()->json([
            'message' => 'Leave balance allocated successfully',
            'balance' => $balance->load(['employee.user', 'leaveType']),
        ], 201);
    }
}
