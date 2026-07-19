<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('category', 20)->default('short_leave'); // short_leave | exemption
            $table->string('exemption_type', 30)->nullable(); // late_arrival | early_departure | missed_punch | official_duty | other
            $table->date('date');
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->text('reason');
            $table->string('status', 20)->default('pending'); // pending | approved | rejected | cancelled
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'date']);
            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_leaves');
    }
};
