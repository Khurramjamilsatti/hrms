<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Job Positions
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('designation_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description');
            $table->text('requirements');
            $table->integer('vacancies')->default(1);
            $table->decimal('min_salary', 12, 2)->nullable();
            $table->decimal('max_salary', 12, 2)->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            $table->string('location')->nullable();
            $table->date('application_deadline')->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'filled'])->default('draft');
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Job Applications
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_position_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('resume_path');
            $table->string('cover_letter_path')->nullable();
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->date('available_from')->nullable();
            $table->enum('status', ['applied', 'screening', 'interview', 'offered', 'hired', 'rejected'])->default('applied');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Interviews
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->datetime('scheduled_at');
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->text('agenda')->nullable();
            $table->foreignId('interviewer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->integer('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamps();
        });

        // Offers
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->onDelete('cascade');
            $table->decimal('offered_salary', 12, 2);
            $table->date('joining_date');
            $table->text('terms')->nullable();
            $table->string('offer_letter_path')->nullable();
            $table->enum('status', ['sent', 'accepted', 'rejected', 'expired'])->default('sent');
            $table->date('valid_until')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_positions');
    }
};
