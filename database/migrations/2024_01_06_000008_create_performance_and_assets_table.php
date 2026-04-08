<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Performance Review Cycles
        Schema::create('performance_review_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('year');
            $table->enum('period', ['quarterly', 'half_yearly', 'annual'])->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');
            $table->timestamps();
        });

        // Performance Reviews
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('review_cycle_id')->constrained('performance_review_cycles')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating')->nullable(); // 1-5
            $table->text('strengths')->nullable();
            $table->text('areas_of_improvement')->nullable();
            $table->text('goals_achieved')->nullable();
            $table->text('comments')->nullable();
            $table->text('employee_comments')->nullable();
            $table->enum('status', ['draft', 'submitted', 'acknowledged'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });

        // Goals/KPIs
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->date('start_date');
            $table->date('target_date');
            $table->integer('weight')->default(1); // For weighted scoring
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'cancelled'])->default('not_started');
            $table->integer('progress')->default(0); // 0-100
            $table->text('achievement_notes')->nullable();
            $table->foreignId('set_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Assets
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('asset_code')->unique();
            $table->enum('type', ['laptop', 'desktop', 'phone', 'tablet', 'furniture', 'other'])->default('other');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Asset Assignments
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('assigned_date');
            $table->date('return_date')->nullable();
            $table->text('condition_at_assignment')->nullable();
            $table->text('condition_at_return')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Announcements
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('goals');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('performance_review_cycles');
    }
};
