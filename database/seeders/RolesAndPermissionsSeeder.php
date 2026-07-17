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
            ['name' => 'Update Employee', 'slug' => 'employees.update', 'module' => 'employees', 'action' => 'update'],
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
            ['name' => 'Manage Leave Types & Balances', 'slug' => 'leaves.manage', 'module' => 'leaves', 'action' => 'manage'],

            // Overtime
            ['name' => 'View Overtime', 'slug' => 'overtime.view', 'module' => 'overtime', 'action' => 'view'],
            ['name' => 'Create Overtime', 'slug' => 'overtime.create', 'module' => 'overtime', 'action' => 'create'],
            ['name' => 'Approve Overtime', 'slug' => 'overtime.approve', 'module' => 'overtime', 'action' => 'approve'],

            // Payroll
            ['name' => 'View Payroll', 'slug' => 'payroll.view', 'module' => 'payroll', 'action' => 'view'],
            ['name' => 'Generate Payroll', 'slug' => 'payroll.generate', 'module' => 'payroll', 'action' => 'generate'],
            ['name' => 'Create Payroll', 'slug' => 'payroll.create', 'module' => 'payroll', 'action' => 'create'],
            ['name' => 'Process Payroll', 'slug' => 'payroll.process', 'module' => 'payroll', 'action' => 'process'],
            ['name' => 'Manage Payroll', 'slug' => 'payroll.manage', 'module' => 'payroll', 'action' => 'manage'],
            ['name' => 'View Own Payroll', 'slug' => 'payroll.view_own', 'module' => 'payroll', 'action' => 'view_own'],

            // Departments
            ['name' => 'View Departments', 'slug' => 'departments.view', 'module' => 'departments', 'action' => 'view'],
            ['name' => 'Create Department', 'slug' => 'departments.create', 'module' => 'departments', 'action' => 'create'],
            ['name' => 'Edit Department', 'slug' => 'departments.edit', 'module' => 'departments', 'action' => 'edit'],
            ['name' => 'Update Department', 'slug' => 'departments.update', 'module' => 'departments', 'action' => 'update'],
            ['name' => 'Delete Department', 'slug' => 'departments.delete', 'module' => 'departments', 'action' => 'delete'],

            // Recruitment
            ['name' => 'View Recruitment', 'slug' => 'recruitment.view', 'module' => 'recruitment', 'action' => 'view'],
            ['name' => 'Create Recruitment', 'slug' => 'recruitment.create', 'module' => 'recruitment', 'action' => 'create'],
            ['name' => 'Update Recruitment', 'slug' => 'recruitment.update', 'module' => 'recruitment', 'action' => 'update'],
            ['name' => 'Delete Recruitment', 'slug' => 'recruitment.delete', 'module' => 'recruitment', 'action' => 'delete'],
            ['name' => 'Manage Recruitment', 'slug' => 'recruitment.manage', 'module' => 'recruitment', 'action' => 'manage'],
            ['name' => 'Manage Positions', 'slug' => 'recruitment.positions', 'module' => 'recruitment', 'action' => 'positions'],
            ['name' => 'Manage Applications', 'slug' => 'recruitment.applications', 'module' => 'recruitment', 'action' => 'applications'],

            // Performance
            ['name' => 'View Performance', 'slug' => 'performance.view', 'module' => 'performance', 'action' => 'view'],
            ['name' => 'Create Performance', 'slug' => 'performance.create', 'module' => 'performance', 'action' => 'create'],
            ['name' => 'Update Performance', 'slug' => 'performance.update', 'module' => 'performance', 'action' => 'update'],
            ['name' => 'Manage Performance', 'slug' => 'performance.manage', 'module' => 'performance', 'action' => 'manage'],
            ['name' => 'Manage Reviews', 'slug' => 'performance.reviews', 'module' => 'performance', 'action' => 'reviews'],
            ['name' => 'Manage Goals', 'slug' => 'performance.goals', 'module' => 'performance', 'action' => 'goals'],

            // Assets
            ['name' => 'View Assets', 'slug' => 'assets.view', 'module' => 'assets', 'action' => 'view'],
            ['name' => 'Create Assets', 'slug' => 'assets.create', 'module' => 'assets', 'action' => 'create'],
            ['name' => 'Update Assets', 'slug' => 'assets.update', 'module' => 'assets', 'action' => 'update'],
            ['name' => 'Delete Assets', 'slug' => 'assets.delete', 'module' => 'assets', 'action' => 'delete'],
            ['name' => 'Manage Assets', 'slug' => 'assets.manage', 'module' => 'assets', 'action' => 'manage'],
            ['name' => 'Assign Assets', 'slug' => 'assets.assign', 'module' => 'assets', 'action' => 'assign'],

            // Announcements
            ['name' => 'View Announcements', 'slug' => 'announcements.view', 'module' => 'announcements', 'action' => 'view'],
            ['name' => 'Create Announcement', 'slug' => 'announcements.create', 'module' => 'announcements', 'action' => 'create'],
            ['name' => 'Edit Announcement', 'slug' => 'announcements.edit', 'module' => 'announcements', 'action' => 'edit'],
            ['name' => 'Update Announcement', 'slug' => 'announcements.update', 'module' => 'announcements', 'action' => 'update'],
            ['name' => 'Delete Announcement', 'slug' => 'announcements.delete', 'module' => 'announcements', 'action' => 'delete'],

            // Landing Page CMS
            ['name' => 'View Landing CMS', 'slug' => 'cms.view', 'module' => 'cms', 'action' => 'view'],
            ['name' => 'Update Landing CMS', 'slug' => 'cms.update', 'module' => 'cms', 'action' => 'update'],

            // Timesheets
            ['name' => 'View Timesheets', 'slug' => 'timesheets.view', 'module' => 'timesheets', 'action' => 'view'],
            ['name' => 'Create Timesheet', 'slug' => 'timesheets.create', 'module' => 'timesheets', 'action' => 'create'],
            ['name' => 'Update Timesheet', 'slug' => 'timesheets.update', 'module' => 'timesheets', 'action' => 'update'],
            ['name' => 'Submit Timesheet', 'slug' => 'timesheets.submit', 'module' => 'timesheets', 'action' => 'submit'],
            ['name' => 'Approve Timesheet', 'slug' => 'timesheets.approve', 'module' => 'timesheets', 'action' => 'approve'],
            ['name' => 'Manage Timesheets', 'slug' => 'timesheets.manage', 'module' => 'timesheets', 'action' => 'manage'],
            ['name' => 'Manage Projects', 'slug' => 'timesheets.projects', 'module' => 'timesheets', 'action' => 'projects'],

            // Onboarding
            ['name' => 'View Onboarding', 'slug' => 'onboarding.view', 'module' => 'onboarding', 'action' => 'view'],
            ['name' => 'Create Onboarding', 'slug' => 'onboarding.create', 'module' => 'onboarding', 'action' => 'create'],
            ['name' => 'Update Onboarding', 'slug' => 'onboarding.update', 'module' => 'onboarding', 'action' => 'update'],
            ['name' => 'Delete Onboarding', 'slug' => 'onboarding.delete', 'module' => 'onboarding', 'action' => 'delete'],
            ['name' => 'Manage Onboarding', 'slug' => 'onboarding.manage', 'module' => 'onboarding', 'action' => 'manage'],

            // Training
            ['name' => 'View Training', 'slug' => 'training.view', 'module' => 'training', 'action' => 'view'],
            ['name' => 'Create Training', 'slug' => 'training.create', 'module' => 'training', 'action' => 'create'],
            ['name' => 'Update Training', 'slug' => 'training.update', 'module' => 'training', 'action' => 'update'],
            ['name' => 'Delete Training', 'slug' => 'training.delete', 'module' => 'training', 'action' => 'delete'],
            ['name' => 'Manage Training', 'slug' => 'training.manage', 'module' => 'training', 'action' => 'manage'],
            ['name' => 'Enroll Training', 'slug' => 'training.enroll', 'module' => 'training', 'action' => 'enroll'],

            // Travel & Expenses
            ['name' => 'View Travel & Expenses', 'slug' => 'travel.view', 'module' => 'travel', 'action' => 'view'],
            ['name' => 'Create Travel Request', 'slug' => 'travel.create', 'module' => 'travel', 'action' => 'create'],
            ['name' => 'Update Travel Request', 'slug' => 'travel.update', 'module' => 'travel', 'action' => 'update'],
            ['name' => 'Submit Travel Request', 'slug' => 'travel.submit', 'module' => 'travel', 'action' => 'submit'],
            ['name' => 'Approve Travel', 'slug' => 'travel.approve', 'module' => 'travel', 'action' => 'approve'],
            ['name' => 'Manage Travel', 'slug' => 'travel.manage', 'module' => 'travel', 'action' => 'manage'],
            ['name' => 'Submit Expense', 'slug' => 'travel.expense', 'module' => 'travel', 'action' => 'expense'],

            // Shifts
            ['name' => 'View Shifts', 'slug' => 'shifts.view', 'module' => 'shifts', 'action' => 'view'],
            ['name' => 'Create Shifts', 'slug' => 'shifts.create', 'module' => 'shifts', 'action' => 'create'],
            ['name' => 'Update Shifts', 'slug' => 'shifts.update', 'module' => 'shifts', 'action' => 'update'],
            ['name' => 'Delete Shifts', 'slug' => 'shifts.delete', 'module' => 'shifts', 'action' => 'delete'],
            ['name' => 'Manage Shifts', 'slug' => 'shifts.manage', 'module' => 'shifts', 'action' => 'manage'],
            ['name' => 'Assign Shifts', 'slug' => 'shifts.assign', 'module' => 'shifts', 'action' => 'assign'],

            // Helpdesk
            ['name' => 'View Tickets', 'slug' => 'helpdesk.view', 'module' => 'helpdesk', 'action' => 'view'],
            ['name' => 'Create Ticket', 'slug' => 'helpdesk.create', 'module' => 'helpdesk', 'action' => 'create'],
            ['name' => 'Update Ticket', 'slug' => 'helpdesk.update', 'module' => 'helpdesk', 'action' => 'update'],
            ['name' => 'Delete Ticket', 'slug' => 'helpdesk.delete', 'module' => 'helpdesk', 'action' => 'delete'],
            ['name' => 'Manage Tickets', 'slug' => 'helpdesk.manage', 'module' => 'helpdesk', 'action' => 'manage'],

            // Files
            ['name' => 'View Files', 'slug' => 'files.view', 'module' => 'files', 'action' => 'view'],
            ['name' => 'Create Files', 'slug' => 'files.create', 'module' => 'files', 'action' => 'create'],
            ['name' => 'Update Files', 'slug' => 'files.update', 'module' => 'files', 'action' => 'update'],
            ['name' => 'Delete Files', 'slug' => 'files.delete', 'module' => 'files', 'action' => 'delete'],
            ['name' => 'Upload Files', 'slug' => 'files.upload', 'module' => 'files', 'action' => 'upload'],
            ['name' => 'Manage Files', 'slug' => 'files.manage', 'module' => 'files', 'action' => 'manage'],

            // Calendar
            ['name' => 'View Calendar', 'slug' => 'calendar.view', 'module' => 'calendar', 'action' => 'view'],
            ['name' => 'Create Calendar Events', 'slug' => 'calendar.create', 'module' => 'calendar', 'action' => 'create'],
            ['name' => 'Update Calendar Events', 'slug' => 'calendar.update', 'module' => 'calendar', 'action' => 'update'],
            ['name' => 'Delete Calendar Events', 'slug' => 'calendar.delete', 'module' => 'calendar', 'action' => 'delete'],
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
                    'employees.view', 'employees.create', 'employees.edit', 'employees.update', 'employees.delete',
                    'attendance.view', 'attendance.checkin', 'attendance.manage', 'attendance.reports',
                    'leaves.view', 'leaves.apply', 'leaves.approve', 'leaves.reject', 'leaves.cancel', 'leaves.manage',
                    'overtime.view', 'overtime.create', 'overtime.approve',
                    'payroll.view', 'payroll.generate', 'payroll.create', 'payroll.process', 'payroll.manage',
                    'departments.view', 'departments.create', 'departments.edit', 'departments.update', 'departments.delete',
                    'recruitment.view', 'recruitment.create', 'recruitment.update', 'recruitment.delete', 'recruitment.manage', 'recruitment.positions', 'recruitment.applications',
                    'performance.view', 'performance.create', 'performance.update', 'performance.manage', 'performance.reviews', 'performance.goals',
                    'assets.view', 'assets.create', 'assets.update', 'assets.delete', 'assets.manage', 'assets.assign',
                    'announcements.view', 'announcements.create', 'announcements.edit', 'announcements.update', 'announcements.delete',
                    'timesheets.view', 'timesheets.create', 'timesheets.update', 'timesheets.submit', 'timesheets.approve', 'timesheets.manage', 'timesheets.projects',
                    'onboarding.view', 'onboarding.create', 'onboarding.update', 'onboarding.delete', 'onboarding.manage',
                    'training.view', 'training.create', 'training.update', 'training.delete', 'training.manage', 'training.enroll',
                    'travel.view', 'travel.create', 'travel.update', 'travel.submit', 'travel.approve', 'travel.manage', 'travel.expense',
                    'shifts.view', 'shifts.create', 'shifts.update', 'shifts.delete', 'shifts.manage', 'shifts.assign',
                    'helpdesk.view', 'helpdesk.create', 'helpdesk.update', 'helpdesk.delete', 'helpdesk.manage',
                    'files.view', 'files.create', 'files.update', 'files.delete', 'files.upload', 'files.manage',
                    'calendar.view', 'calendar.create', 'calendar.update', 'calendar.delete', 'calendar.manage',
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
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Legacy admin role with full HR operational access',
                'is_system_role' => true,
                'is_active' => true,
                'permissions' => [
                    'dashboard.view', 'dashboard.stats',
                    'employees.view', 'employees.create', 'employees.edit', 'employees.update', 'employees.delete',
                    'attendance.view', 'attendance.checkin', 'attendance.manage', 'attendance.reports',
                    'leaves.view', 'leaves.apply', 'leaves.approve', 'leaves.reject', 'leaves.cancel', 'leaves.manage',
                    'overtime.view', 'overtime.create', 'overtime.approve',
                    'payroll.view', 'payroll.generate', 'payroll.create', 'payroll.process', 'payroll.manage',
                    'departments.view', 'departments.create', 'departments.edit', 'departments.update', 'departments.delete',
                    'recruitment.view', 'recruitment.create', 'recruitment.update', 'recruitment.delete', 'recruitment.manage',
                    'performance.view', 'performance.create', 'performance.update', 'performance.manage', 'performance.reviews', 'performance.goals',
                    'assets.view', 'assets.create', 'assets.update', 'assets.delete', 'assets.manage', 'assets.assign',
                    'announcements.view',
                    'timesheets.view', 'timesheets.create', 'timesheets.update', 'timesheets.submit', 'timesheets.approve', 'timesheets.manage',
                    'onboarding.view', 'onboarding.create', 'onboarding.update', 'onboarding.delete', 'onboarding.manage',
                    'training.view', 'training.create', 'training.update', 'training.delete', 'training.manage', 'training.enroll',
                    'travel.view', 'travel.create', 'travel.update', 'travel.submit', 'travel.approve', 'travel.manage', 'travel.expense',
                    'shifts.view', 'shifts.create', 'shifts.update', 'shifts.delete', 'shifts.manage', 'shifts.assign',
                    'helpdesk.view', 'helpdesk.create', 'helpdesk.update', 'helpdesk.delete', 'helpdesk.manage',
                    'files.view', 'files.create', 'files.update', 'files.delete', 'files.upload', 'files.manage',
                    'calendar.view', 'calendar.create', 'calendar.update', 'calendar.delete', 'calendar.manage',
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
                'name' => 'Section Head',
                'slug' => 'section_head',
                'description' => 'Section head with first-level leave and team approvals',
                'is_system_role' => true,
                'is_active' => true,
                'permissions' => [
                    'dashboard.view', 'dashboard.stats',
                    'employees.view', 'employees.view_own',
                    'attendance.view', 'attendance.checkin', 'attendance.reports',
                    'leaves.view', 'leaves.apply', 'leaves.approve', 'leaves.reject', 'leaves.cancel',
                    'overtime.view', 'overtime.create', 'overtime.approve',
                    'departments.view',
                    'performance.view', 'performance.reviews',
                    'announcements.view',
                    'timesheets.view', 'timesheets.create', 'timesheets.update', 'timesheets.submit', 'timesheets.approve',
                    'training.view',
                    'travel.view', 'travel.create', 'travel.update', 'travel.submit', 'travel.approve',
                    'shifts.view', 'shifts.assign',
                    'helpdesk.view', 'helpdesk.create',
                    'files.view', 'files.create', 'files.upload',
                    'calendar.view',
                    'notifications.view',
                    'organization.view',
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
                    'overtime.view', 'overtime.create', 'overtime.approve',
                    'payroll.view_own',
                    'departments.view',
                    'performance.view', 'performance.create', 'performance.update', 'performance.reviews',
                    'assets.view',
                    'announcements.view',
                    'timesheets.view', 'timesheets.create', 'timesheets.update', 'timesheets.submit', 'timesheets.approve',
                    'training.view',
                    'travel.view', 'travel.create', 'travel.update', 'travel.submit', 'travel.approve', 'travel.expense',
                    'shifts.view', 'shifts.assign',
                    'helpdesk.view', 'helpdesk.create',
                    'files.view', 'files.create', 'files.upload',
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
                    'overtime.view', 'overtime.create',
                    'payroll.view_own',
                    'performance.view',
                    'announcements.view',
                    'timesheets.view', 'timesheets.create', 'timesheets.update', 'timesheets.submit',
                    'training.view',
                    'travel.view', 'travel.create', 'travel.update', 'travel.submit', 'travel.expense',
                    'shifts.view',
                    'helpdesk.view', 'helpdesk.create',
                    'files.view', 'files.create', 'files.upload',
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

        // Sync legacy users.role string to roles.id so permission checks work
        foreach (Role::all() as $role) {
            \App\Models\User::where('role', $role->slug)
                ->where(function ($q) use ($role) {
                    $q->whereNull('role_id')->orWhere('role_id', '!=', $role->id);
                })
                ->update(['role_id' => $role->id]);
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

        if ($superAdminRole && $superAdmin->role_id !== $superAdminRole->id) {
            $superAdmin->update([
                'role' => 'super_admin',
                'role_id' => $superAdminRole->id,
            ]);
        }

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Super Admin user created: admin@hrms.com / password');
    }
}
