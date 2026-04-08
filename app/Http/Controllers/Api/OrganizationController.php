<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    public function getOrganizationChart()
    {
        // Build hierarchical structure based on reporting relationships
        $employees = Employee::with(['designation', 'department', 'user'])
            ->where('employment_status', 'active')
            ->get();

        // Find root employees (those without managers - typically the CEO)
        $rootEmployees = $employees->filter(function($emp) {
            return $emp->manager_id === null;
        });

        // Build tree recursively
        $buildTree = function($parentUserId) use ($employees, &$buildTree) {
            $children = $employees->filter(function($emp) use ($parentUserId) {
                return $emp->manager_id === $parentUserId;
            });

            return $children->map(function($emp) use ($buildTree) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->first_name . ' ' . $emp->last_name,
                    'designation' => $emp->designation->title ?? $emp->designation->name ?? '',
                    'department' => $emp->department->name ?? '',
                    'email' => $emp->user->email ?? $emp->email ?? '',
                    'photo' => $emp->photo_url ?? null,
                    'subordinates' => $buildTree($emp->user_id)->values()->all(),
                ];
            })->values();
        };

        // Build hierarchy starting from root employees
        $hierarchy = $rootEmployees->map(function($emp) use ($buildTree) {
            return [
                'id' => $emp->id,
                'name' => $emp->first_name . ' ' . $emp->last_name,
                'designation' => $emp->designation->title ?? $emp->designation->name ?? '',
                'department' => $emp->department->name ?? '',
                'email' => $emp->user->email ?? $emp->email ?? '',
                'photo' => $emp->photo_url ?? null,
                'subordinates' => $buildTree($emp->user_id)->values()->all(),
            ];
        })->values()->first(); // Return the first root (typically CEO)

        return response()->json($hierarchy);
    }

    public function getHierarchy()
    {
        // Build hierarchical structure based on reporting relationships
        $employees = Employee::with(['designation', 'department'])
            ->where('employment_status', 'active')
            ->get();

        // Find root employees (those without managers or top-level)
        $rootEmployees = $employees->filter(function($emp) {
            return $emp->manager_id === null || $emp->designation->name === 'CEO' || $emp->designation->name === 'Managing Director';
        });

        $buildTree = function($parentId) use ($employees, &$buildTree) {
            $children = $employees->filter(function($emp) use ($parentId) {
                return $emp->manager_id === $parentId;
            });

            return $children->map(function($emp) use ($buildTree) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->first_name . ' ' . $emp->last_name,
                    'designation' => $emp->designation->name ?? '',
                    'department' => $emp->department->name ?? '',
                    'photo' => $emp->photo_url ?? null,
                    'email' => $emp->email,
                    'children' => $buildTree($emp->id),
                ];
            })->values();
        };

        $hierarchy = $rootEmployees->map(function($emp) use ($buildTree) {
            return [
                'id' => $emp->id,
                'name' => $emp->first_name . ' ' . $emp->last_name,
                'designation' => $emp->designation->name ?? '',
                'department' => $emp->department->name ?? '',
                'photo' => $emp->photo_url ?? null,
                'email' => $emp->email,
                'children' => $buildTree($emp->id),
            ];
        })->values();

        return response()->json($hierarchy);
    }

    public function getDepartmentStats(Request $request)
    {
        $query = Department::select('departments.*')
            ->withCount('employees')
            ->with('manager');

        // Get per_page parameter (default to 20)
        $perPage = $request->input('per_page', 20);

        $stats = $query->paginate($perPage);

        return response()->json($stats);
    }

    public function getEmployeeDirectory(Request $request)
    {
        // Query User model with employee relationships (as expected by frontend)
        // Show active and on_leave employees (not terminated)
        $query = \App\Models\User::query()
            ->whereHas('employee', function($q) {
                $q->whereIn('employment_status', ['active', 'on_leave', 'probation']);
            })
            ->with(['employee.designation', 'employee.department', 'employee.manager']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhereHas('employee', function($empQuery) use ($search) {
                      $empQuery->where('first_name', 'ilike', "%{$search}%")
                               ->orWhere('last_name', 'ilike', "%{$search}%")
                               ->orWhere('phone', 'ilike', "%{$search}%");
                  });
            });
        }

        // Department filter
        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Designation filter
        if ($request->has('designation_id') && $request->designation_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('designation_id', $request->designation_id);
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('employment_status', $request->status);
            });
        }

        // Get per_page parameter (default to 20)
        $perPage = $request->input('per_page', 20);

        // Paginate results
        $directory = $query->latest('created_at')->paginate($perPage);

        return response()->json($directory);
    }

    public function getTeamMembers($managerId)
    {
        $team = Employee::where('manager_id', $managerId)
            ->with(['designation', 'department'])
            ->where('employment_status', 'active')
            ->get()
            ->map(function($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->first_name . ' ' . $emp->last_name,
                    'designation' => $emp->designation->name ?? '',
                    'department' => $emp->department->name ?? '',
                    'email' => $emp->email,
                    'phone' => $emp->phone,
                    'photo' => $emp->photo_url ?? null,
                ];
            });

        return response()->json($team);
    }
}
