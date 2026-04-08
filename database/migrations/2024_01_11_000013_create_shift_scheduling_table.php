<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shift Roster
        Schema::create('shift_rosters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Shift Assignments
        Schema::create('shift_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roster_id')->constrained('shift_rosters')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_day_off')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['employee_id', 'date']);
        });

        // Shift Swap Requests
        Schema::create('shift_swap_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('requester_assignment_id')->constrained('shift_assignments')->cascadeOnDelete();
            $table->foreignId('swapper_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('swapper_assignment_id')->nullable()->constrained('shift_assignments')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'approved', 'declined'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_swap_requests');
        Schema::dropIfExists('shift_assignments');
        Schema::dropIfExists('shift_rosters');
    }
};
