<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix advance_type constraint to include salary advance types
        DB::statement("ALTER TABLE advance_requests DROP CONSTRAINT IF EXISTS advance_requests_advance_type_check");
        DB::statement("ALTER TABLE advance_requests ADD CONSTRAINT advance_requests_advance_type_check CHECK (advance_type IN ('travel', 'salary', 'emergency_salary', 'festival', 'project', 'emergency'))");
        
        // Add code column to salary_components if it doesn't exist
        if (!Schema::hasColumn('salary_components', 'code')) {
            Schema::table('salary_components', function (Blueprint $table) {
                $table->string('code')->nullable()->after('name');
            });
        }
        
        // Update existing components with codes
        $existingComponents = [
            'House Rent Allowance' => 'HRA',
            'Transport Allowance' => 'TRANSPORT',
            'Medical Allowance' => 'MEDICAL',
            'Income Tax' => 'INCOME_TAX',
            'Provident Fund' => 'PF',
            'EOBI' => 'EOBI',
        ];
        
        foreach ($existingComponents as $name => $code) {
            DB::table('salary_components')
                ->where('name', $name)
                ->whereNull('code')
                ->update(['code' => $code]);
        }
        
        // Add more salary components to salary_components table
        $newComponents = [
            [
                'name' => 'Food Allowance',
                'code' => 'FOOD',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mobile/Phone Allowance',
                'code' => 'PHONE',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fuel Allowance',
                'code' => 'FUEL',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Conveyance Allowance',
                'code' => 'CONVEYANCE',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Internet Allowance',
                'code' => 'INTERNET',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Special Allowance',
                'code' => 'SPECIAL',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Education Allowance',
                'code' => 'EDUCATION',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Entertainment Allowance',
                'code' => 'ENTERTAINMENT',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Overtime Allowance',
                'code' => 'OVERTIME',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Performance Bonus',
                'code' => 'PERF_BONUS',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Annual Bonus',
                'code' => 'ANNUAL_BONUS',
                'type' => 'earning',
                'calculation_type' => 'percentage',
                'is_taxable' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leave Encashment',
                'code' => 'LEAVE_ENCASH',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_taxable' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Late Deduction',
                'code' => 'LATE_DED',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'is_mandatory' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Absence Deduction',
                'code' => 'ABSENCE_DED',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'is_mandatory' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional Tax',
                'code' => 'PROF_TAX',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'is_mandatory' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($newComponents as $component) {
            // Check if component doesn't already exist
            $exists = DB::table('salary_components')
                ->where('code', $component['code'])
                ->exists();
                
            if (!$exists) {
                DB::table('salary_components')->insert($component);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert advance_type constraint
        DB::statement("ALTER TABLE advance_requests DROP CONSTRAINT IF EXISTS advance_requests_advance_type_check");
        DB::statement("ALTER TABLE advance_requests ADD CONSTRAINT advance_requests_advance_type_check CHECK (advance_type IN ('travel', 'salary', 'project', 'emergency'))");
        
        // Remove added salary components
        DB::table('salary_components')->whereIn('code', [
            'FOOD_ALL', 'PHONE_ALL', 'FUEL_ALL', 'CONV_ALL', 'INTERNET_ALL',
            'SPECIAL_ALL', 'EDU_ALL', 'ENT_ALL', 'OT_ALL', 'PERF_BONUS',
            'ANN_BONUS', 'LEAVE_ENCASH', 'LATE_DED', 'ABSENCE_DED', 'PROF_TAX'
        ])->delete();
    }
};
