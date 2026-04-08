<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Training Courses
        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['technical', 'soft_skills', 'compliance', 'leadership', 'other'])->default('other');
            $table->integer('duration_hours');
            $table->foreignId('instructor_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('external_provider')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->enum('delivery_mode', ['online', 'classroom', 'hybrid', 'self_paced'])->default('online');
            $table->integer('max_participants')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Training Sessions
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('training_courses')->cascadeOnDelete();
            $table->string('session_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->integer('available_seats')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });

        // Training Enrollments
        Schema::create('training_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('training_sessions')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrolled_by')->constrained('users')->cascadeOnDelete();
            $table->date('enrolled_date');
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'failed', 'cancelled'])->default('enrolled');
            $table->date('completion_date')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('attendance_percentage')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('rating')->nullable(); // 1-5
            $table->boolean('certificate_issued')->default(false);
            $table->timestamps();
        });

        // Training Certificates
        Schema::create('training_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('training_enrollments')->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('certificate_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_certificates');
        Schema::dropIfExists('training_enrollments');
        Schema::dropIfExists('training_sessions');
        Schema::dropIfExists('training_courses');
    }
};
