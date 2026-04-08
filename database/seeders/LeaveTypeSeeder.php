<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'description' => 'Paid annual vacation leave',
                'days_per_year' => 20,
                'is_paid' => true,
                'is_carry_forward' => true,
                'max_carry_forward_days' => 5,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Medical leave for illness',
                'days_per_year' => 10,
                'is_paid' => true,
                'requires_document' => true,
            ],
            [
                'name' => 'Casual Leave',
                'description' => 'Short-term casual leave',
                'days_per_year' => 10,
                'is_paid' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Maternity leave for female employees',
                'days_per_year' => 90,
                'is_paid' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'description' => 'Leave without pay',
                'days_per_year' => 0,
                'is_paid' => false,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::create($type);
        }
    }
}
