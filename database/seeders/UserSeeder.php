<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get all roles for mapping
        $roles = Role::all()->keyBy('slug');
        
        // LEVEL 5: Super Admin - Can do anything
        User::updateOrCreate(
            ['email' => 'super@hrms.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'role_id' => $roles['super_admin']->id ?? null,
                'is_active' => true,
            ]
        );

        // LEVEL 4: HR Admin - Final approval authority
        User::updateOrCreate(
            ['email' => 'hradmin@hrms.com'],
            [
                'name' => 'HR Admin',
                'password' => Hash::make('password'),
                'role' => 'hr_admin',
                'role_id' => $roles['hr_admin']->id ?? null,
                'is_active' => true,
            ]
        );

        // LEVEL 3: Section Heads - First level approval (department-based)
        // Note: section_head is not in the new role system, defaulting to manager
        $sectionHeadRoleId = $roles['manager']->id ?? null;
        
        User::updateOrCreate(
            ['email' => 'finance.head@hrms.com'],
            [
                'name' => 'Finance Head',
                'password' => Hash::make('password'),
                'role' => 'section_head',
                'role_id' => $sectionHeadRoleId,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'it.head@hrms.com'],
            [
                'name' => 'IT Head',
                'password' => Hash::make('password'),
                'role' => 'section_head',
                'role_id' => $sectionHeadRoleId,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'hr.head@hrms.com'],
            [
                'name' => 'HR Head',
                'password' => Hash::make('password'),
                'role' => 'section_head',
                'role_id' => $sectionHeadRoleId,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'operations.head@hrms.com'],
            [
                'name' => 'Operations Head',
                'password' => Hash::make('password'),
                'role' => 'section_head',
                'role_id' => $sectionHeadRoleId,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'sales.head@hrms.com'],
            [
                'name' => 'Sales Head',
                'password' => Hash::make('password'),
                'role' => 'section_head',
                'role_id' => $sectionHeadRoleId,
                'is_active' => true,
            ]
        );

        // LEVEL 2: Managers - Team leads
        User::updateOrCreate(
            ['email' => 'manager@hrms.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'role_id' => $roles['manager']->id ?? null,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'team.lead@hrms.com'],
            [
                'name' => 'Team Lead',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'role_id' => $roles['manager']->id ?? null,
                'is_active' => true,
            ]
        );

        // LEVEL 1: Employees and Admin
        // Note: admin is not in the new role system, defaulting to hr_admin
        User::updateOrCreate(
            ['email' => 'admin@hrms.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'role_id' => $roles['hr_admin']->id ?? null,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'employee@hrms.com'],
            [
                'name' => 'Employee User',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'role_id' => $roles['employee']->id ?? null,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'john.doe@hrms.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'role_id' => $roles['employee']->id ?? null,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'jane.smith@hrms.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'role_id' => $roles['employee']->id ?? null,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mike.johnson@hrms.com'],
            [
                'name' => 'Mike Johnson',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'role_id' => $roles['employee']->id ?? null,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'sarah.williams@hrms.com'],
            [
                'name' => 'Sarah Williams',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'role_id' => $roles['employee']->id ?? null,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'david.brown@hrms.com'],
            [
                'name' => 'David Brown',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'role_id' => $roles['employee']->id ?? null,
                'is_active' => true,
            ]
        );
    }
}
