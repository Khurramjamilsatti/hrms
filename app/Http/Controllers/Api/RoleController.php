<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('paginate') && $request->boolean('paginate')) {
            $roles = $query->orderBy('name')->paginate($request->get('per_page', 15));
        } else {
            $roles = $query->orderBy('name')->get();
        }

        return response()->json([
            'data' => $roles
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'nullable|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        
        // Auto-generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name'], '_');
        }

        $permissionIds = $data['permission_ids'] ?? [];
        unset($data['permission_ids']);

        $role = Role::create($data);

        // Attach permissions
        if (!empty($permissionIds)) {
            $role->syncPermissions($permissionIds);
        }

        $role->load('permissions');

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load('permissions');

        // Group permissions by module for better display
        $groupedPermissions = $role->getGroupedPermissions();

        return response()->json([
            'data' => [
                'role' => $role,
                'grouped_permissions' => $groupedPermissions
            ]
        ]);
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        // Prevent modification of system roles
        if ($role->is_system_role) {
            return response()->json([
                'message' => 'System roles cannot be modified'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'sometimes|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        
        // Extract permission_ids before updating role
        $permissionIds = $data['permission_ids'] ?? null;
        unset($data['permission_ids']);

        $role->update($data);

        // Sync permissions if provided
        if ($permissionIds !== null) {
            $role->syncPermissions($permissionIds);
        }

        $role->load('permissions');

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        if ($role->is_system_role) {
            return response()->json([
                'message' => 'System roles cannot be deleted'
            ], 403);
        }

        // Check if any users have this role
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role that is assigned to users'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Sync permissions for a role
     */
    public function syncPermissions(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $role->syncPermissions($request->permission_ids);
        $role->load('permissions');

        return response()->json([
            'message' => 'Permissions synced successfully',
            'data' => $role
        ]);
    }

    /**
     * Grant a permission to a role
     */
    public function grantPermission(Request $request, Role $role)
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
        $role->grantPermission($permission);
        $role->load('permissions');

        return response()->json([
            'message' => 'Permission granted successfully',
            'data' => $role
        ]);
    }

    /**
     * Revoke a permission from a role
     */
    public function revokePermission(Request $request, Role $role)
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
        $role->revokePermission($permission);
        $role->load('permissions');

        return response()->json([
            'message' => 'Permission revoked successfully',
            'data' => $role
        ]);
    }
}
