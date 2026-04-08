<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Roles & Permissions (must run first)
            RolesAndPermissionsSeeder::class,
            
            // Core Setup
            UserSeeder::class,
            DepartmentSeeder::class,
            DesignationSeeder::class,
            EmployeeSeeder::class,
            LeaveTypeSeeder::class,
            SalaryComponentSeeder::class,
            EmployeeSalarySeeder::class,
            ShiftSeeder::class,
            
            // Attendance & Time Tracking
            AttendanceSeeder::class,
            
            // Leave Applications
            LeaveApplicationSeeder::class,
            
            // Financial Modules
            LoanSeeder::class,
            
            // HR Modules
            CvBankSeeder::class,
            DeploymentSeeder::class,
            
            // New Modules
            TimesheetSeeder::class,
            OnboardingSeeder::class,
            TrainingSeeder::class,
            TravelExpenseSeeder::class,
            ShiftSchedulingSeeder::class,
            HelpdeskSeeder::class,
            FileManagementSeeder::class,
            CalendarSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
