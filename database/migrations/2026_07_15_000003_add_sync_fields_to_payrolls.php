<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('unpaid_leave_days', 5, 2)->default(0)->after('leave_days');
            $table->decimal('half_days', 5, 2)->default(0)->after('unpaid_leave_days');
            $table->decimal('timesheet_hours', 8, 2)->default(0)->after('overtime_hours');
            $table->decimal('unpaid_leave_deduction', 12, 2)->default(0)->after('total_deductions');
            $table->decimal('absent_deduction', 12, 2)->default(0)->after('unpaid_leave_deduction');
            $table->decimal('sunday_allowance', 12, 2)->default(0)->after('bonus_amount');
            $table->decimal('holiday_allowance', 12, 2)->default(0)->after('sunday_allowance');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'unpaid_leave_days',
                'half_days',
                'timesheet_hours',
                'unpaid_leave_deduction',
                'absent_deduction',
                'sunday_allowance',
                'holiday_allowance',
            ]);
        });
    }
};
