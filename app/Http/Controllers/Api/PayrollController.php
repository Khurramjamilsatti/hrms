<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Bonus;
use App\Models\OvertimeRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Payroll::with(['employee.user', 'processedBy']);

        // Role-based data access control
        if ($user->hasRole('employee')) {
            // Employees can only see their own payroll
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('manager')) {
            // Managers cannot view payroll - restricted
            return response()->json(['message' => 'Unauthorized access to payroll'], 403);
        } elseif ($user->hasRole('section_head')) {
            // Section heads cannot view payroll - restricted
            return response()->json(['message' => 'Unauthorized access to payroll'], 403);
        }
        // hr_admin, super_admin, and admin can view all payroll

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('month')) {
            $query->where('month', $request->month);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
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
            });
        }

        $payrolls = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($payrolls);
    }

    public function generateMonthlyPayroll(Request $request)
    {
        $user = $request->user();
        
        // Only hr_admin, super_admin, and admin can generate payroll
        if (!in_array($user->role, ['hr_admin', 'super_admin', 'admin'])) {
            return response()->json(['message' => 'Unauthorized to generate payroll'], 403);
        }

        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        $month = $validated['month'];
        $year = $validated['year'];
        
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        // Check if payroll already exists
        $exists = Payroll::where('month', $month)
            ->where('year', $year)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Payroll for this month already exists'], 400);
        }

        $employees = Employee::where('employment_status', 'active')
            ->with('salaries')
            ->get();

        $payrolls = [];

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                $salary = $employee->salaries()
                    ->where('effective_from', '<=', Carbon::create($year, $month, 1))
                    ->where(function ($query) use ($year, $month) {
                        $query->whereNull('effective_to')
                              ->orWhere('effective_to', '>=', Carbon::create($year, $month, 1));
                    })
                    ->first();

                if (!$salary) continue;

                // Get attendance summary
                $attendances = Attendance::where('employee_id', $employee->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();

                $presentDays = $attendances->whereIn('status', ['present', 'late'])->count();
                $absentDays = $attendances->where('status', 'absent')->count();
                $leaveDays = $attendances->where('status', 'on_leave')->count();
                $overtimeHours = $attendances->sum('overtime_hours');

                // Get bonuses
                $bonusAmount = Bonus::where('employee_id', $employee->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->where('status', 'approved')
                    ->sum('amount');

                // Calculate salary components
                $basicSalary = $salary->basic_salary;
                $workingDays = config('app.working_days_per_month', 22);
                $perDaySalary = $basicSalary / $workingDays;
                
                // Deduct for absences
                $absentDeduction = $perDaySalary * $absentDays;
                
                // Overtime calculation (1.5x hourly rate)
                $hourlyRate = $basicSalary / ($workingDays * 8);
                $overtimeAmount = $overtimeHours * $hourlyRate * 1.5;

                // Calculate earnings and deductions from salary components
                $totalEarnings = $basicSalary;
                $totalDeductions = $absentDeduction;

                foreach ($salary->components as $component) {
                    if ($component->salaryComponent->type === 'earning') {
                        $totalEarnings += $component->amount;
                    } else {
                        $totalDeductions += $component->amount;
                    }
                }

                // Add active loan deductions
                $activeLoans = \App\Models\Loan::where('employee_id', $employee->id)
                    ->where('status', 'active')
                    ->where('balance_amount', '>', 0)
                    ->get();
                
                $loanDeduction = 0;
                foreach ($activeLoans as $loan) {
                    $loanDeduction += $loan->installment_amount;
                }
                $totalDeductions += $loanDeduction;

                // Add advance salary deductions (only approved & paid advances with balance)
                $advanceRequests = \App\Models\AdvanceRequest::where('employee_id', $employee->id)
                    ->where('status', 'paid')
                    ->where('balance_amount', '>', 0)
                    ->get();
                
                $advanceDeduction = 0;
                foreach ($advanceRequests as $advance) {
                    $installmentAmount = $advance->installment_amount ?? ($advance->balance_amount / max($advance->installments ?? 1, 1));
                    $advanceDeduction += min($installmentAmount, $advance->balance_amount);
                }
                $totalDeductions += $advanceDeduction;

                $netSalary = $totalEarnings - $totalDeductions + $overtimeAmount + $bonusAmount;

                $payroll = Payroll::create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                    'basic_salary' => $basicSalary,
                    'total_earnings' => $totalEarnings,
                    'total_deductions' => $totalDeductions,
                    'overtime_amount' => $overtimeAmount,
                    'bonus_amount' => $bonusAmount,
                    'net_salary' => $netSalary,
                    'working_days' => $workingDays,
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'leave_days' => $leaveDays,
                    'overtime_hours' => $overtimeHours,
                    'status' => 'draft',
                    'processed_by' => $request->user()->id,
                ]);

                // Create payroll details
                foreach ($salary->components as $component) {
                    $payroll->details()->create([
                        'salary_component_id' => $component->salary_component_id,
                        'amount' => $component->amount,
                    ]);
                }

                // Create advance deduction records and update balance
                $installmentNumber = 1;
                foreach ($advanceRequests as $advance) {
                    $deductionAmount = min(
                        $advance->installment_amount ?? ($advance->balance_amount / max($advance->installments ?? 1, 1)),
                        $advance->balance_amount
                    );
                    
                    // Create deduction record
                    \App\Models\AdvanceDeduction::create([
                        'advance_request_id' => $advance->id,
                        'payroll_id' => $payroll->id,
                        'deduction_date' => now(),
                        'installment_number' => $installmentNumber++,
                        'deduction_amount' => $deductionAmount,
                        'balance_after_deduction' => $advance->balance_amount - $deductionAmount,
                        'status' => 'deducted',
                    ]);
                    
                    // Update advance balance
                    $newBalance = $advance->balance_amount - $deductionAmount;
                    $advance->update([
                        'balance_amount' => $newBalance,
                        'deducted_amount' => ($advance->deducted_amount ?? 0) + $deductionAmount,
                    ]);
                }

                // Update loan payments (for tracking)
                foreach ($activeLoans as $loan) {
                    // Create loan payment record
                    \App\Models\LoanPayment::create([
                        'loan_id' => $loan->id,
                        'payment_date' => Carbon::create($year, $month, 28),
                        'amount' => $loan->installment_amount,
                        'principal_amount' => $loan->installment_amount, // No interest
                        'interest_amount' => 0,
                        'payment_method' => 'salary_deduction',
                        'processed_by' => $request->user()->id,
                        'remarks' => "Payroll deduction for {$monthNames[$month - 1]} {$year}",
                    ]);
                }

                $payrolls[] = $payroll;
            }

            DB::commit();

            return response()->json([
                'message' => 'Payroll generated successfully',
                'count' => count($payrolls),
                'payrolls' => $payrolls,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error generating payroll: ' . $e->getMessage()], 500);
        }
    }

    public function show(Payroll $payroll)
    {
        return response()->json($payroll->load([
            'employee.user',
            'employee.department',
            'details.salaryComponent',
            'processedBy',
        ]));
    }

    public function processPayroll(Payroll $payroll)
    {
        if ($payroll->status !== 'draft') {
            return response()->json(['message' => 'Payroll already processed'], 400);
        }

        $payroll->update(['status' => 'processed']);

        return response()->json($payroll);
    }

    public function markAsPaid(Request $request, Payroll $payroll)
    {
        if ($payroll->status !== 'processed') {
            return response()->json(['message' => 'Payroll must be processed first'], 400);
        }

        $validated = $request->validate([
            'payment_date' => 'required|date',
        ]);

        $payroll->update([
            'status' => 'paid',
            'payment_date' => $validated['payment_date'],
        ]);

        return response()->json($payroll);
    }
}
