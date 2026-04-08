<?php

namespace Database\Seeders;

use App\Models\SalaryComponent;
use Illuminate\Database\Seeder;

class SalaryComponentSeeder extends Seeder
{
    public function run(): void
    {
        $components = [
            // Core Earnings - Allowances
            [
                'name' => 'House Rent Allowance',
                'code' => 'HRA',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Transport Allowance',
                'code' => 'TRANSPORT',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Medical Allowance',
                'code' => 'MEDICAL',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Food Allowance',
                'code' => 'FOOD',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Mobile/Phone Allowance',
                'code' => 'PHONE',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Fuel Allowance',
                'code' => 'FUEL',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Conveyance Allowance',
                'code' => 'CONVEYANCE',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Internet Allowance',
                'code' => 'INTERNET',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Special Allowance',
                'code' => 'SPECIAL',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Education Allowance',
                'code' => 'EDUCATION',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Entertainment Allowance',
                'code' => 'ENTERTAINMENT',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
            ],
            
            // Bonuses & Incentives
            [
                'name' => 'Overtime Allowance',
                'code' => 'OVERTIME',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Performance Bonus',
                'code' => 'PERF_BONUS',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Annual Bonus',
                'code' => 'ANNUAL_BONUS',
                'type' => 'earning',
                'calculation_type' => 'percentage',
                'is_taxable' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Leave Encashment',
                'code' => 'LEAVE_ENCASH',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => true,
                'is_active' => true,
            ],
            
            // Deductions - Mandatory
            [
                'name' => 'Income Tax',
                'code' => 'INCOME_TAX',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Provident Fund',
                'code' => 'PF',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'name' => 'EOBI',
                'code' => 'EOBI',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Professional Tax',
                'code' => 'PROF_TAX',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'is_mandatory' => false,
                'is_active' => true,
            ],
            
            // Deductions - Others
            [
                'name' => 'Late Deduction',
                'code' => 'LATE_DED',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'is_mandatory' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Absence Deduction',
                'code' => 'ABSENCE_DED',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'is_mandatory' => false,
                'is_active' => true,
            ],
        ];

        foreach ($components as $component) {
            SalaryComponent::updateOrCreate(
                ['code' => $component['code']],
                $component
            );
        }
    }
}
