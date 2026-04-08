<?php

use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;

// Get attendance.create permission
$createPerm = Permission::where('slug', 'attendance.create')->first();

if (!$createPerm) {
    echo "❌ attendance.create permission does not exist!\n";
    exit(1);
}

echo "✓ attendance.create permission exists (ID: {$createPerm->id})\n\n";

// Get Employee role
$employeeRole = Role::where('slug', 'employee')->first();

if (!$employeeRole) {
    echo "❌ Employee role not found!\n";
    exit(1);
}

echo "Employee Role: {$employeeRole->name} (ID: {$employeeRole->id})\n";
echo "Current permissions: " . $employeeRole->permissions()->count() . "\n\n";

// Check if role has this permission
$hasPermission = RolePermission::where('role_id', $employeeRole->id)
    ->where('permission_id', $createPerm->id)
    ->exists();

if ($hasPermission) {
    echo "✓ Employee already has attendance.create permission\n";
} else {
    echo "Adding attendance.create to Employee role...\n";
    RolePermission::create([
        'role_id' => $employeeRole->id,
        'permission_id' => $createPerm->id
    ]);
    echo "✅ Permission granted!\n";
}

echo "\nEmployee role now has " . $employeeRole->permissions()->count() . " permissions\n";

// List attendance-related permissions
echo "\nAttendance permissions for Employee:\n";
$attendancePerms = $employeeRole->permissions()
    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
    ->where('permissions.slug', 'like', 'attendance.%')
    ->select('permissions.name', 'permissions.slug')
    ->get();

foreach ($attendancePerms as $perm) {
    echo "  • {$perm->name} ({$perm->slug})\n";
}
