<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationHierarchySeeder extends Seeder
{
    /**
     * Setup realistic organization hierarchy with manager-subordinate relationships
     */
    public function run(): void
    {
        $this->command->info('Setting up organization hierarchy...');
        
        DB::beginTransaction();
        
        try {
            // Step 1: Find or create CEO (top of hierarchy)
            $ceoDesignation = DB::table('designations')
                ->whereIn(DB::raw('LOWER(title)'), ['ceo', 'chief executive officer', 'managing director'])
                ->first();
            
            if (!$ceoDesignation) {
                $ceoDesignation = DB::table('designations')
                    ->where('level', 'executive')
                    ->first();
            }
            
            $ceo = null;
            if ($ceoDesignation) {
                $ceo = Employee::where('designation_id', $ceoDesignation->id)
                    ->where('employment_status', 'active')
                    ->first();
            }
            
            // If no CEO found, pick the first admin user
            if (!$ceo) {
                $adminUser = User::where('role', 'admin')->where('email', 'admin@hrms.com')->first();
                if ($adminUser && $adminUser->employee) {
                    $ceo = $adminUser->employee;
                }
            }
            
            // Still no CEO? Pick any admin
            if (!$ceo) {
                $adminUser = User::where('role', 'admin')->first();
                if ($adminUser && $adminUser->employee) {
                    $ceo = $adminUser->employee;
                } else {
                    // Last resort: pick first employee
                    $ceo = Employee::where('employment_status', 'active')->first();
                }
            }
            
            if (!$ceo) {
                $this->command->error('No employees found to set as CEO!');
                return;
            }
            
            // CEO has no manager - set and save immediately
            $ceo->manager_id = null;
            $ceo->save();
            $ceoUserId = $ceo->user_id;
            $this->command->info("CEO: {$ceo->first_name} {$ceo->last_name} (User ID: {$ceoUserId})");
            
            // Step 2: Get all managers (users with role 'manager' or 'admin')
            $managerUsers = User::whereIn('role', ['admin', 'manager'])
                ->where('id', '!=', $ceoUserId)
                ->with('employee')
                ->get()
                ->filter(function($user) {
                    return $user->employee && $user->employee->employment_status === 'active';
                });
            
            $this->command->info("Found " . $managerUsers->count() . " potential managers");
            
            // Step 3: Assign department managers to report to CEO
            $departments = Department::with('employees')->get();
            $departmentManagers = [];
            
            foreach ($departments as $department) {
                // Find a manager user in this department
                $deptManager = $managerUsers->first(function($user) use ($department) {
                    return $user->employee->department_id === $department->id;
                });
                
                if (!$deptManager && $department->employees->count() > 0) {
                    // No manager user in dept, pick the first employee
                    $deptManager = $department->employees->where('employment_status', 'active')->first();
                    if ($deptManager) {
                        $deptManager = User::find($deptManager->user_id);
                    }
                }
                
                if ($deptManager && $deptManager->employee) {
                    $employee = $deptManager->employee;
                    
                    // Don't assign CEO as subordinate
                    if ($employee->id !== $ceo->id) {
                        $employee->manager_id = $ceoUserId;
                        $employee->save();
                        $departmentManagers[$department->id] = $employee;
                        $this->command->info("  Dept Manager ({$department->name}): {$employee->first_name} {$employee->last_name}");
                    }
                }
            }
            
            // Step 4: Assign remaining managers to their department heads
            foreach ($managerUsers as $managerUser) {
                $employee = $managerUser->employee;
                
                // Skip CEO
                if ($employee->id === $ceo->id) {
                    continue;
                }
                
                // Skip if already assigned (department manager)
                if ($employee->manager_id) {
                    continue;
                }
                
                // Assign to department manager or CEO
                if (isset($departmentManagers[$employee->department_id])) {
                    $deptManager = $departmentManagers[$employee->department_id];
                    if ($deptManager->id !== $employee->id) {
                        $employee->manager_id = $deptManager->user_id;
                        $employee->save();
                    }
                } else {
                    // No dept manager, report to CEO
                    $employee->manager_id = $ceoUserId;
                    $employee->save();
                }
            }
            
            // Step 5: Assign regular employees to managers in their department
            $regularEmployees = Employee::where('employment_status', 'active')
                ->whereNull('manager_id')
                ->get();
            
            $this->command->info("Assigning " . $regularEmployees->count() . " regular employees to managers...");
            
            foreach ($regularEmployees as $employee) {
                // Find managers in same department
                $managersInDept = collect($departmentManagers)->filter(function($manager) use ($employee) {
                    return $manager->department_id === $employee->department_id;
                })->values();
                
                if ($managersInDept->count() > 0) {
                    // Assign to department manager
                    $employee->manager_id = $managersInDept->first()->user_id;
                } else {
                    // No manager in department, check if there are any managers with role manager
                    $anyManager = $managerUsers->first(function($user) use ($employee) {
                        return $user->employee->department_id === $employee->department_id 
                            && $user->employee->id !== $employee->id;
                    });
                    
                    if ($anyManager) {
                        $employee->manager_id = $anyManager->id;
                    } else {
                        // Last resort: report to CEO
                        $employee->manager_id = $ceoUserId;
                    }
                }
                
                $employee->save();
            }
            
            // Ensure CEO still has no manager (double check)
            $ceo->refresh();
            $ceo->manager_id = null;
            $ceo->save();
            
            // Step 6: Handle employees on leave and other statuses
            $nonActiveEmployees = Employee::whereIn('employment_status', ['on_leave', 'probation', 'suspended'])
                ->whereNull('manager_id')
                ->get();
            
            foreach ($nonActiveEmployees as $employee) {
                if (isset($departmentManagers[$employee->department_id])) {
                    $employee->manager_id = $departmentManagers[$employee->department_id]->user_id;
                } else {
                    $employee->manager_id = $ceoUserId;
                }
                $employee->save();
            }
            
            DB::commit();
            
            // Statistics
            $stats = [
                'total' => Employee::count(),
                'with_manager' => Employee::whereNotNull('manager_id')->count(),
                'without_manager' => Employee::whereNull('manager_id')->count(),
                'active_with_manager' => Employee::where('employment_status', 'active')->whereNotNull('manager_id')->count(),
            ];
            
            $this->command->info('');
            $this->command->info('Organization Hierarchy Setup Complete!');
            $this->command->info("Total Employees: {$stats['total']}");
            $this->command->info("With Manager: {$stats['with_manager']}");
            $this->command->info("Without Manager: {$stats['without_manager']} (should be 1 - the CEO)");
            $this->command->info("Active with Manager: {$stats['active_with_manager']}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error setting up hierarchy: ' . $e->getMessage());
            throw $e;
        }
    }
}
