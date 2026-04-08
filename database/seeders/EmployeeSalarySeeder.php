<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalaryComponent;
use App\Models\SalaryComponent;
use Illuminate\Database\Seeder;

class EmployeeSalarySeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $earningComponents = SalaryComponent::where('type', 'earning')->where('is_active', true)->get();
        $deductionComponents = SalaryComponent::where('type', 'deduction')->where('is_active', true)->get();

        // Salary ranges per employee (in PKR)
        $salaryData = [
            1 => 150000, // Admin - Rs. 150,000
            2 => 120000, // Manager - Rs. 120,000
            3 => 80000,  // Employee - Rs. 80,000
        ];

        foreach ($employees as $employee) {
            $basicSalary = $salaryData[$employee->id] ?? 75000;

            // Create employee salary record effective from their joining date
            $salary = EmployeeSalary::create([
                'employee_id' => $employee->id,
                'basic_salary' => $basicSalary,
                'effective_from' => $employee->joining_date ?? now()->subYear(),
                'effective_to' => null,
                'payment_mode' => 'bank_transfer',
                'bank_name' => 'HBL',
                'account_number' => '0010' . str_pad($employee->id, 8, '0', STR_PAD_LEFT),
            ]);

            // Add earning components
            foreach ($earningComponents as $component) {
                $amount = match ($component->name) {
                    'House Rent Allowance' => round($basicSalary * 0.45),  // 45% of basic
                    'Transport Allowance' => 5000,
                    'Medical Allowance' => round($basicSalary * 0.10),     // 10% of basic
                    default => round($basicSalary * 0.05),
                };

                EmployeeSalaryComponent::create([
                    'employee_salary_id' => $salary->id,
                    'salary_component_id' => $component->id,
                    'amount' => $amount,
                    'percentage' => null,
                ]);
            }

            // Add deduction components
            foreach ($deductionComponents as $component) {
                $amount = match ($component->name) {
                    'Income Tax' => round($basicSalary * 0.05),    // 5% tax
                    'Provident Fund' => round($basicSalary * 0.08), // 8% PF
                    'EOBI' => 350,                                   // Fixed EOBI
                    default => round($basicSalary * 0.02),
                };

                EmployeeSalaryComponent::create([
                    'employee_salary_id' => $salary->id,
                    'salary_component_id' => $component->id,
                    'amount' => $amount,
                    'percentage' => null,
                ]);
            }
        }
    }
}
