<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Travel Requests
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('request_number')->unique();
            $table->string('purpose');
            $table->text('description')->nullable();
            $table->string('from_location');
            $table->string('to_location');
            $table->date('departure_date');
            $table->date('return_date');
            $table->enum('travel_mode', ['flight', 'train', 'bus', 'car', 'other'])->default('flight');
            $table->decimal('estimated_cost', 10, 2);
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // Expense Categories
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('requires_receipt')->default(true);
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Expense Claims
        Schema::create('expense_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('travel_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('claim_number')->unique();
            $table->foreignId('category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->date('expense_date');
            $table->string('merchant_name')->nullable();
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PKR');
            $table->string('receipt_file')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'paid'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });

        // Advance Requests
        Schema::create('advance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('travel_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('request_number')->unique();
            $table->text('purpose');
            $table->decimal('amount', 10, 2);
            $table->date('required_date');
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid', 'settled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('settlement_date')->nullable();
            $table->decimal('settled_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advance_requests');
        Schema::dropIfExists('expense_claims');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('travel_requests');
    }
};
