<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaveApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $leaveTypes = LeaveType::all();
        $adminUser = User::where('role', 'admin')->first();
        $currentYear = 2026;

        if ($employees->isEmpty() || $leaveTypes->isEmpty()) {
            return;
        }

        // First, create leave balances for all employees for each leave type
        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                EmployeeLeaveBalance::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $currentYear,
                    ],
                    [
                        'total_days' => $leaveType->days_per_year,
                        'used_days' => 0,
                        'remaining_days' => $leaveType->days_per_year,
                    ]
                );
            }
        }

        // Define leave applications with various statuses
        $leaveApplications = [
            // Employee 1 - Approved annual leave (past)
            [
                'employee_id' => $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Annual Leave')->first()->id,
                'start_date' => '2026-01-06',
                'end_date' => '2026-01-10',
                'total_days' => 5,
                'reason' => 'Family vacation planned during winter holidays.',
                'status' => 'approved',
                'approved_by' => $adminUser?->id,
                'approved_at' => '2026-01-03 10:00:00',
                'approval_remarks' => 'Approved. Enjoy your vacation!',
            ],
            // Employee 1 - Approved sick leave (past)
            [
                'employee_id' => $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Sick Leave')->first()->id,
                'start_date' => '2026-01-20',
                'end_date' => '2026-01-21',
                'total_days' => 2,
                'reason' => 'Feeling unwell, need to visit doctor.',
                'status' => 'approved',
                'approved_by' => $adminUser?->id,
                'approved_at' => '2026-01-20 08:30:00',
                'approval_remarks' => 'Get well soon.',
            ],
            // Employee 2 - Pending casual leave (upcoming)
            [
                'employee_id' => $employees->count() > 1 ? $employees[1]->id : $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Casual Leave')->first()->id,
                'start_date' => '2026-02-18',
                'end_date' => '2026-02-19',
                'total_days' => 2,
                'reason' => 'Personal errands to attend to.',
                'status' => 'pending',
            ],
            // Employee 2 - Rejected annual leave
            [
                'employee_id' => $employees->count() > 1 ? $employees[1]->id : $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Annual Leave')->first()->id,
                'start_date' => '2026-02-02',
                'end_date' => '2026-02-06',
                'total_days' => 5,
                'reason' => 'Planning a short trip.',
                'status' => 'rejected',
                'approved_by' => $adminUser?->id,
                'approved_at' => '2026-01-30 14:00:00',
                'approval_remarks' => 'Critical project deadline during this period. Please reschedule.',
            ],
            // Employee 3 - Pending sick leave
            [
                'employee_id' => $employees->count() > 2 ? $employees[2]->id : $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Sick Leave')->first()->id,
                'start_date' => '2026-02-16',
                'end_date' => '2026-02-17',
                'total_days' => 2,
                'reason' => 'Dental surgery appointment scheduled.',
                'status' => 'pending',
            ],
            // Employee 1 - Pending annual leave (future)
            [
                'employee_id' => $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Annual Leave')->first()->id,
                'start_date' => '2026-03-02',
                'end_date' => '2026-03-06',
                'total_days' => 5,
                'reason' => 'Spring break vacation with family.',
                'status' => 'pending',
            ],
            // Employee 3 - Approved casual leave (past)
            [
                'employee_id' => $employees->count() > 2 ? $employees[2]->id : $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Casual Leave')->first()->id,
                'start_date' => '2026-01-15',
                'end_date' => '2026-01-15',
                'total_days' => 1,
                'reason' => 'Moving to new apartment.',
                'status' => 'approved',
                'approved_by' => $adminUser?->id,
                'approved_at' => '2026-01-14 09:00:00',
                'approval_remarks' => 'Approved.',
            ],
            // Employee 2 - Approved sick leave
            [
                'employee_id' => $employees->count() > 1 ? $employees[1]->id : $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Sick Leave')->first()->id,
                'start_date' => '2026-01-27',
                'end_date' => '2026-01-28',
                'total_days' => 2,
                'reason' => 'Flu symptoms, resting at home.',
                'status' => 'approved',
                'approved_by' => $adminUser?->id,
                'approved_at' => '2026-01-27 08:00:00',
                'approval_remarks' => 'Take care and rest well.',
            ],
            // Employee 1 - Cancelled leave
            [
                'employee_id' => $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Casual Leave')->first()->id,
                'start_date' => '2026-02-10',
                'end_date' => '2026-02-10',
                'total_days' => 1,
                'reason' => 'Need to handle personal matter.',
                'status' => 'cancelled',
            ],
            // Employee 3 - Pending annual leave
            [
                'employee_id' => $employees->count() > 2 ? $employees[2]->id : $employees[0]->id,
                'leave_type_id' => $leaveTypes->where('name', 'Annual Leave')->first()->id,
                'start_date' => '2026-02-23',
                'end_date' => '2026-02-27',
                'total_days' => 5,
                'reason' => 'Attending a wedding out of town.',
                'status' => 'pending',
            ],
        ];

        foreach ($leaveApplications as $data) {
            LeaveApplication::create($data);
        }

        // Update leave balances for approved leaves
        $approvedLeaves = LeaveApplication::where('status', 'approved')->get();
        foreach ($approvedLeaves as $leave) {
            $balance = EmployeeLeaveBalance::where('employee_id', $leave->employee_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->where('year', $currentYear)
                ->first();

            if ($balance) {
                $balance->used_days += $leave->total_days;
                $balance->remaining_days = $balance->total_days - $balance->used_days;
                $balance->save();
            }
        }
    }
}
