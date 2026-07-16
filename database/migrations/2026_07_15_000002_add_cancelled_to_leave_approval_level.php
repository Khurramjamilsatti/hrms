<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Laravel enum on PostgreSQL is a varchar + check constraint.
        // Allow cancelled so leave cancel flow can update approval_level consistently.
        DB::statement('ALTER TABLE leave_applications DROP CONSTRAINT IF EXISTS leave_applications_approval_level_check');
        DB::statement("ALTER TABLE leave_applications ADD CONSTRAINT leave_applications_approval_level_check CHECK ((approval_level)::text = ANY ((ARRAY['pending'::character varying, 'first_approved'::character varying, 'final_approved'::character varying, 'rejected'::character varying, 'cancelled'::character varying])::text[]))");
    }

    public function down(): void
    {
        DB::table('leave_applications')
            ->where('approval_level', 'cancelled')
            ->update(['approval_level' => 'pending']);

        DB::statement('ALTER TABLE leave_applications DROP CONSTRAINT IF EXISTS leave_applications_approval_level_check');
        DB::statement("ALTER TABLE leave_applications ADD CONSTRAINT leave_applications_approval_level_check CHECK ((approval_level)::text = ANY ((ARRAY['pending'::character varying, 'first_approved'::character varying, 'final_approved'::character varying, 'rejected'::character varying])::text[]))");
    }
};
