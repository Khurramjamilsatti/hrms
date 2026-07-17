<?php

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Ensure system roles (including manager) have their seeded permissions.
     * Safe to re-run: seeder uses firstOrCreate + syncPermissions.
     */
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => RolesAndPermissionsSeeder::class,
            '--force' => true,
        ]);
    }

    public function down(): void
    {
        // Permissions are additive/synced; no destructive rollback.
    }
};
