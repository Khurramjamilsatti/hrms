<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\EmployeeContract;
use App\Models\OvertimeRequest;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DashboardDataSeeder extends Seeder
{
    public function run()
    {
        // Create sample announcements
        $announcements = [
            [
                'title' => 'New Health Insurance Policy',
                'content' => 'We are excited to announce our new comprehensive health insurance policy for all employees. Coverage starts from next month.',
                'priority' => 'high',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'is_published' => true,
                'created_by' => 1,
            ],
            [
                'title' => 'Office Renovation Notice',
                'content' => 'Please note that the 3rd floor will undergo renovation from next week. Temporary workspaces have been arranged.',
                'priority' => 'medium',
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(18),
                'is_published' => true,
                'created_by' => 1,
            ],
            [
                'title' => 'Annual Performance Reviews',
                'content' => 'Annual performance review season is here! Please complete your self-assessment forms by end of this month.',
                'priority' => 'high',
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->addDays(23),
                'is_published' => true,
                'created_by' => 1,
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::create($data);
        }

        echo "✅ Created " . count($announcements) . " announcements\n";

        // Create sample employee contracts
        $employees = Employee::limit(30)->get();
        $contractsCreated = 0;

        foreach ($employees as $employee) {
            // Some with expiring contracts (next 30 days)
            if ($contractsCreated < 5) {
                EmployeeContract::create([
                    'employee_id' => $employee->id,
                    'contract_type' => 'permanent',
                    'start_date' => Carbon::now()->subYear(),
                    'end_date' => Carbon::now()->addDays(rand(10, 28)),
                    'basic_salary' => rand(40000, 80000),
                    'terms' => 'Standard employment contract terms and conditions.',
                    'status' => 'active',
                ]);
            } else {
                // Regular contracts
                EmployeeContract::create([
                    'employee_id' => $employee->id,
                    'contract_type' => rand(0, 1) ? 'permanent' : 'contract',
                    'start_date' => Carbon::now()->subMonths(rand(6, 24)),
                    'end_date' => Carbon::now()->addMonths(rand(12, 36)),
                    'basic_salary' => rand(40000, 100000),
                    'terms' => 'Standard employment contract terms and conditions.',
                    'status' => 'active',
                ]);
            }
            $contractsCreated++;
        }

        echo "✅ Created {$contractsCreated} employee contracts\n";

        // Create sample overtime requests
        $overtimeEmployees = Employee::limit(15)->get();
        $overtimeCreated = 0;

        foreach ($overtimeEmployees as $employee) {
            // Some pending, some approved
            $status = $overtimeCreated < 5 ? 'pending' : (rand(0, 1) ? 'approved' : 'rejected');
            
            OvertimeRequest::create([
                'employee_id' => $employee->id,
                'date' => Carbon::now()->subDays(rand(1, 7)),
                'hours' => rand(2, 6),
                'reason' => 'Project deadline and urgent deliverables',
                'status' => $status,
                'approved_by' => $status !== 'pending' ? 1 : null,
                'approval_remarks' => $status !== 'pending' ? 'Reviewed and ' . $status : null,
                'approved_at' => $status !== 'pending' ? Carbon::now()->subDays(rand(0, 3)) : null,
            ]);
            $overtimeCreated++;
        }

        echo "✅ Created {$overtimeCreated} overtime requests\n";

        // Update some employee birthdays to be in next 30 days for testing
        $employeesForBirthday = Employee::limit(8)->get();
        foreach ($employeesForBirthday as $index => $employee) {
            $futureDate = Carbon::now()->addDays(rand(1, 30));
            $birthYear = rand(1985, 1998);
            
            $employee->update([
                'date_of_birth' => Carbon::create($birthYear, $futureDate->month, $futureDate->day)
            ]);
        }

        echo "✅ Updated birthdays for " . $employeesForBirthday->count() . " employees\n";

        echo "\n🎉 Dashboard data seeding completed successfully!\n";
    }
}
