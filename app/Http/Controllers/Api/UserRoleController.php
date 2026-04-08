<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{
    /**
     * Assign a role to a user (Super Admin only)
     */
    public function assignRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::find($request->role_id);

        // Check if role is active
        if (!$role->is_active) {
            return response()->json([
                'message' => 'Cannot assign an inactive role'
            ], 422);
        }

        $user->assignRole($role);
        $user->load('assignedRole.permissions');

        return response()->json([
            'message' => 'Role assigned successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove role from a user (Super Admin only)
     */
    public function removeRole(User $user)
    {
        $user->role_id = null;
        $user->role = 'employee'; // Default fallback
        $user->save();

        return response()->json([
            'message' => 'Role removed successfully',
            'data' => $user
        ]);
    }

    /**
     * Grant a direct permission to a user (Super Admin only)
     */
    public function grantPermission(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $permission = Permission::find($request->permission_id);
        $user->grantPermission($permission);
        $user->load('directPermissions');

        return response()->json([
            'message' => 'Permission granted successfully',
            'data' => $user
        ]);
    }

    /**
     * Revoke a direct permission from a user (Super Admin only)
     */
    public function revokePermission(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $permission = Permission::find($request->permission_id);
        $user->revokePermission($permission);
        $user->load('directPermissions');

        return response()->json([
            'message' => 'Permission revoked successfully',
            'data' => $user
        ]);
    }

    /**
     * Get all permissions for a user
     */
    public function permissions(User $user)
    {
        $permissions = $user->getAllPermissions();
        $groupedPermissions = $user->getGroupedPermissions();
        $allowedModules = $user->getAllowedModules();

        return response()->json([
            'data' => [
                'permissions' => $permissions,
                'grouped_permissions' => $groupedPermissions,
                'allowed_modules' => $allowedModules,
                'role' => $user->assignedRole,
                'direct_permissions' => $user->directPermissions
            ]
        ]);
    }

    /**
     * Get current user's permissions
     */
    public function myPermissions(Request $request)
    {
        $user = $request->user();
        $permissions = $user->getAllPermissions();
        $groupedPermissions = $user->getGroupedPermissions();
        $allowedModules = $user->getAllowedModules();

        return response()->json([
            'data' => [
                'permissions' => $permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'module' => $permission->module,
                        'action' => $permission->action,
                    ];
                }),
                'grouped_permissions' => $groupedPermissions,
                'allowed_modules' => $allowedModules,
                'role' => $user->assignedRole ? [
                    'id' => $user->assignedRole->id,
                    'name' => $user->assignedRole->name,
                    'slug' => $user->assignedRole->slug,
                ] : null,
                'is_super_admin' => $user->role === 'super_admin',
            ]
        ]);
    }

    /**
     * Check if current user has a specific permission
     */
    public function checkPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $hasPermission = $user->hasPermission($request->permission);

        return response()->json([
            'data' => [
                'has_permission' => $hasPermission,
                'permission' => $request->permission,
            ]
        ]);
    }
}
