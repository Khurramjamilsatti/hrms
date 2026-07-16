<?php

namespace Tests\Feature;

use App\Models\AdvanceRequest;
use App\Models\LeaveApplication;
use App\Models\Loan;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OwnershipAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    private function employeeUser(): User
    {
        $user = User::query()
            ->where('role', 'employee')
            ->whereHas('employee')
            ->with('employee')
            ->first();

        $this->assertNotNull($user, 'Seeded employee user with employee profile is required');

        return $user;
    }

    public function test_employee_cannot_view_other_payroll(): void
    {
        $user = $this->employeeUser();
        $other = Payroll::query()
            ->where('employee_id', '!=', $user->employee->id)
            ->first();

        if (!$other) {
            $this->markTestSkipped('No other payroll records available');
        }

        Sanctum::actingAs($user);

        $this->getJson('/api/my-payroll/' . $other->id)->assertForbidden();
        $this->getJson('/api/payroll/' . $other->id)->assertForbidden();
    }

    public function test_employee_cannot_view_other_loan_or_leave_or_advance(): void
    {
        $user = $this->employeeUser();
        Sanctum::actingAs($user);

        $loan = Loan::query()->where('employee_id', '!=', $user->employee->id)->first();
        if ($loan) {
            $this->getJson('/api/loans/' . $loan->id)->assertForbidden();
        }

        $leave = LeaveApplication::query()->where('employee_id', '!=', $user->employee->id)->first();
        if ($leave) {
            $this->getJson('/api/leave-applications/' . $leave->id)->assertForbidden();
        }

        $advance = AdvanceRequest::query()
            ->whereIn('advance_type', ['salary', 'emergency_salary', 'festival'])
            ->where('employee_id', '!=', $user->employee->id)
            ->first();
        if ($advance) {
            $this->getJson('/api/salary-advances/' . $advance->id)->assertForbidden();
        }

        $this->assertTrue($loan || $leave || $advance, 'Need at least one foreign record to assert IDOR protection');
    }

    public function test_employee_cannot_access_company_dashboard_stats(): void
    {
        Sanctum::actingAs($this->employeeUser());

        $this->getJson('/api/dashboard/stats')->assertForbidden();
    }

    public function test_admin_can_access_dashboard_stats_and_payroll(): void
    {
        $admin = User::query()->where('role', 'super_admin')->first()
            ?? User::query()->where('role', 'hr_admin')->first();

        $this->assertNotNull($admin);
        Sanctum::actingAs($admin);

        $this->getJson('/api/dashboard/stats')->assertOk();
        $this->getJson('/api/salary-advances')->assertOk();
        $this->getJson('/api/assets/assignments/list')->assertOk();
        $this->getJson('/api/timesheets/summary')->assertOk();
    }
}
