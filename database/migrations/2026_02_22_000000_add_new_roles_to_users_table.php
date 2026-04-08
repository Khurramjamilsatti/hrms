<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing check constraint
        DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
        
        // Add new check constraint with additional roles
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role::text = ANY (ARRAY['super_admin'::character varying, 'hr_admin'::character varying, 'section_head'::character varying, 'admin'::character varying, 'manager'::character varying, 'employee'::character varying]::text[]))");
    }

    public function down(): void
    {
        // Drop the new check constraint
        DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
        
        // Restore original check constraint
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role::text = ANY (ARRAY['admin'::character varying, 'manager'::character varying, 'employee'::character varying]::text[]))");
    }
};
