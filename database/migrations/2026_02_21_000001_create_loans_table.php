<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('loan_number')->unique();
            $table->enum('loan_type', ['personal', 'medical', 'education', 'housing', 'emergency', 'other'])->default('personal');
            $table->decimal('amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->integer('installments');
            $table->decimal('installment_amount', 12, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('purpose');
            $table->enum('status', ['pending', 'approved', 'rejected', 'disbursed', 'active', 'completed', 'defaulted', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->date('disbursed_date')->nullable();
            $table->foreignId('disbursed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2);
            $table->string('payment_method')->nullable();
            $table->string('guarantor_name')->nullable();
            $table->foreignId('guarantor_employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('guarantor_contact')->nullable();
            $table->text('remarks')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->date('closed_date')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_amount', 12, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
        Schema::dropIfExists('loans');
    }
};
