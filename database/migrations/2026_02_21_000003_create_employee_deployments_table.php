<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('deployment_number')->unique();
            $table->enum('deployment_type', ['domestic', 'international', 'project', 'temporary', 'permanent'])->default('domestic');
            $table->string('project_name')->nullable();
            $table->string('client_name')->nullable();
            $table->string('location');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'active', 'completed', 'cancelled', 'extended'])->default('draft');
            $table->text('purpose')->nullable();
            $table->string('role')->nullable();
            $table->string('reporting_manager')->nullable();
            $table->string('accommodation_type')->nullable();
            $table->text('accommodation_details')->nullable();
            $table->text('transport_details')->nullable();
            $table->decimal('allowance_amount', 12, 2)->nullable();
            $table->enum('travel_ticket_status', ['pending', 'booked', 'issued', 'used', 'cancelled'])->nullable();
            $table->enum('visa_status', ['not_required', 'pending', 'in_process', 'approved', 'rejected', 'issued'])->nullable();
            $table->enum('insurance_status', ['pending', 'active', 'expired', 'not_required'])->nullable();
            $table->boolean('departure_from_long_leave')->default(false);
            $table->date('long_leave_start_date')->nullable();
            $table->date('long_leave_end_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->integer('extension_count')->default(0);
            $table->date('current_extension_end_date')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index('deployment_number');
        });

        Schema::create('deployment_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deployment_id')->constrained('employee_deployments')->onDelete('cascade');
            $table->integer('extension_number');
            $table->date('previous_end_date');
            $table->date('new_end_date');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deployment_extensions');
        Schema::dropIfExists('employee_deployments');
    }
};
