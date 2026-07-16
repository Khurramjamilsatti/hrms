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
     * List users for role assignment (Super Admin only)
     */
    public function index(Request $request)
    {
        $query = User::with(['assignedRole.permissions', 'employee.department', 'directPermissions'])
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('employee_code', 'ilike', "%{$search}%")
                            ->orWhere('first_name', 'ilike', "%{$search}%")
                            ->orWhere('last_name', 'ilike', "%{$search}%");
                    });
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->boolean('unassigned_only')) {
            $query->whereNull('role_id');
        }

        $users = $query->paginate($request->integer('per_page', 15));

        $users->getCollection()->transform(function (User $user) {
            if ($user->role === 'super_admin') {
                $user->setAttribute('permissions_count', Permission::count());
            } else {
                $user->setAttribute('permissions_count', $user->getAllPermissions()->count());
            }
            return $user;
        });

        $payload = $users->toArray();
        $payload['stats'] = [
            'total' => User::count(),
            'with_role' => User::whereNotNull('role_id')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return response()->json($payload);
    }

    /**
     * Toggle user active status (Super Admin only)
     */
    public function toggleActive(User $user)
    {
        if ($user->role === 'super_admin' && $user->is_active) {
            $otherActiveSuperAdmins = User::where('role', 'super_admin')
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($otherActiveSuperAdmins === 0) {
                return response()->json([
                    'message' => 'Cannot deactivate the only active super admin account',
                ], 422);
            }
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => $user->is_active ? 'User activated successfully' : 'User deactivated successfully',
            'data' => $user->load(['assignedRole', 'employee']),
        ]);
    }

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

        // Prevent demoting the last super admin
        if ($user->role === 'super_admin' && $role->slug !== 'super_admin') {
            $otherActiveSuperAdmins = User::where('role', 'super_admin')
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($otherActiveSuperAdmins === 0) {
                return response()->json([
                    'message' => 'Cannot change role of the only active super admin',
                ], 422);
            }
        }

        $user->assignRole($role);
        $user->load('assignedRole.permissions', 'employee');

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
        if ($user->role === 'super_admin') {
            $otherActiveSuperAdmins = User::where('role', 'super_admin')
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($otherActiveSuperAdmins === 0) {
                return response()->json([
                    'message' => 'Cannot remove role from the only active super admin',
                ], 422);
            }
        }

        $employeeRole = Role::where('slug', 'employee')->first();

        $user->role_id = $employeeRole?->id;
        $user->role = 'employee';
        $user->save();
        $user->load('assignedRole', 'employee');

        return response()->json([
            'message' => 'Role reset to Employee successfully',
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
