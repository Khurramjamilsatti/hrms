<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns to attendances table for Sunday and special day tracking
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_weekend')->default(false)->after('status');
            $table->boolean('is_sunday')->default(false)->after('is_weekend');
            $table->boolean('is_holiday')->default(false)->after('is_sunday');
            $table->string('day_type')->nullable()->after('is_holiday')->comment('regular, sunday, holiday, special');
            $table->decimal('sunday_allowance', 10, 2)->nullable()->after('overtime_hours');
            $table->decimal('holiday_allowance', 10, 2)->nullable()->after('sunday_allowance');
        });

        // Update existing records to mark Sundays
        // PostgreSQL: 0 = Sunday, 6 = Saturday
        DB::statement("
            UPDATE attendances 
            SET is_sunday = CASE WHEN EXTRACT(DOW FROM date) = 0 THEN true ELSE false END,
                is_weekend = CASE WHEN EXTRACT(DOW FROM date) IN (0, 6) THEN true ELSE false END,
                day_type = CASE 
                    WHEN EXTRACT(DOW FROM date) = 0 THEN 'sunday'
                    WHEN EXTRACT(DOW FROM date) = 6 THEN 'saturday'
                    ELSE 'regular'
                END
        ");

        // Note: PostgreSQL doesn't need explicit ENUM modification
        // The status column will accept new values automatically
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'is_weekend',
                'is_sunday',
                'is_holiday',
                'day_type',
                'sunday_allowance',
                'holiday_allowance'
            ]);
        });
    }
};
