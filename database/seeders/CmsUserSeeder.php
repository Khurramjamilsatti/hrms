<?php

namespace Database\Seeders;

use App\Models\CmsUser;
use Illuminate\Database\Seeder;

class CmsUserSeeder extends Seeder
{
    public function run(): void
    {
        CmsUser::updateOrCreate(
            ['email' => 'cms@payroll-digital.com'],
            [
                'name' => 'CMS Administrator',
                'password' => 'password',
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        CmsUser::updateOrCreate(
            ['email' => 'editor@payroll-digital.com'],
            [
                'name' => 'Content Editor',
                'password' => 'password',
                'role' => 'editor',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info('CMS users ready:');
        $this->command?->info('  cms@payroll-digital.com / password (admin)');
        $this->command?->info('  editor@payroll-digital.com / password (editor)');
    }
}
