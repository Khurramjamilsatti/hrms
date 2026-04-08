<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all permissions by module and action
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard', 'action' => 'view'],
            ['name' => 'View Dashboard Stats', 'slug' => 'dashboard.stats', 'module' => 'dashboard', 'action' => 'stats'],

            // Employees
            ['name' => 'View Employees', 'slug' => 'employees.view', 'module' => 'employees', 'action' => 'view'],
            ['name' => 'Create Employee', 'slug' => 'employees.create', 'module' => 'employees', 'action' => 'create'],
            ['name' => 'Edit Employee', 'slug' => 'employees.edit', 'module' => 'employees', 'action' => 'edit'],
            ['name' => 'Delete Employee', 'slug' => 'employees.delete', 'module' => 'employees', 'action' => 'delete'],
            ['name' => 'View Own Profile', 'slug' => 'employees.view_own', 'module' => 'employees', 'action' => 'view_own'],

            // Attendance
            ['name' => 'View Attendance', 'slug' => 'attendance.view', 'module' => 'attendance', 'action' => 'view'],
            ['name' => 'Check In/Out', 'slug' => 'attendance.checkin', 'module' => 'attendance', 'action' => 'checkin'],
            ['name' => 'Manage Attendance', 'slug' => 'attendance.manage', 'module' => 'attendance', 'action' => 'manage'],
            ['name' => 'View Attendance Reports', 'slug' => 'attendance.reports', 'module' => 'attendance', 'action' => 'reports'],

            // Leave Management
            ['name' => 'View Leaves', 'slug' => 'leaves.view', 'module' => 'leaves', 'action' => 'view'],
            ['name' => 'Apply Leave', 'slug' => 'leaves.apply', 'module' => 'leaves', 'action' => 'apply'],
            ['name' => 'Approve Leave', 'slug' => 'leaves.approve', 'module' => 'leaves', 'action' => 'approve'],
            ['name' => 'Reject Leave', 'slug' => 'leaves.reject', 'module' => 'leaves', 'action' => 'reject'],
            ['name' => 'Cancel Leave', 'slug' => 'leaves.cancel', 'module' => 'leaves', 'action' => 'cancel'],

            // Payroll
            ['name' => 'View Payroll', 'slug' => 'payroll.view', 'module' => 'payroll', 'action' => 'view'],
            ['name' => 'Generate Payroll', 'slug' => 'payroll.generate', 'module' => 'payroll', 'action' => 'generate'],
            ['name' => 'Process Payroll', 'slug' => 'payroll.process', 'module' => 'payroll', 'action' => 'process'],
            ['name' => 'View Own Payroll', 'slug' => 'payroll.view_own', 'module' => 'payroll', 'action' => 'view_own'],

            // Departments
            ['name' => 'View Departments', 'slug' => 'departments.view', 'module' => 'departments', 'action' => 'view'],
            ['name' => 'Create Department', 'slug' => 'departments.create', 'module' => 'departments', 'action' => 'create'],
            ['name' => 'Edit Department', 'slug' => 'departments.edit', 'module' => 'departments', 'action' => 'edit'],
            ['name' => 'Delete Department', 'slug' => 'departments.delete', 'module' => 'departments', 'action' => 'delete'],

            // Recruitment
            ['name' => 'View Recruitment', 'slug' => 'recruitment.view', 'module' => 'recruitment', 'action' => 'view'],
            ['name' => 'Manage Positions', 'slug' => 'recruitment.positions', 'module' => 'recruitment', 'action' => 'positions'],
            ['name' => 'Manage Applications', 'slug' => 'recruitment.applications', 'module' => 'recruitment', 'action' => 'applications'],

            // Performance
            ['name' => 'View Performance', 'slug' => 'performance.view', 'module' => 'performance', 'action' => 'view'],
            ['name' => 'Manage Reviews', 'slug' => 'performance.reviews', 'module' => 'performance', 'action' => 'reviews'],
            ['name' => 'Manage Goals', 'slug' => 'performance.goals', 'module' => 'performance', 'action' => 'goals'],

            // Assets
            ['name' => 'View Assets', 'slug' => 'assets.view', 'module' => 'assets', 'action' => 'view'],
            ['name' => 'Manage Assets', 'slug' => 'assets.manage', 'module' => 'assets', 'action' => 'manage'],
            ['name' => 'Assign Assets', 'slug' => 'assets.assign', 'module' => 'assets', 'action' => 'assign'],

            // Announcements
            ['name' => 'View Announcements', 'slug' => 'announcements.view', 'module' => 'announcements', 'action' => 'view'],
            ['name' => 'Create Announcement', 'slug' => 'announcements.create', 'module' => 'announcements', 'action' => 'create'],
            ['name' => 'Edit Announcement', 'slug' => 'announcements.edit', 'module' => 'announcements', 'action' => 'edit'],
            ['name' => 'Delete Announcement', 'slug' => 'announcements.delete', 'module' => 'announcements', 'action' => 'delete'],

            // Timesheets
            ['name' => 'View Timesheets', 'slug' => 'timesheets.view', 'module' => 'timesheets', 'action' => 'view'],
            ['name' => 'Submit Timesheet', 'slug' => 'timesheets.submit', 'module' => 'timesheets', 'action' => 'submit'],
            ['name' => 'Approve Timesheet', 'slug' => 'timesheets.approve', 'module' => 'timesheets', 'action' => 'approve'],
            ['name' => 'Manage Projects', 'slug' => 'timesheets.projects', 'module' => 'timesheets', 'action' => 'projects'],

            // Onboarding
            ['name' => 'View Onboarding', 'slug' => 'onboarding.view', 'module' => 'onboarding', 'action' => 'view'],
            ['name' => 'Manage Onboarding', 'slug' => 'onboarding.manage', 'module' => 'onboarding', 'action' => 'manage'],

            // Training
            ['name' => 'View Training', 'slug' => 'training.view', 'module' => 'training', 'action' => 'view'],
            ['name' => 'Manage Training', 'slug' => 'training.manage', 'module' => 'training', 'action' => 'manage'],
            ['name' => 'Enroll Training', 'slug' => 'training.enroll', 'module' => 'training', 'action' => 'enroll'],

            // Travel & Expenses
            ['name' => 'View Travel & Expenses', 'slug' => 'travel.view', 'module' => 'travel', 'action' => 'view'],
            ['name' => 'Submit Travel Request', 'slug' => 'travel.submit', 'module' => 'travel', 'action' => 'submit'],
            ['name' => 'Approve Travel', 'slug' => 'travel.approve', 'module' => 'travel', 'action' => 'approve'],
            ['name' => 'Submit Expense', 'slug' => 'travel.expense', 'module' => 'travel', 'action' => 'expense'],

            // Shifts
            ['name' => 'View Shifts', 'slug' => 'shifts.view', 'module' => 'shifts', 'action' => 'view'],
            ['name' => 'Manage Shifts', 'slug' => 'shifts.manage', 'module' => 'shifts', 'action' => 'manage'],
            ['name' => 'Assign Shifts', 'slug' => 'shifts.assign', 'module' => 'shifts', 'action' => 'assign'],

            // Helpdesk
            ['name' => 'View Tickets', 'slug' => 'helpdesk.view', 'module' => 'helpdesk', 'action' => 'view'],
            ['name' => 'Create Ticket', 'slug' => 'helpdesk.create', 'module' => 'helpdesk', 'action' => 'create'],
            ['name' => 'Manage Tickets', 'slug' => 'helpdesk.manage', 'module' => 'helpdesk', 'action' => 'manage'],

            // Files
            ['name' => 'View Files', 'slug' => 'files.view', 'module' => 'files', 'action' => 'view'],
            ['name' => 'Upload Files', 'slug' => 'files.upload', 'module' => 'files', 'action' => 'upload'],
            ['name' => 'Manage Files', 'slug' => 'files.manage', 'module' => 'files', 'action' => 'manage'],

            // Calendar
            ['name' => 'View Calendar', 'slug' => 'calendar.view', 'module' => 'calendar', 'action' => 'view'],
            ['name' => 'Manage Events', 'slug' => 'calendar.manage', 'module' => 'calendar', 'action' => 'manage'],

            // Notifications
            ['name' => 'View Notifications', 'slug' => 'notifications.view', 'module' => 'notifications', 'action' => 'view'],
            ['name' => 'Manage Notifications', 'slug' => 'notifications.manage', 'module' => 'notifications', 'action' => 'manage'],

            // Organization
            ['name' => 'View Organization', 'slug' => 'organization.view', 'module' => 'organization', 'action' => 'view'],

            // Loans
            ['name' => 'View Loans', 'slug' => 'loans.view', 'module' => 'loans', 'action' => 'view'],
            ['name' => 'Apply Loan', 'slug' => 'loans.apply', 'module' => 'loans', 'action' => 'apply'],
            ['name' => 'Approve Loan', 'slug' => 'loans.approve', 'module' => 'loans', 'action' => 'approve'],
            ['name' => 'Manage Loans', 'slug' => 'loans.manage', 'module' => 'loans', 'action' => 'manage'],

            // Salary Advances
            ['name' => 'View Salary Advances', 'slug' => 'salary_advances.view', 'module' => 'salary_advances', 'action' => 'view'],
            ['name' => 'Request Advance', 'slug' => 'salary_advances.request', 'module' => 'salary_advances', 'action' => 'request'],
            ['name' => 'Approve Advance', 'slug' => 'salary_advances.approve', 'module' => 'salary_advances', 'action' => 'approve'],

            // Salary Components
            ['name' => 'View Salary Components', 'slug' => 'salary_components.view', 'module' => 'salary_components', 'action' => 'view'],
            ['name' => 'Manage Salary Components', 'slug' => 'salary_components.manage', 'module' => 'salary_components', 'action' => 'manage'],

            // CV Bank
            ['name' => 'View CV Bank', 'slug' => 'cv_bank.view', 'module' => 'cv_bank', 'action' => 'view'],
            ['name' => 'Manage CV Bank', 'slug' => 'cv_bank.manage', 'module' => 'cv_bank', 'action' => 'manage'],

            // Deployments
            ['name' => 'View Deployments', 'slug' => 'deployments.view', 'module' => 'deployments', 'action' => 'view'],
            ['name' => 'Manage Deployments', 'slug' => 'deployments.manage', 'module' => 'deployments', 'action' => 'manage'],

            // Roles & Permissions (Super Admin only)
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'roles', 'action' => 'manage'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'module' => 'permissions', 'action' => 'manage'],
            ['name' => 'Assign User Roles', 'slug' => 'users.assign_roles', 'module' => 'users', 'action' => 'assign_roles'],
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Define roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'System administrator with full access to all modules and settings',
                'is_system_role' => true,
                'is_active' => true,
                'permissions' => [] // Super admin gets all permissions by default in code
            ],
            [
                'name' => 'HR Admin',
                'slug' => 'hr_admin',
                'description' => 'HR administrator with access to all HR functions',
                'is_system_role' => true,
                'is_active' => true,
                'permissions' => [
                    'dashboard.view', 'dashboard.stats',
                    'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
                    'attendance.view', 'attendance.manage', 'attendance.reports',
                    'leaves.view', 'leaves.approve', 'leaves.reject',
                    'payroll.view', 'payroll.generate', 'payroll.process',
                    'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
                    'recruitment.view', 'recruitment.positions', 'recruitment.applications',
                    'performance.view', 'performance.reviews', 'performance.goals',
                    'assets.view', 'assets.manage', 'assets.assign',
                    'announcements.view', 'announcements.create', 'announcements.edit', 'announcements.delete',
                    'timesheets.view', 'timesheets.approve', 'timesheets.projects',
                    'onboarding.view', 'onboarding.manage',
                    'training.view', 'training.manage', 'training.enroll',
                    'travel.view', 'travel.approve', 'travel.expense',
                    'shifts.view', 'shifts.manage', 'shifts.assign',
                    'helpdesk.view', 'helpdesk.manage',
                    'files.view', 'files.upload', 'files.manage',
                    'calendar.view', 'calendar.manage',
                    'notifications.view', 'notifications.manage',
                    'organization.view',
                    'loans.view', 'loans.approve', 'loans.manage',
                    'salary_advances.view', 'salary_advances.approve',
                    'salary_components.view', 'salary_components.manage',
                    'cv_bank.view', 'cv_bank.manage',
                    'deployments.view', 'deployments.manage',
                ]
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Department manager with team management capabilities',
                'is_system_role' => true,
                'is_active' => true,
                'permissions' => [
                    'dashboard.view', 'dashboard.stats',
                    'employees.view', 'employees.view_own',
                    'attendance.view', 'attendance.checkin', 'attendance.reports',
                    'leaves.view', 'leaves.apply', 'leaves.approve', 'leaves.reject', 'leaves.cancel',
                    'payroll.view_own',
                    'departments.view',
                    'performance.view', 'performance.reviews',
                    'assets.view',
                    'announcements.view',
                    'timesheets.view', 'timesheets.submit', 'timesheets.approve',
                    'training.view',
                    'travel.view', 'travel.submit', 'travel.approve', 'travel.expense',
                    'shifts.view',
                    'helpdesk.view', 'helpdesk.create',
                    'files.view', 'files.upload',
                    'calendar.view',
                    'notifications.view',
                    'organization.view',
                    'loans.view', 'loans.apply',
                    'salary_advances.view', 'salary_advances.request',
                ]
            ],
            [
                'name' => 'Employee',
                'slug' => 'employee',
                'description' => 'Regular employee with self-service access',
                'is_system_role' => true,
                'is_active' => true,
                'permissions' => [
                    'dashboard.view',
                    'employees.view_own',
                    'attendance.view', 'attendance.checkin',
                    'leaves.view', 'leaves.apply', 'leaves.cancel',
                    'payroll.view_own',
                    'performance.view',
                    'announcements.view',
                    'timesheets.view', 'timesheets.submit',
                    'training.view',
                    'travel.view', 'travel.submit', 'travel.expense',
                    'shifts.view',
                    'helpdesk.view', 'helpdesk.create',
                    'files.view', 'files.upload',
                    'calendar.view',
                    'notifications.view',
                    'organization.view',
                    'loans.view', 'loans.apply',
                    'salary_advances.view', 'salary_advances.request',
                ]
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleData) {
            $permissionSlugs = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );

            if (!empty($permissionSlugs)) {
                $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id')->toArray();
                $role->syncPermissions($permissionIds);
            }
        }

        // Create Super Admin user
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        
        $superAdmin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@hrms.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'role' => 'super_admin', // Legacy role field for compatibility
                'role_id' => $superAdminRole->id,
            ]
        );

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Super Admin user created: admin@hrms.com / password');
    }
}
