<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // File Categories
        Schema::create('file_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('file_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Employee Files
        Schema::create('employee_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained('file_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size'); // in bytes
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_confidential')->default(false);
            $table->date('expiry_date')->nullable();
            $table->integer('version')->default(1);
            $table->foreignId('parent_file_id')->nullable()->constrained('employee_files')->nullOnDelete();
            $table->timestamps();
        });

        // File Access Log
        Schema::create('file_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('employee_files')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('action', ['view', 'download', 'edit', 'delete'])->default('view');
            $table->timestamp('accessed_at');
            $table->timestamps();
        });

        // Calendar Events
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('event_type', ['meeting', 'training', 'interview', 'holiday', 'company_event', 'other'])->default('meeting');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_rule')->nullable(); // RRULE format
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Event Attendees
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('calendar_events')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['invited', 'accepted', 'declined', 'tentative'])->default('invited');
            $table->boolean('is_organizer')->default(false);
            $table->text('response_note')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            
            $table->unique(['event_id', 'employee_id']);
        });

        // Reminders
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('calendar_events')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->integer('remind_before_minutes'); // e.g., 15, 30, 60
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
        Schema::dropIfExists('event_attendees');
        Schema::dropIfExists('calendar_events');
        Schema::dropIfExists('file_access_logs');
        Schema::dropIfExists('employee_files');
        Schema::dropIfExists('file_categories');
    }
};
