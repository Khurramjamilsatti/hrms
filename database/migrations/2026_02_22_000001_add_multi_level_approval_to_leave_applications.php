<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update leave_applications table with two-stage approval
        Schema::table('leave_applications', function (Blueprint $table) {
            // First level approval (Section Head/Manager)
            $table->foreignId('first_approved_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->text('first_approval_remarks')->nullable()->after('first_approved_by');
            $table->timestamp('first_approved_at')->nullable()->after('first_approval_remarks');
            
            // Rename existing approved_by to final_approved_by (HR Admin approval)
            $table->renameColumn('approved_by', 'final_approved_by');
            $table->renameColumn('approval_remarks', 'final_approval_remarks');
            $table->renameColumn('approved_at', 'final_approved_at');
            
            // Add approval level tracking
            $table->enum('approval_level', ['pending', 'first_approved', 'final_approved', 'rejected'])->default('pending')->after('status');
        });

        // Update attendances table with two-stage approval for corrections
        Schema::table('attendances', function (Blueprint $table) {
            // First level approval (Section Head/Manager)
            $table->foreignId('first_approved_by')->nullable()->after('approved_by')->constrained('users')->onDelete('set null');
            $table->text('first_approval_remarks')->nullable()->after('first_approved_by');
            $table->timestamp('first_approved_at')->nullable()->after('first_approval_remarks');
            
            // Rename existing approved_by to final_approved_by and add missing fields
            $table->renameColumn('approved_by', 'final_approved_by');
            
            // Add final approval fields (attendance table didn't have these)
            $table->text('final_approval_remarks')->nullable()->after('first_approved_at');
            $table->timestamp('final_approved_at')->nullable()->after('final_approval_remarks');
            
            // Add approval level tracking
            $table->enum('approval_level', ['pending', 'first_approved', 'final_approved', 'rejected'])->default('pending')->after('status');
        });

        // Update overtime_requests table with two-stage approval
        Schema::table('overtime_requests', function (Blueprint $table) {
            // First level approval (Section Head/Manager)
            $table->foreignId('first_approved_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->text('first_approval_remarks')->nullable()->after('first_approved_by');
            $table->timestamp('first_approved_at')->nullable()->after('first_approval_remarks');
            
            // Rename existing approved_by to final_approved_by
            $table->renameColumn('approved_by', 'final_approved_by');
            $table->renameColumn('approval_remarks', 'final_approval_remarks');
            $table->renameColumn('approved_at', 'final_approved_at');
            
            // Add approval level tracking
            $table->enum('approval_level', ['pending', 'first_approved', 'final_approved', 'rejected'])->default('pending')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropColumn(['first_approved_by', 'first_approval_remarks', 'first_approved_at', 'approval_level']);
            $table->renameColumn('final_approved_by', 'approved_by');
            $table->renameColumn('final_approval_remarks', 'approval_remarks');
            $table->renameColumn('final_approved_at', 'approved_at');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'first_approved_by', 
                'first_approval_remarks', 
                'first_approved_at',
                'final_approval_remarks',
                'final_approved_at',
                'approval_level'
            ]);
            $table->renameColumn('final_approved_by', 'approved_by');
        });

        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->dropColumn(['first_approved_by', 'first_approval_remarks', 'first_approved_at', 'approval_level']);
            $table->renameColumn('final_approved_by', 'approved_by');
            $table->renameColumn('final_approval_remarks', 'approval_remarks');
            $table->renameColumn('final_approved_at', 'approved_at');
        });
    }
};
