<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Onboarding Templates
        Schema::create('onboarding_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('duration_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Onboarding Template Tasks
        Schema::create('onboarding_template_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('onboarding_templates')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('day_number'); // Day after joining
            $table->enum('task_type', ['document', 'training', 'meeting', 'system_access', 'other'])->default('other');
            $table->foreignId('assigned_to_role')->nullable()->comment('User ID for manager/mentor');
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();
        });

        // Employee Onboarding
        Schema::create('employee_onboarding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('onboarding_templates')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('expected_completion_date');
            $table->date('actual_completion_date')->nullable();
            $table->foreignId('buddy_id')->nullable()->constrained('employees')->comment('Onboarding buddy')->nullOnDelete();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'overdue'])->default('not_started');
            $table->integer('completion_percentage')->default(0);
            $table->timestamps();
        });

        // Employee Onboarding Tasks
        Schema::create('employee_onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onboarding_id')->constrained('employee_onboarding')->cascadeOnDelete();
            $table->foreignId('template_task_id')->constrained('onboarding_template_tasks')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->date('completed_date')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_onboarding_tasks');
        Schema::dropIfExists('employee_onboarding');
        Schema::dropIfExists('onboarding_template_tasks');
        Schema::dropIfExists('onboarding_templates');
    }
};
