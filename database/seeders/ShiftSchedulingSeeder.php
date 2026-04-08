<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShiftRoster;
use App\Models\ShiftAssignment;
use App\Models\ShiftSwapRequest;
use App\Models\Shift;
use App\Models\User;
use App\Models\Department;

class ShiftSchedulingSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();
        $employees = User::where('role', 'employee')->get();
        $shifts = Shift::all();

        if ($shifts->isEmpty()) {
            // Create default shifts if they don't exist
            $shifts = collect([
                Shift::create([
                    'name' => 'Morning Shift',
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                    'is_active' => true
                ]),
                Shift::create([
                    'name' => 'Evening Shift',
                    'start_time' => '14:00:00',
                    'end_time' => '22:00:00',
                    'is_active' => true
                ]),
                Shift::create([
                    'name' => 'Night Shift',
                    'start_time' => '22:00:00',
                    'end_time' => '06:00:00',
                    'is_active' => true
                ])
            ]);
        }

        // Create Rosters
        $admin = User::where('role', 'admin')->first();
        
        foreach ($departments->take(4) as $index => $department) {
            // Previous month roster (published)
            $prevMonthStart = now()->subMonth()->startOfMonth();
            $prevMonthEnd = now()->subMonth()->endOfMonth();
            
            $roster1 = ShiftRoster::create([
                'name' => $department->name . ' - ' . $prevMonthStart->format('F Y'),
                'department_id' => $department->id,
                'start_date' => $prevMonthStart->format('Y-m-d'),
                'end_date' => $prevMonthEnd->format('Y-m-d'),
                'status' => 'published',
                'created_by' => $admin->id,
                'published_at' => $prevMonthStart->subDays(5)
            ]);

            // Current month roster (published)
            $currentMonthStart = now()->startOfMonth();
            $currentMonthEnd = now()->endOfMonth();
            
            $roster2 = ShiftRoster::create([
                'name' => $department->name . ' - ' . $currentMonthStart->format('F Y'),
                'department_id' => $department->id,
                'start_date' => $currentMonthStart->format('Y-m-d'),
                'end_date' => $currentMonthEnd->format('Y-m-d'),
                'status' => 'published',
                'created_by' => $admin->id,
                'published_at' => $currentMonthStart->subDays(3)
            ]);

            // Next month roster (draft)
            $nextMonthStart = now()->addMonth()->startOfMonth();
            $nextMonthEnd = now()->addMonth()->endOfMonth();
            
            $roster3 = ShiftRoster::create([
                'name' => $department->name . ' - ' . $nextMonthStart->format('F Y'),
                'department_id' => $department->id,
                'start_date' => $nextMonthStart->format('Y-m-d'),
                'end_date' => $nextMonthEnd->format('Y-m-d'),
                'status' => 'draft',
                'created_by' => $admin->id,
                'published_at' => null
            ]);

            // Create shift assignments for current roster
            $deptEmployees = $employees->where('employee.department_id', $department->id)->take(10);
            
            $startDate = $currentMonthStart->copy();
            while ($startDate <= $currentMonthEnd) {
                foreach ($deptEmployees as $employee) {
                    // Skip weekends randomly
                    if ($startDate->isWeekend() && rand(0, 1)) {
                        continue;
                    }

                    $shift = $shifts->random();
                    
                    ShiftAssignment::create([
                        'roster_id' => $roster2->id,
                        'employee_id' => $employee->id,
                        'shift_id' => $shift->id,
                        'date' => $startDate->format('Y-m-d'),
                        'start_time' => $shift->start_time,
                        'end_time' => $shift->end_time,
                        'is_day_off' => false
                    ]);
                }
                $startDate->addDay();
            }
        }

        // Create Shift Swap Requests
        $allAssignments = ShiftAssignment::whereHas('roster', function($query) {
            $query->where('start_date', '<=', now()->format('Y-m-d'))
                  ->where('end_date', '>=', now()->format('Y-m-d'));
        })->get();

        for ($i = 0; $i < 15; $i++) {
            if ($allAssignments->count() < 2) break;

            $requesterAssignment = $allAssignments->random();
            $swapperAssignment = $allAssignments->where('id', '!=', $requesterAssignment->id)
                                                 ->where('date', $requesterAssignment->date)
                                                 ->first();

            if (!$swapperAssignment) continue;

            ShiftSwapRequest::create([
                'requester_id' => $requesterAssignment->employee_id,
                'requester_assignment_id' => $requesterAssignment->id,
                'swapper_id' => $swapperAssignment->employee_id,
                'swapper_assignment_id' => $swapperAssignment->id,
                'reason' => ['Personal appointment', 'Family emergency', 'Medical checkup', 'Other commitment'][array_rand(['Personal appointment', 'Family emergency', 'Medical checkup', 'Other commitment'])],
                'status' => ['pending', 'accepted', 'rejected', 'approved', 'declined'][array_rand(['pending', 'accepted', 'rejected', 'approved', 'declined'])],
                'approved_by' => rand(0, 1) ? 1 : null,
                'approved_at' => rand(0, 1) ? now()->subDays(rand(1, 3)) : null,
                'rejection_reason' => rand(0, 1) ? 'Schedule conflict' : null
            ]);
        }

        $this->command->info('Shift Scheduling data created successfully!');
    }
}
