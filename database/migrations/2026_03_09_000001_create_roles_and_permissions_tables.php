<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'Super Admin', 'HR Manager', 'Employee'
            $table->string('slug')->unique(); // e.g., 'super_admin', 'hr_manager', 'employee'
            $table->text('description')->nullable();
            $table->boolean('is_system_role')->default(false); // System roles cannot be deleted
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'View Employees', 'Edit Employee'
            $table->string('slug')->unique(); // e.g., 'employees.view', 'employees.edit'
            $table->string('module'); // e.g., 'employees', 'attendance', 'payroll'
            $table->string('action'); // e.g., 'view', 'create', 'edit', 'delete', 'approve'
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Role has many Permissions pivot table
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });

        // User belongs to Role
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained('roles')->onDelete('set null');
            // Keep old 'role' column for backward compatibility during migration
        });

        // Direct user permissions (exceptions/overrides)
        Schema::create('user_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->boolean('is_granted')->default(true); // true = granted, false = revoked
            $table->timestamps();
            
            $table->unique(['user_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('user_permission');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
