<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Filter by module
        if ($request->has('module')) {
            $query->where('module', $request->module);
        }

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->action);
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

        if ($request->has('grouped') && $request->grouped) {
            return response()->json([
                'data' => Permission::getGroupedPermissions()
            ]);
        }

        $permissions = $query->orderBy('module')->orderBy('action')->get();

        return response()->json([
            'data' => $permissions
        ]);
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions',
            'slug' => 'nullable|string|max:255|unique:permissions',
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'description' => 'nullable|string',
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
            $data['slug'] = $data['module'] . '.' . $data['action'];
        }

        $permission = Permission::create($data);

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => $permission
        ], 201);
    }

    /**
     * Display the specified permission
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');

        return response()->json([
            'data' => $permission
        ]);
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:permissions,name,' . $permission->id,
            'slug' => 'sometimes|string|max:255|unique:permissions,slug,' . $permission->id,
            'module' => 'sometimes|string|max:255',
            'action' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $permission->update($validator->validated());

        return response()->json([
            'message' => 'Permission updated successfully',
            'data' => $permission
        ]);
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'message' => 'Permission deleted successfully'
        ]);
    }

    /**
     * Get all available modules
     */
    public function modules()
    {
        $modules = Permission::distinct('module')
            ->pluck('module')
            ->sort()
            ->values();

        return response()->json([
            'data' => $modules
        ]);
    }

    /**
     * Get all available actions
     */
    public function actions()
    {
        $actions = Permission::distinct('action')
            ->pluck('action')
            ->sort()
            ->values();

        return response()->json([
            'data' => $actions
        ]);
    }
}
