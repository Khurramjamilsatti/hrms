<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEmployeeResource;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    use AuthorizesEmployeeResource;

    public function __construct(protected NotificationService $notifier)
    {
    }

    private function loanNotificationData(Loan $loan): array
    {
        return [
            'loan_id' => $loan->id,
            'loan_number' => $loan->loan_number,
            'amount' => $loan->amount,
            'loan_type' => $loan->loan_type,
            'status' => $loan->status,
        ];
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Loan::with(['employee.user', 'employee.department', 'approver']);

        $this->scopeToAccessibleEmployees($query, $request, 'loans');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('loan_type')) {
            $query->where('loan_type', $request->loan_type);
        }

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
            })->orWhere('loan_number', 'ilike', "%{$search}%");
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($loans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'loan_type' => 'required|in:personal,medical,education,housing,emergency,other',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'installments' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'purpose' => 'required|string',
            'guarantor_name' => 'nullable|string',
            'guarantor_employee_id' => 'nullable|exists:employees,id',
            'guarantor_contact' => 'nullable|string',
        ]);

        // If employee_id provided (admin/manager submitting), use it; otherwise use authenticated user's employee
        if (isset($validated['employee_id'])) {
            $this->assertCanAccessEmployeeRecord($request, (int) $validated['employee_id'], 'loans');
            $employee = \App\Models\Employee::find($validated['employee_id']);
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

        // Generate loan number
        $lastLoan = Loan::orderBy('id', 'desc')->first();
        $loanNumber = 'LN' . date('Y') . str_pad(($lastLoan ? $lastLoan->id + 1 : 1), 5, '0', STR_PAD_LEFT);

        $validated['loan_number'] = $loanNumber;
        $validated['status'] = 'pending';
        
        // Always set interest rate to 0 (interest-free loans)
        $validated['interest_rate'] = 0;
        
        // Calculate installment amount (no interest)
        $validated['installment_amount'] = round($validated['amount'] / $validated['installments'], 2);
        $validated['balance_amount'] = $validated['amount'];

        $loan = Loan::create($validated);

        $employee->loadMissing('user');
        $amountLabel = number_format((float) $loan->amount);

        // Notify approvers (HR/admin)
        $this->notifier->notifyRoles(
            ['hr_admin', 'super_admin', 'admin'],
            'loan_request',
            'New Loan Request',
            "{$employee->full_name} has requested a {$loan->loan_type} loan of {$amountLabel} ({$loan->loan_number})",
            $this->loanNotificationData($loan) + ['employee_name' => $employee->full_name],
            "/loans/{$loan->id}",
            'normal',
            [$request->user()->id]
        );

        // Confirm to the employee
        if ($employee->user_id && $employee->user_id !== $request->user()->id) {
            $this->notifier->notifyUser(
                $employee->user_id,
                'loan_request',
                'Loan Request Submitted',
                "A loan request of {$amountLabel} ({$loan->loan_number}) was submitted on your behalf",
                $this->loanNotificationData($loan),
                "/loans/{$loan->id}"
            );
        }

        return response()->json([
            'message' => 'Loan request created successfully',
            'loan' => $loan->load(['employee.user', 'approver'])
        ], 201);
    }

    public function show(Request $request, Loan $loan)
    {
        $this->assertCanAccessEmployeeRecord($request, $loan->employee_id, 'loans');

        return response()->json($loan->load([
            'employee.user',
            'employee.department',
            'approver',
            'payments.processor'
        ]));
    }

    public function update(Request $request, Loan $loan)
    {
        $this->assertCanAccessEmployeeRecord($request, $loan->employee_id, 'loans');

        $validated = $request->validate([
            'employee_id' => 'sometimes|exists:employees,id',
            'loan_type' => 'sometimes|in:personal,medical,education,housing,emergency,other',
            'amount' => 'sometimes|numeric|min:0',
            'interest_rate' => 'sometimes|numeric|min:0|max:100',
            'installments' => 'sometimes|integer|min:1',
            'start_date' => 'sometimes|date',
            'purpose' => 'sometimes|string',
            'remarks' => 'nullable|string',
        ]);

        // Always set interest rate to 0 (interest-free loans)
        $validated['interest_rate'] = 0;
        
        // Recalculate if amount or installments changed
        if (isset($validated['amount']) || isset($validated['installments'])) {
            $amount = $validated['amount'] ?? $loan->amount;
            $installments = $validated['installments'] ?? $loan->installments;
            
            // Calculate installment amount (no interest)
            $validated['installment_amount'] = round($amount / $installments, 2);
        }

        $loan->update($validated);

        return response()->json([
            'message' => 'Loan updated successfully',
            'loan' => $loan->load(['employee.user', 'approver'])
        ]);
    }

    public function approve(Request $request, Loan $loan)
    {
        $this->assertCanAccessEmployeeRecord($request, $loan->employee_id, 'loans');

        $request->validate([
            'remarks' => 'nullable|string',
        ]);

        $loan->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'remarks' => $request->remarks,
        ]);

        $this->notifier->notifyUser(
            $loan->employee?->user_id,
            'loan_approved',
            'Loan Approved',
            "Your loan request {$loan->loan_number} of " . number_format((float) $loan->amount) . ' has been approved',
            $this->loanNotificationData($loan),
            "/loans/{$loan->id}"
        );

        return response()->json([
            'message' => 'Loan approved successfully',
            'loan' => $loan->load(['employee.user', 'approver'])
        ]);
    }

    public function reject(Request $request, Loan $loan)
    {
        $this->assertCanAccessEmployeeRecord($request, $loan->employee_id, 'loans');

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $loan->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $this->notifier->notifyUser(
            $loan->employee?->user_id,
            'loan_rejected',
            'Loan Request Rejected',
            "Your loan request {$loan->loan_number} was rejected: {$validated['rejection_reason']}",
            $this->loanNotificationData($loan),
            "/loans/{$loan->id}",
            'high'
        );

        return response()->json([
            'message' => 'Loan rejected',
            'loan' => $loan
        ]);
    }

    public function disburse(Request $request, Loan $loan)
    {
        $this->assertCanAccessEmployeeRecord($request, $loan->employee_id, 'loans');

        $validated = $request->validate([
            'disbursed_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        $loan->update([
            'status' => 'active',
            'disbursed_by' => auth()->id(),
            'disbursed_date' => $validated['disbursed_date'],
            'payment_method' => $validated['payment_method'],
        ]);

        $this->notifier->notifyUser(
            $loan->employee?->user_id,
            'loan_disbursed',
            'Loan Disbursed',
            "Your loan {$loan->loan_number} of " . number_format((float) $loan->amount) . ' has been disbursed',
            $this->loanNotificationData($loan),
            "/loans/{$loan->id}"
        );

        return response()->json([
            'message' => 'Loan disbursed successfully',
            'loan' => $loan->load(['employee.user'])
        ]);
    }

    public function addPayment(Request $request, Loan $loan)
    {
        $this->assertCanAccessEmployeeRecord($request, $loan->employee_id, 'loans');

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'principal_amount' => 'required|numeric|min:0',
            'interest_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['processed_by'] = auth()->id();
        $validated['loan_id'] = $loan->id;

        DB::beginTransaction();
        try {
            $payment = LoanPayment::create($validated);

            // Update loan balance
            $loan->total_paid += $validated['amount'];
            $loan->balance_amount  -= $validated['principal_amount'];

            if ($loan->balance_amount <= 0) {
                $loan->status = 'completed';
                $loan->closed_date = now();
                $loan->closed_by = auth()->id();
            }

            $loan->save();

            DB::commit();

            return response()->json([
                'message' => 'Payment recorded successfully',
                'payment' => $payment,
                'loan' => $loan
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to record payment'], 500);
        }
    }

    public function destroy(Request $request, Loan $loan)
    {
        $this->assertCanAccessEmployeeRecord($request, $loan->employee_id, 'loans');

        if (in_array($loan->status, ['active', 'disbursed'])) {
            return response()->json(['message' => 'Cannot delete active loan'], 400);
        }

        $loan->delete();

        return response()->json(['message' => 'Loan deleted successfully']);
    }
}
