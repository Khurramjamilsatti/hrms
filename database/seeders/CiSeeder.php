<?php

namespace Database\Seeders;

use App\Models\AdvanceRequest;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\Loan;
use App\Models\Payroll;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Minimal dataset so CI feature tests have admin + employee + foreign records.
 */
class CiSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $employeeRole = Role::query()->where('slug', 'employee')->first();
        $hrRole = Role::query()->where('slug', 'hr_admin')->first();

        $admin = User::query()->where('email', 'admin@hrms.com')->first();

        $hr = User::query()->firstOrCreate(
            ['email' => 'hradmin@hrms.com'],
            [
                'name' => 'HR Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'hr_admin',
                'role_id' => $hrRole?->id,
            ]
        );

        if ($hrRole && $hr->role_id !== $hrRole->id) {
            $hr->update(['role' => 'hr_admin', 'role_id' => $hrRole->id]);
        }

        $employeeUser = User::query()->firstOrCreate(
            ['email' => 'employee@hrms.com'],
            [
                'name' => 'CI Employee',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'employee',
                'role_id' => $employeeRole?->id,
            ]
        );

        if ($employeeRole && $employeeUser->role_id !== $employeeRole->id) {
            $employeeUser->update(['role' => 'employee', 'role_id' => $employeeRole->id]);
        }

        $employee = Employee::query()->firstOrCreate(
            ['user_id' => $employeeUser->id],
            [
                'employee_code' => 'CI-EMP-001',
                'first_name' => 'CI',
                'last_name' => 'Employee',
                'joining_date' => now()->subYear()->toDateString(),
                'employment_type' => 'full_time',
                'employment_status' => 'active',
            ]
        );

        // Second employee owned by admin (or HR) so "other" records exist for IDOR tests
        $otherUser = User::query()->firstOrCreate(
            ['email' => 'other@hrms.com'],
            [
                'name' => 'CI Other',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'employee',
                'role_id' => $employeeRole?->id,
            ]
        );

        $otherEmployee = Employee::query()->firstOrCreate(
            ['user_id' => $otherUser->id],
            [
                'employee_code' => 'CI-EMP-002',
                'first_name' => 'CI',
                'last_name' => 'Other',
                'joining_date' => now()->subYears(2)->toDateString(),
                'employment_type' => 'full_time',
                'employment_status' => 'active',
            ]
        );

        // Ensure admin also has an employee profile (common in this app)
        if ($admin && !Employee::query()->where('user_id', $admin->id)->exists()) {
            Employee::query()->create([
                'user_id' => $admin->id,
                'employee_code' => 'CI-ADM-001',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'joining_date' => now()->subYears(3)->toDateString(),
                'employment_type' => 'full_time',
                'employment_status' => 'active',
            ]);
        }

        $leaveType = LeaveType::query()->firstOrCreate(
            ['name' => 'Annual Leave'],
            [
                'description' => 'CI annual leave',
                'days_per_year' => 20,
                'is_paid' => true,
                'is_active' => true,
            ]
        );

        if (!Payroll::query()->where('employee_id', $otherEmployee->id)->exists()) {
            Payroll::query()->create([
                'employee_id' => $otherEmployee->id,
                'month' => (int) now()->month,
                'year' => (int) now()->year,
                'basic_salary' => 100000,
                'total_earnings' => 100000,
                'total_deductions' => 0,
                'net_salary' => 100000,
                'working_days' => 22,
                'present_days' => 22,
                'status' => 'draft',
            ]);
        }

        if (!Loan::query()->where('employee_id', $otherEmployee->id)->exists()) {
            Loan::query()->create([
                'employee_id' => $otherEmployee->id,
                'loan_number' => 'LN-CI-00001',
                'loan_type' => 'personal',
                'amount' => 50000,
                'interest_rate' => 0,
                'installments' => 10,
                'installment_amount' => 5000,
                'start_date' => now()->toDateString(),
                'purpose' => 'CI test loan',
                'status' => 'pending',
                'balance_amount' => 50000,
            ]);
        }

        if (!LeaveApplication::query()->where('employee_id', $otherEmployee->id)->exists()) {
            LeaveApplication::query()->create([
                'employee_id' => $otherEmployee->id,
                'leave_type_id' => $leaveType->id,
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => now()->addDays(6)->toDateString(),
                'total_days' => 2,
                'reason' => 'CI test leave',
                'status' => 'pending',
                'approval_level' => 'pending',
            ]);
        }

        if (!AdvanceRequest::query()
            ->where('employee_id', $otherEmployee->id)
            ->whereIn('advance_type', ['salary', 'emergency_salary', 'festival'])
            ->exists()) {
            AdvanceRequest::query()->create([
                'employee_id' => $otherEmployee->id,
                'request_number' => 'ADV-CI-' . strtoupper(uniqid()),
                'purpose' => 'CI salary advance',
                'amount' => 15000,
                'required_date' => now()->addWeek()->toDateString(),
                'status' => 'pending',
                'advance_type' => 'salary',
                'installments' => 1,
                'installment_amount' => 15000,
            ]);
        }

        // Touch employee so relation exists for IDOR subject
        $employee->refresh();
    }
}
