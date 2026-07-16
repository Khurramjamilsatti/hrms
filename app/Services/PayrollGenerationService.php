<?php

namespace App\Services;

use App\Models\AdvanceDeduction;
use App\Models\AdvanceRequest;
use App\Models\Attendance;
use App\Models\Bonus;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\LeaveApplication;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\OvertimeRequest;
use App\Models\Payroll;
use App\Models\Timesheet;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollGenerationService
{
    /**
     * Generate monthly payroll for all active employees, syncing attendance, leaves, and timesheets.
     *
     * @return array{count:int, payrolls:array<int, Payroll>}
     */
    public function generate(int $month, int $year, int $processedBy, bool $regenerateDrafts = false): array
    {
        $existing = Payroll::where('month', $month)->where('year', $year);

        if ($existing->exists()) {
            if (!$regenerateDrafts) {
                throw new \RuntimeException('Payroll for this month already exists');
            }

            $nonDraft = (clone $existing)->where('status', '!=', 'draft')->exists();
            if ($nonDraft) {
                throw new \RuntimeException('Cannot regenerate: some payrolls for this month are already processed or paid');
            }

            (clone $existing)->where('status', 'draft')->delete();
        }

        $employees = Employee::where('employment_status', 'active')
            ->with(['salaries.components.salaryComponent'])
            ->get();

        $payrolls = [];
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                $salary = $this->resolveSalary($employee, $month, $year);
                if (!$salary) {
                    continue;
                }

                $calc = $this->calculateEmployeePayroll($employee, $salary, $month, $year);

                $payroll = Payroll::create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                    'basic_salary' => $calc['basic_salary'],
                    'total_earnings' => $calc['total_earnings'],
                    'total_deductions' => $calc['total_deductions'],
                    'absent_deduction' => $calc['absent_deduction'],
                    'unpaid_leave_deduction' => $calc['unpaid_leave_deduction'],
                    'overtime_amount' => $calc['overtime_amount'],
                    'bonus_amount' => $calc['bonus_amount'],
                    'sunday_allowance' => $calc['sunday_allowance'],
                    'holiday_allowance' => $calc['holiday_allowance'],
                    'net_salary' => $calc['net_salary'],
                    'working_days' => $calc['working_days'],
                    'present_days' => $calc['present_days'],
                    'absent_days' => $calc['absent_days'],
                    'leave_days' => $calc['leave_days'],
                    'unpaid_leave_days' => $calc['unpaid_leave_days'],
                    'half_days' => $calc['half_days'],
                    'overtime_hours' => $calc['overtime_hours'],
                    'timesheet_hours' => $calc['timesheet_hours'],
                    'status' => 'draft',
                    'processed_by' => $processedBy,
                    'remarks' => $calc['remarks'],
                ]);

                foreach ($salary->components as $component) {
                    if (!$component->salaryComponent) {
                        continue;
                    }
                    $payroll->details()->create([
                        'salary_component_id' => $component->salary_component_id,
                        'amount' => $component->amount,
                    ]);
                }

                $this->applyAdvanceDeductions($payroll, $calc['advance_requests']);
                $this->applyLoanDeductions(
                    $payroll,
                    $calc['active_loans'],
                    $processedBy,
                    "{$monthNames[$month]} {$year}"
                );

                $payrolls[] = $payroll;
            }

            DB::commit();

            return [
                'count' => count($payrolls),
                'payrolls' => $payrolls,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function calculateEmployeePayroll(Employee $employee, EmployeeSalary $salary, int $month, int $year): array
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->copy()->endOfMonth();
        $workingDays = (int) config('app.working_days_per_month', 22);
        $basicSalary = (float) $salary->basic_salary;
        $perDaySalary = $workingDays > 0 ? $basicSalary / $workingDays : 0;
        $hourlyRate = $workingDays > 0 ? $basicSalary / ($workingDays * 8) : 0;

        $attendanceSummary = $this->summarizeAttendance($employee->id, $month, $year);
        $leaveSummary = $this->summarizeLeaves($employee->id, $periodStart, $periodEnd);
        $timesheetSummary = $this->summarizeTimesheets($employee->id, $month, $year);
        $overtimeHours = $this->resolveOvertimeHours(
            $employee->id,
            $month,
            $year,
            $attendanceSummary['overtime_hours'],
            $timesheetSummary['overtime_hours']
        );

        // Paid leave: prefer leave apps; fall back to attendance on_leave when apps are missing
        $paidLeaveDays = $leaveSummary['paid_days'];
        $unpaidLeaveDays = $leaveSummary['unpaid_days'];
        $leaveDays = $paidLeaveDays + $unpaidLeaveDays;
        if ($leaveDays <= 0) {
            $leaveDays = $attendanceSummary['leave_days'];
        }

        // Half-days count as 0.5 absence for pay; unpaid leave is deducted separately
        $halfDays = $attendanceSummary['half_days'];
        $wholeAbsentDays = $attendanceSummary['absent_days'];
        $absentDaysForPay = $wholeAbsentDays + ($halfDays * 0.5);

        $absentDeduction = round($perDaySalary * $absentDaysForPay, 2);
        $unpaidLeaveDeduction = round($perDaySalary * $unpaidLeaveDays, 2);
        $overtimeAmount = round($overtimeHours * $hourlyRate * 1.5, 2);
        $sundayAllowance = round($attendanceSummary['sunday_allowance'], 2);
        $holidayAllowance = round($attendanceSummary['holiday_allowance'], 2);

        $bonusAmount = (float) Bonus::where('employee_id', $employee->id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'approved')
            ->sum('amount');

        $totalEarnings = $basicSalary;
        $componentDeductions = 0.0;

        if (!$salary->relationLoaded('components')) {
            $salary->load('components.salaryComponent');
        }

        foreach ($salary->components as $component) {
            $type = $component->salaryComponent?->type;
            if ($type === 'earning') {
                $totalEarnings += (float) $component->amount;
            } elseif ($type === 'deduction') {
                $componentDeductions += (float) $component->amount;
            }
        }

        $totalEarnings += $sundayAllowance + $holidayAllowance;

        $activeLoans = Loan::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->where('balance_amount', '>', 0)
            ->get();

        $loanDeduction = (float) $activeLoans->sum('installment_amount');

        $advanceRequests = AdvanceRequest::where('employee_id', $employee->id)
            ->where('status', 'paid')
            ->where('balance_amount', '>', 0)
            ->get();

        $advanceDeduction = 0.0;
        foreach ($advanceRequests as $advance) {
            $installment = $advance->installment_amount
                ?? ($advance->balance_amount / max($advance->installments ?? 1, 1));
            $advanceDeduction += min((float) $installment, (float) $advance->balance_amount);
        }

        $totalDeductions = $absentDeduction
            + $unpaidLeaveDeduction
            + $componentDeductions
            + $loanDeduction
            + $advanceDeduction;

        $netSalary = round(
            $totalEarnings - $totalDeductions + $overtimeAmount + $bonusAmount,
            2
        );

        $remarks = sprintf(
            'Synced: attendance (P:%s A:%s H:%s L:%s), leaves (paid:%s unpaid:%s), timesheets (%.2fh), OT %.2fh',
            $attendanceSummary['present_days'],
            $attendanceSummary['absent_days'],
            $halfDays,
            $attendanceSummary['leave_days'],
            $paidLeaveDays,
            $unpaidLeaveDays,
            $timesheetSummary['hours'],
            $overtimeHours
        );

        return [
            'basic_salary' => $basicSalary,
            'total_earnings' => round($totalEarnings, 2),
            'total_deductions' => round($totalDeductions, 2),
            'absent_deduction' => $absentDeduction,
            'unpaid_leave_deduction' => $unpaidLeaveDeduction,
            'overtime_amount' => $overtimeAmount,
            'bonus_amount' => $bonusAmount,
            'sunday_allowance' => $sundayAllowance,
            'holiday_allowance' => $holidayAllowance,
            'net_salary' => $netSalary,
            'working_days' => $workingDays,
            'present_days' => $attendanceSummary['present_days'],
            'absent_days' => $wholeAbsentDays,
            'leave_days' => $leaveDays,
            'unpaid_leave_days' => $unpaidLeaveDays,
            'half_days' => $halfDays,
            'overtime_hours' => round($overtimeHours, 2),
            'timesheet_hours' => round($timesheetSummary['hours'], 2),
            'remarks' => $remarks,
            'active_loans' => $activeLoans,
            'advance_requests' => $advanceRequests,
        ];
    }

    protected function resolveSalary(Employee $employee, int $month, int $year): ?EmployeeSalary
    {
        $effectiveDate = Carbon::create($year, $month, 1);

        $query = $employee->salaries()
            ->with('components.salaryComponent')
            ->where('effective_from', '<=', $effectiveDate)
            ->where(function ($q) use ($effectiveDate) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $effectiveDate);
            })
            ->orderByDesc('effective_from');

        return $query->first();
    }

    protected function summarizeAttendance(int $employeeId, int $month, int $year): array
    {
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        // Deduplicate by date in case of historical duplicate rows
        $byDate = $attendances->groupBy(fn ($a) => Carbon::parse($a->date)->toDateString())
            ->map(fn (Collection $rows) => $rows->sortByDesc('id')->first());

        $present = 0;
        $absent = 0;
        $leave = 0;
        $half = 0;
        $ot = 0.0;
        $sunday = 0.0;
        $holiday = 0.0;

        foreach ($byDate as $row) {
            match ($row->status) {
                'present', 'late' => $present++,
                'absent' => $absent++,
                'on_leave' => $leave++,
                'half_day' => $half++,
                default => null,
            };
            $ot += (float) ($row->overtime_hours ?? 0);
            $sunday += (float) ($row->sunday_allowance ?? 0);
            $holiday += (float) ($row->holiday_allowance ?? 0);
        }

        return [
            'present_days' => $present,
            'absent_days' => $absent,
            'leave_days' => $leave,
            'half_days' => $half,
            'overtime_hours' => $ot,
            'sunday_allowance' => $sunday,
            'holiday_allowance' => $holiday,
        ];
    }

    protected function summarizeLeaves(int $employeeId, Carbon $periodStart, Carbon $periodEnd): array
    {
        $leaves = LeaveApplication::with('leaveType')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $periodEnd)
            ->whereDate('end_date', '>=', $periodStart)
            ->get();

        $paid = 0.0;
        $unpaid = 0.0;

        foreach ($leaves as $leave) {
            $overlapStart = Carbon::parse($leave->start_date)->max($periodStart)->startOfDay();
            $overlapEnd = Carbon::parse($leave->end_date)->min($periodEnd)->startOfDay();
            $days = $this->countWeekdays($overlapStart, $overlapEnd);

            if ($leave->leaveType && !$leave->leaveType->is_paid) {
                $unpaid += $days;
            } else {
                $paid += $days;
            }
        }

        return [
            'paid_days' => $paid,
            'unpaid_days' => $unpaid,
        ];
    }

    protected function summarizeTimesheets(int $employeeId, int $month, int $year): array
    {
        $timesheets = Timesheet::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $totalMinutes = (float) $timesheets->sum('hours_worked');
        $hours = $totalMinutes / 60;

        // Daily excess over 8h contributes as timesheet-based OT
        $otHours = 0.0;
        foreach ($timesheets->groupBy(fn ($t) => Carbon::parse($t->date)->toDateString()) as $dayRows) {
            $dayHours = ((float) $dayRows->sum('hours_worked')) / 60;
            $otHours += max(0, $dayHours - 8);
        }

        return [
            'hours' => $hours,
            'overtime_hours' => $otHours,
        ];
    }

    protected function resolveOvertimeHours(
        int $employeeId,
        int $month,
        int $year,
        float $attendanceOt,
        float $timesheetOt
    ): float {
        $approvedOt = (float) OvertimeRequest::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('hours');

        if ($approvedOt > 0) {
            return $approvedOt;
        }

        return max($attendanceOt, $timesheetOt);
    }

    protected function countWeekdays(Carbon $start, Carbon $end): float
    {
        if ($start->gt($end)) {
            return 0;
        }

        $days = 0;
        foreach (CarbonPeriod::create($start, $end) as $date) {
            if (!$date->isWeekend()) {
                $days++;
            }
        }

        return (float) $days;
    }

    protected function applyAdvanceDeductions(Payroll $payroll, Collection $advanceRequests): void
    {
        $installmentNumber = 1;
        foreach ($advanceRequests as $advance) {
            $deductionAmount = min(
                (float) ($advance->installment_amount
                    ?? ($advance->balance_amount / max($advance->installments ?? 1, 1))),
                (float) $advance->balance_amount
            );

            AdvanceDeduction::create([
                'advance_request_id' => $advance->id,
                'payroll_id' => $payroll->id,
                'deduction_date' => now(),
                'installment_number' => $installmentNumber++,
                'deduction_amount' => $deductionAmount,
                'balance_after_deduction' => $advance->balance_amount - $deductionAmount,
                'status' => 'deducted',
            ]);

            $newBalance = $advance->balance_amount - $deductionAmount;
            $advance->update([
                'balance_amount' => $newBalance,
                'deducted_amount' => ($advance->deducted_amount ?? 0) + $deductionAmount,
            ]);
        }
    }

    protected function applyLoanDeductions(Payroll $payroll, Collection $activeLoans, int $processedBy, string $periodLabel): void
    {
        foreach ($activeLoans as $loan) {
            $amount = min((float) $loan->installment_amount, (float) $loan->balance_amount);

            LoanPayment::create([
                'loan_id' => $loan->id,
                'payment_date' => Carbon::create($payroll->year, $payroll->month, 28),
                'amount' => $amount,
                'principal_amount' => $amount,
                'interest_amount' => 0,
                'payment_method' => 'salary_deduction',
                'processed_by' => $processedBy,
                'remarks' => "Payroll deduction for {$periodLabel}",
            ]);

            $newBalance = max(0, (float) $loan->balance_amount - $amount);
            $loan->update([
                'balance_amount' => $newBalance,
                'status' => $newBalance <= 0 ? 'completed' : 'active',
            ]);
        }
    }

    /**
     * Mark weekdays in an approved leave range as on_leave attendance.
     */
    public function syncLeaveToAttendance(LeaveApplication $leave): void
    {
        $leave->loadMissing('leaveType');
        $start = Carbon::parse($leave->start_date)->startOfDay();
        $end = Carbon::parse($leave->end_date)->startOfDay();
        $label = $leave->leaveType?->name ?? 'Leave';

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($date->isWeekend()) {
                continue;
            }

            $existing = Attendance::where('employee_id', $leave->employee_id)
                ->whereDate('date', $date->toDateString())
                ->orderByDesc('id')
                ->first();

            if ($existing) {
                $existing->update([
                    'status' => 'on_leave',
                    'remarks' => trim(($existing->remarks ? $existing->remarks.' | ' : '')."Approved leave: {$label}"),
                ]);
            } else {
                Attendance::create([
                    'employee_id' => $leave->employee_id,
                    'date' => $date->toDateString(),
                    'status' => 'on_leave',
                    'working_hours' => 0,
                    'overtime_hours' => 0,
                    'remarks' => "Approved leave: {$label}",
                ]);
            }
        }
    }

    /**
     * Remove on_leave markers created for a cancelled leave (weekdays in range).
     */
    public function unsyncLeaveFromAttendance(LeaveApplication $leave): void
    {
        $start = Carbon::parse($leave->start_date)->startOfDay();
        $end = Carbon::parse($leave->end_date)->startOfDay();

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($date->isWeekend()) {
                continue;
            }

            $rows = Attendance::where('employee_id', $leave->employee_id)
                ->whereDate('date', $date->toDateString())
                ->where('status', 'on_leave')
                ->get();

            foreach ($rows as $row) {
                if ($row->check_in || $row->check_out) {
                    $row->update(['status' => 'present']);
                } else {
                    $row->delete();
                }
            }
        }
    }
}
