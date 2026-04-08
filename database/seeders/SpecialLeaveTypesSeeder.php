<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class SpecialLeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialLeaveTypes = [
            [
                'name' => 'Gratuity Leave',
                'description' => 'Leave granted as part of gratuity/end-of-service benefits for long-serving employees',
                'days_per_year' => 30,
                'is_paid' => true,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Study Leave',
                'description' => 'Leave for pursuing higher education or professional certifications',
                'days_per_year' => 60,
                'is_paid' => false,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Compassionate Leave',
                'description' => 'Special leave granted for family emergencies or bereavement beyond regular provisions',
                'days_per_year' => 10,
                'is_paid' => true,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sabbatical Leave',
                'description' => 'Extended leave for personal development, research, or special projects',
                'days_per_year' => 90,
                'is_paid' => false,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Hajj/Pilgrimage Leave',
                'description' => 'Special leave for religious pilgrimage',
                'days_per_year' => 45,
                'is_paid' => true,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Long Service Leave',
                'description' => 'Additional leave granted for employees with long tenure',
                'days_per_year' => 15,
                'is_paid' => true,
                'is_carry_forward' => true,
                'max_carry_forward_days' => 30,
                'requires_document' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Military Service Leave',
                'description' => 'Leave for mandatory military or national service',
                'days_per_year' => 365,
                'is_paid' => false,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Jury Duty Leave',
                'description' => 'Leave for jury duty or court summons',
                'days_per_year' => 10,
                'is_paid' => true,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Adoption Leave',
                'description' => 'Leave for adoption procedures and bonding with adopted child',
                'days_per_year' => 60,
                'is_paid' => true,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Volunteer Leave',
                'description' => 'Leave for approved volunteering or community service',
                'days_per_year' => 5,
                'is_paid' => true,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => true,
                'is_active' => true,
            ],
        ];

        foreach ($specialLeaveTypes as $leaveType) {
            LeaveType::updateOrCreate(
                ['name' => $leaveType['name']],
                $leaveType
            );
        }

        $this->command->info('Special leave types seeded successfully!');
    }
}
