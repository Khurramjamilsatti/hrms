<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEmployeeResource;
use App\Http\Controllers\Controller;
use App\Models\AdvanceRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryAdvanceController extends Controller
{
    use AuthorizesEmployeeResource;

    public function index(Request $request)
    {
        $user = $request->user();
        $query = AdvanceRequest::with(['employee.user', 'employee.department', 'approver'])
            ->whereIn('advance_type', ['salary', 'emergency_salary', 'festival']);

        $this->scopeToAccessibleEmployees($query, $request, 'salary_advances');

        // Filter by employee
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'ilike', "%{$search}%")
                                ->orWhere('name', 'ilike', "%{$search}%");
                  });
            })->orWhere('request_number', 'ilike', "%{$search}%");
        }

        $advances = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($advances);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'advance_type' => 'required|in:salary,emergency_salary,festival',
            'amount' => 'required|numeric|min:0',
            'required_date' => 'required|date',
            'purpose' => 'required|string',
            'installments' => 'required|integer|min:1|max:12',
        ]);

        // Get employee
        if (isset($validated['employee_id'])) {
            $this->assertCanAccessEmployeeRecord($request, (int) $validated['employee_id'], 'salary_advances');
            $employee = Employee::find($validated['employee_id']);
            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }
        } else {
            $employee = $request->user()->employee;
            if (!$employee) {
                return response()->json(['message' => 'Employee record not found'], 404);
            }
            $validated['employee_id'] = $employee->id;
        }

        // Generate request number
        $lastRequest = AdvanceRequest::orderBy('id', 'desc')->first();
        $requestNumber = 'ADV' . date('Y') . str_pad(($lastRequest ? $lastRequest->id + 1 : 1), 5, '0', STR_PAD_LEFT);

        $validated['request_number'] = $requestNumber;
        $validated['status'] = 'pending';
        $validated['balance_amount'] = $validated['amount'];
        $validated['installment_amount'] = round($validated['amount'] / $validated['installments'], 2);

        $advance = AdvanceRequest::create($validated);

        return response()->json([
            'message' => 'Salary advance request created successfully',
            'advance' => $advance->load(['employee.user', 'approver'])
        ], 201);
    }

    public function show(Request $request, AdvanceRequest $salaryAdvance)
    {
        $this->assertCanAccessEmployeeRecord($request, $salaryAdvance->employee_id, 'salary_advances');

        return response()->json($salaryAdvance->load([
            'employee.user',
            'employee.department',
            'approver'
        ]));
    }

    public function update(Request $request, AdvanceRequest $salaryAdvance)
    {
        if ($salaryAdvance->status !== 'pending') {
            return response()->json(['message' => 'Cannot update advance request that is not pending'], 400);
        }

        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'required_date' => 'sometimes|date',
            'purpose' => 'sometimes|string',
            'installments' => 'sometimes|integer|min:1|max:12',
        ]);

        // Recalculate if amount or installments changed
        if (isset($validated['amount']) || isset($validated['installments'])) {
            $amount = $validated['amount'] ?? $salaryAdvance->amount;
            $installments = $validated['installments'] ?? $salaryAdvance->installments;
            
            $validated['installment_amount'] = round($amount / $installments, 2);
            $validated['balance_amount'] = $amount;
        }

        $salaryAdvance->update($validated);

        return response()->json([
            'message' => 'Advance request updated successfully',
            'advance' => $salaryAdvance->load(['employee.user', 'approver'])
        ]);
    }

    public function approve(Request $request, AdvanceRequest $salaryAdvance)
    {
        if ($salaryAdvance->status !== 'pending') {
            return response()->json(['message' => 'Advance request is not pending'], 400);
        }

        $validated = $request->validate([
            'remarks' => 'nullable|string',
        ]);

        $salaryAdvance->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['remarks'] ?? null,
        ]);

        return response()->json([
            'message' => 'Advance request approved successfully',
            'advance' => $salaryAdvance->load(['employee.user', 'approver'])
        ]);
    }

    public function reject(Request $request, AdvanceRequest $salaryAdvance)
    {
        if ($salaryAdvance->status !== 'pending') {
            return response()->json(['message' => 'Advance request is not pending'], 400);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $salaryAdvance->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return response()->json([
            'message' => 'Advance request rejected',
            'advance' => $salaryAdvance->load(['employee.user', 'approver'])
        ]);
    }

    public function disburse(Request $request, AdvanceRequest $salaryAdvance)
    {
        if ($salaryAdvance->status !== 'approved') {
            return response()->json(['message' => 'Advance request must be approved first'], 400);
        }

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,cheque',
            'payment_reference' => 'nullable|string',
        ]);

        $salaryAdvance->update([
            'status' => 'paid',
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'first_deduction_date' => now()->addMonth()->startOfMonth(),
        ]);

        return response()->json([
            'message' => 'Advance disbursed successfully',
            'advance' => $salaryAdvance->load(['employee.user', 'approver'])
        ]);
    }

    public function destroy(AdvanceRequest $salaryAdvance)
    {
        if ($salaryAdvance->status !== 'pending') {
            return response()->json(['message' => 'Can only delete pending advance requests'], 400);
        }

        $salaryAdvance->delete();

        return response()->json(['message' => 'Advance request deleted successfully']);
    }
}
