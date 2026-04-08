<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enhance Travel Requests table
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->string('project_code')->nullable()->after('purpose');
            $table->string('client_name')->nullable()->after('project_code');
            $table->enum('travel_class', ['economy', 'business', 'first'])->default('economy')->after('travel_mode');
            $table->enum('accommodation_type', ['budget', 'standard', 'premium'])->nullable()->after('travel_class');
            $table->integer('number_of_travelers')->default(1)->after('accommodation_type');
            $table->json('travelers')->nullable()->after('number_of_travelers'); // Names of other travelers
            $table->boolean('accommodation_required')->default(false)->after('travelers');
            $table->string('hotel_name')->nullable()->after('accommodation_required');
            $table->decimal('accommodation_cost', 10, 2)->nullable()->after('hotel_name');
            $table->boolean('visa_required')->default(false)->after('accommodation_cost');
            $table->decimal('visa_cost', 10, 2)->nullable()->after('visa_required');
            $table->boolean('insurance_required')->default(false)->after('visa_cost');
            $table->decimal('insurance_cost', 10, 2)->nullable()->after('insurance_required');
            $table->decimal('per_diem_rate', 10, 2)->nullable()->after('insurance_cost');
            $table->integer('number_of_days')->nullable()->after('per_diem_rate');
            $table->decimal('total_per_diem', 10, 2)->nullable()->after('number_of_days');
            $table->text('itinerary')->nullable()->after('total_per_diem');
            $table->boolean('billable_to_client')->default(false)->after('itinerary');
            $table->string('cost_center')->nullable()->after('billable_to_client');
            $table->decimal('actual_cost', 10, 2)->nullable()->after('estimated_cost');
            $table->text('admin_notes')->nullable()->after('rejection_reason');
            $table->foreignId('secondary_approver_id')->nullable()->after('approved_by')->constrained('users')->nullOnDelete();
            $table->timestamp('secondary_approved_at')->nullable()->after('approved_at');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('status');
        });

        // Enhance Expense Categories table
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->string('category_code')->unique()->nullable()->after('name');
            $table->decimal('per_diem_rate', 10, 2)->nullable()->after('max_amount');
            $table->boolean('requires_approval')->default(true)->after('requires_receipt');
            $table->boolean('is_taxable')->default(false)->after('requires_approval');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('is_taxable');
            $table->integer('approval_threshold')->nullable()->after('tax_rate'); // Auto-approve below this amount
            $table->string('gl_account')->nullable()->after('approval_threshold'); // General Ledger account code
        });

        // Enhance Expense Claims table
        Schema::table('expense_claims', function (Blueprint $table) {
            $table->string('project_code')->nullable()->after('travel_request_id');
            $table->string('cost_center')->nullable()->after('project_code');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'other'])->default('cash')->after('expense_date');
            $table->string('reference_number')->nullable()->after('payment_method'); // Invoice/bill number
            $table->decimal('distance_km', 10, 2)->nullable()->after('amount'); // For mileage claims
            $table->decimal('rate_per_km', 10, 2)->nullable()->after('distance_km');
            $table->decimal('tax_amount', 10, 2)->nullable()->after('currency');
            $table->decimal('total_amount', 10, 2)->nullable()->after('tax_amount');
            $table->boolean('is_reimbursable')->default(true)->after('total_amount');
            $table->boolean('billable_to_client')->default(false)->after('is_reimbursable');
            $table->string('client_name')->nullable()->after('billable_to_client');
            $table->text('admin_notes')->nullable()->after('rejection_reason');
            $table->foreignId('secondary_approver_id')->nullable()->after('approved_by')->constrained('users')->nullOnDelete();
            $table->timestamp('secondary_approved_at')->nullable()->after('approved_at');
            $table->string('payment_reference')->nullable()->after('payment_date');
            $table->enum('reimbursement_method', ['bank_transfer', 'cash', 'salary', 'other'])->default('bank_transfer')->after('payment_reference');
        });

        // Enhance Advance Requests table
        Schema::table('advance_requests', function (Blueprint $table) {
            $table->string('project_code')->nullable()->after('travel_request_id');
            $table->enum('advance_type', ['travel', 'salary', 'project', 'emergency'])->default('travel')->after('purpose');
            $table->integer('installments')->default(1)->after('amount');
            $table->decimal('installment_amount', 10, 2)->nullable()->after('installments');
            $table->date('first_deduction_date')->nullable()->after('installment_amount');
            $table->decimal('deducted_amount', 10, 2)->default(0)->after('settled_amount');
            $table->decimal('balance_amount', 10, 2)->nullable()->after('deducted_amount');
            $table->text('deduction_notes')->nullable()->after('balance_amount');
            $table->text('admin_notes')->nullable()->after('deduction_notes');
            $table->string('payment_reference')->nullable()->after('payment_date');
            $table->enum('payment_method', ['bank_transfer', 'cash', 'cheque'])->default('bank_transfer')->after('payment_reference');
        });

        // Create Travel Policies table
        Schema::create('travel_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_name');
            $table->text('description')->nullable();
            $table->enum('travel_type', ['domestic', 'international'])->default('domestic');
            $table->enum('designation_level', ['executive', 'senior', 'mid', 'junior', 'all'])->default('all');
            $table->decimal('max_flight_cost', 10, 2)->nullable();
            $table->decimal('max_hotel_per_night', 10, 2)->nullable();
            $table->decimal('per_diem_rate', 10, 2)->nullable();
            $table->decimal('mileage_rate_per_km', 10, 2)->nullable();
            $table->boolean('advance_allowed')->default(true);
            $table->decimal('max_advance_percentage', 5, 2)->default(80.00);
            $table->integer('advance_days_before_travel')->default(7);
            $table->integer('settlement_days_after_return')->default(15);
            $table->boolean('requires_manager_approval')->default(true);
            $table->boolean('requires_finance_approval')->default(false);
            $table->decimal('finance_approval_threshold', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create Expense Attachments table for multiple receipt uploads
        Schema::create('expense_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_claim_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create Advance Deductions table to track installment payments
        Schema::create('advance_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advance_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_id')->nullable()->constrained()->nullOnDelete();
            $table->date('deduction_date');
            $table->decimal('amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Create Travel Itinerary Items table
        Schema::create('travel_itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_request_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->string('from_location');
            $table->string('to_location');
            $table->enum('transport_mode', ['flight', 'train', 'bus', 'car', 'taxi', 'other']);
            $table->string('booking_reference')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        // Create Mileage Claims table for vehicle usage
        Schema::create('mileage_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_claim_id')->nullable()->constrained()->nullOnDelete();
            $table->string('claim_number')->unique();
            $table->date('travel_date');
            $table->string('from_location');
            $table->string('to_location');
            $table->text('purpose');
            $table->decimal('distance_km', 10, 2);
            $table->decimal('rate_per_km', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_registration')->nullable();
            $table->integer('odometer_start')->nullable();
            $table->integer('odometer_end')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'paid'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mileage_claims');
        Schema::dropIfExists('travel_itinerary_items');
        Schema::dropIfExists('advance_deductions');
        Schema::dropIfExists('expense_attachments');
        Schema::dropIfExists('travel_policies');
        
        Schema::table('advance_requests', function (Blueprint $table) {
            $table->dropColumn([
                'project_code', 'advance_type', 'installments', 'installment_amount',
                'first_deduction_date', 'deducted_amount', 'balance_amount',
                'deduction_notes', 'admin_notes', 'payment_reference', 'payment_method'
            ]);
        });

        Schema::table('expense_claims', function (Blueprint $table) {
            $table->dropForeign(['secondary_approver_id']);
            $table->dropColumn([
                'project_code', 'cost_center', 'payment_method', 'reference_number',
                'distance_km', 'rate_per_km', 'tax_amount', 'total_amount',
                'is_reimbursable', 'billable_to_client', 'client_name',
                'admin_notes', 'secondary_approver_id', 'secondary_approved_at',
                'payment_reference', 'reimbursement_method'
            ]);
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn([
                'category_code', 'per_diem_rate', 'requires_approval',
                'is_taxable', 'tax_rate', 'approval_threshold', 'gl_account'
            ]);
        });

        Schema::table('travel_requests', function (Blueprint $table) {
            $table->dropForeign(['secondary_approver_id']);
            $table->dropColumn([
                'project_code', 'client_name', 'travel_class', 'accommodation_type',
                'number_of_travelers', 'travelers', 'accommodation_required',
                'hotel_name', 'accommodation_cost', 'visa_required', 'visa_cost',
                'insurance_required', 'insurance_cost', 'per_diem_rate',
                'number_of_days', 'total_per_diem', 'itinerary', 'billable_to_client',
                'cost_center', 'actual_cost', 'admin_notes', 'secondary_approver_id',
                'secondary_approved_at', 'priority'
            ]);
        });
    }
};
