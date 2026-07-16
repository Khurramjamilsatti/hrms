<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PermissionAliasTest extends TestCase
{
    use DatabaseTransactions;

    public function test_route_permission_slugs_resolve_to_seeded_permissions(): void
    {
        $dbUser = User::query()->where('role', 'hr_admin')->whereNotNull('role_id')->first()
            ?? User::query()->where('role', 'admin')->whereNotNull('role_id')->first();

        $this->assertNotNull($dbUser, 'Seeded HR/admin user required');

        $this->assertTrue($dbUser->hasPermission('loans.apply'));
        $this->assertTrue($dbUser->hasPermission('loans.create'), 'loans.create should alias to loans.apply/manage');
        $this->assertTrue($dbUser->hasPermission('salary-advance.view'), 'hyphenated salary-advance.view should alias');
        $this->assertTrue($dbUser->hasPermission('salary_advances.view'));
        $this->assertTrue($dbUser->hasPermission('cv-bank.create'), 'cv-bank.create should alias to cv_bank.manage');
        $this->assertTrue($dbUser->hasPermission('deployments.create'), 'deployments.create should alias to deployments.manage');
        $this->assertTrue($dbUser->hasPermission('salary-components.create'));
    }

    public function test_permission_table_includes_canonical_slugs(): void
    {
        foreach ([
            'loans.apply',
            'salary_advances.view',
            'cv_bank.manage',
            'deployments.manage',
            'salary_components.manage',
        ] as $slug) {
            $this->assertTrue(
                Permission::query()->where('slug', $slug)->exists(),
                "Missing permission slug: {$slug}"
            );
        }
    }
}
