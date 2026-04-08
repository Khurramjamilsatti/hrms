<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeDeployment;
use App\Models\DeploymentExtension;
use App\Models\Employee;
use App\Models\User;

class DeploymentSeeder extends Seeder
{
    public function run()
    {
        $employees = Employee::with('user')->get();
        
        if ($employees->count() < 3) {
            $this->command->info('Not enough employees to seed deployments. Please run EmployeeSeeder first.');
            return;
        }

        $deploymentTypes = ['domestic', 'international', 'project', 'temporary', 'permanent'];
        $statuses = ['draft', 'pending', 'approved', 'active', 'completed', 'cancelled'];
        $countries = ['Pakistan', 'United States', 'United Kingdom', 'UAE', 'Saudi Arabia', 'Canada', 'Australia'];
        $cities = [
            'Pakistan' => ['Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad'],
            'United States' => ['New York', 'San Francisco', 'Chicago', 'Boston', 'Seattle'],
            'United Kingdom' => ['London', 'Manchester', 'Birmingham', 'Edinburgh'],
            'UAE' => ['Dubai', 'Abu Dhabi', 'Sharjah'],
            'Saudi Arabia' => ['Riyadh', 'Jeddah', 'Dammam'],
            'Canada' => ['Toronto', 'Vancouver', 'Montreal'],
            'Australia' => ['Sydney', 'Melbourne', 'Brisbane'],
        ];

        $projects = [
            'ERP Implementation',
            'Cloud Migration Project',
            'Mobile App Development',
            'Digital Transformation Initiative',
            'Infrastructure Upgrade',
            'AI/ML Research Project',
            'Cybersecurity Enhancement',
            'Data Center Setup',
        ];

        $currencies = ['PKR', 'USD', 'GBP', 'AED', 'SAR', 'CAD', 'AUD'];

        // Create 15 deployments
        foreach ($employees->random(min(12, $employees->count())) as $index => $employee) {
            $deploymentNumber = 200001 + $index;
            $country = $countries[rand(0, count($countries) - 1)];
            $city = $cities[$country][rand(0, count($cities[$country]) - 1)];
            $startDate = now()->subDays(rand(1, 180));
            $duration = rand(30, 365); // 1 month to 1 year
            $endDate = (clone $startDate)->addDays($duration);
            $status = $statuses[rand(0, count($statuses) - 1)];
            
            // 30% chance of being from long leave
            $departureFromLongLeave = rand(1, 100) <= 30;
            $longLeaveStartDate = null;
            $longLeaveEndDate = null;
            
            if ($departureFromLongLeave) {
                $longLeaveStartDate = (clone $startDate)->subDays(rand(30, 90));
                $longLeaveEndDate = (clone $startDate)->subDays(rand(1, 10));
            }

            $deployment = EmployeeDeployment::create([
                'deployment_number' => 'DEP2026' . str_pad($deploymentNumber, 6, '0', STR_PAD_LEFT),
                'employee_id' => $employee->id,
                'deployment_type' => $deploymentTypes[rand(0, 4)],
                'country' => $country,
                'city' => $city,
                'location' => $this->getLocationName($city),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'expected_return_date' => $endDate,
                'actual_return_date' => $status === 'completed' ? $endDate : null,
                'project_name' => $projects[rand(0, count($projects) - 1)],
                'client_name' => 'Client ' . rand(1, 10),
                'purpose' => 'Project deployment for ' . $projects[rand(0, count($projects) - 1)],
                'role' => $employee->designation->title ?? 'Software Engineer',
                'allowance_amount' => rand(10000, 100000),
                'departure_from_long_leave' => $departureFromLongLeave,
                'long_leave_start_date' => $longLeaveStartDate,
                'long_leave_end_date' => $longLeaveEndDate,
                'status' => $status,
                'approved_by' => !in_array($status, ['draft', 'pending']) ? User::where('role', 'admin')->first()?->id : null,
                'approved_at' => !in_array($status, ['draft', 'pending']) ? now()->subDays(rand(1, 30)) : null,
                'created_by' => User::where('role', 'admin')->first()?->id,
                'notes' => !in_array($status, ['draft', 'pending']) ? 'Approved for business requirements' : null,
                'visa_status' => $country !== 'Pakistan' ? ['in_process', 'approved', 'issued'][rand(0, 2)] : 'not_required',
                'insurance_status' => ['active', 'not_required'][rand(0, 1)],
                'travel_ticket_status' => in_array($status, ['active', 'completed']) ? 'used' : 'pending',
            ]);

            // Add extensions for active deployments (40% chance)
            if ($status === 'active' && rand(1, 100) <= 40) {
                $extensionCount = rand(1, 2);
                $currentEndDate = $endDate;
                
                for ($i = 0; $i < $extensionCount; $i++) {
                    $newEndDate = (clone $currentEndDate)->addDays(rand(30, 90));
                    $extensionStatus = $i === $extensionCount - 1 ? 'approved' : 'approved';
                    
                    DeploymentExtension::create([
                        'deployment_id' => $deployment->id,
                        'extension_number' => $i + 1,
                        'requested_by' => $employee->user_id,
                        'previous_end_date' => $currentEndDate,
                        'new_end_date' => $newEndDate,
                        'reason' => $this->getExtensionReason(),
                        'status' => $extensionStatus,
                        'approved_by' => User::where('role', 'admin')->first()?->id,
                        'approved_at' => now()->subDays(rand(1, 15)),
                    ]);
                    
                    $currentEndDate = $newEndDate;
                }
                
                // Update deployment with new end date and extension count
                $deployment->update([
                    'end_date' => $currentEndDate,
                    'extension_count' => $extensionCount,
                    'current_extension_end_date' => $currentEndDate,
                ]);
            }
        }

        $this->command->info('Deployments seeded successfully!');
    }

    private function getLocationName($city)
    {
        $locations = [
            'Downtown Office',
            'Tech Park Campus',
            'Business District',
            'Industrial Zone',
            'Corporate Headquarters',
            'Regional Office',
            'Innovation Center',
            'Development Hub',
        ];

        return $city . ' - ' . $locations[rand(0, count($locations) - 1)];
    }

    private function getExtensionReason()
    {
        $reasons = [
            'Project timeline extended',
            'Additional work requirements',
            'Client requested extension',
            'Critical phase pending',
            'Knowledge transfer needed',
            'Resource shortage at client site',
            'Project scope increased',
            'Handover activities pending',
        ];

        return $reasons[rand(0, count($reasons) - 1)];
    }
}
