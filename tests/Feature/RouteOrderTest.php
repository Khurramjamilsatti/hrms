<?php

namespace Tests\Feature;

use Tests\TestCase;

class RouteOrderTest extends TestCase
{
    public function test_static_paths_are_registered(): void
    {
        $uris = collect(app('router')->getRoutes())->map(fn ($route) => $route->uri());

        foreach ([
            'api/assets/assignments/list',
            'api/cvs/employees/{employeeId}/history',
            'api/deployments/employees/{employeeId}/history',
            'api/timesheets/summary',
            'api/deployments/extensions/{extension}/approve',
        ] as $uri) {
            $this->assertTrue($uris->contains($uri), "Missing route URI: {$uri}");
        }
    }

    public function test_assignment_list_is_matched_before_asset_show(): void
    {
        $routes = collect(app('router')->getRoutes())->values();
        $assignmentIndex = $routes->search(fn ($r) => $r->uri() === 'api/assets/assignments/list');
        $assetShowIndex = $routes->search(fn ($r) => $r->uri() === 'api/assets/{asset}');

        $this->assertNotFalse($assignmentIndex);
        $this->assertNotFalse($assetShowIndex);
        $this->assertLessThan($assetShowIndex, $assignmentIndex);
    }
}
