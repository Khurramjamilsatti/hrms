<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEmployeeResource;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    use AuthorizesEmployeeResource;

    public function index(Request $request)
    {
        $query = Employee::with(['user', 'department', 'designation', 'manager']);

        $this->scopeAccessibleEmployeesList($query, $request);

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'ilike', "%{$search}%");
                  });
            });
        }

        $employees = $query->paginate($request->per_page ?? 50);

        return response()->json($employees);
    }

    public function getAllEmployees(Request $request)
    {
        $query = Employee::with(['user', 'department', 'designation'])
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name');

        $this->scopeAccessibleEmployeesList($query, $request);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%");
            });
        }

        $employees = $query->limit(100)->get();
        return response()->json($employees);
    }

    public function getAllEmployeesForDropdown(Request $request)
    {
        $query = Employee::with(['user', 'department', 'designation'])
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name');

        $this->scopeAccessibleEmployeesList($query, $request);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%");
            });
        }

        $employees = $query->get();
        return response()->json($employees);
    }

    /**
     * Get section heads / managers for department & reporting-manager dropdowns.
     * Optional department_id includes that department's assigned head even if
     * they are not currently in the same department.
     */
    public function getSectionHeads(Request $request)
    {
        $eligibleRoles = ['section_head', 'manager', 'hr_admin', 'admin'];

        $query = Employee::with(['user', 'department', 'designation'])
            ->whereHas('user', function ($q) use ($eligibleRoles) {
                $q->whereIn('role', $eligibleRoles)->where('is_active', true);
            })
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name');

        $departmentManagerId = null;
        if ($request->filled('department_id')) {
            $departmentId = $request->department_id;
            $departmentManagerId = Department::where('id', $departmentId)->value('manager_id');

            $query->where(function ($q) use ($departmentId, $departmentManagerId) {
                $q->where('department_id', $departmentId);
                if ($departmentManagerId) {
                    $q->orWhere('user_id', $departmentManagerId);
                }
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%");
            });
        }

        $sectionHeads = $query->get()->map(function ($employee) {
            return [
                'id' => $employee->user_id,
                'name' => $employee->full_name ?? "{$employee->first_name} {$employee->last_name}",
                'employee_code' => $employee->employee_code,
                'department_id' => $employee->department_id,
                'department' => $employee->department->name ?? null,
            ];
        });

        // Ensure department head is present even without an employee profile
        if ($departmentManagerId && ! $sectionHeads->contains('id', $departmentManagerId)) {
            $managerUser = User::find($departmentManagerId);
            if ($managerUser) {
                $sectionHeads->push([
                    'id' => $managerUser->id,
                    'name' => $managerUser->name,
                    'employee_code' => null,
                    'department_id' => (int) $request->department_id,
                    'department' => null,
                ]);
            }
        }

        return response()->json($sectionHeads->values());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,manager,employee',
            'employee_code' => 'required|unique:employees,employee_code',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'manager_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'string',
            'national_id' => 'nullable|string',
            'joining_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'employment_status' => 'in:active,on_leave,terminated,resigned',
        ]);

        DB::beginTransaction();
        try {
            // Get role_id from role slug (default to 'employee' if role not found)
            $roleSlug = $validated['role'] ?? 'employee';
            $role = Role::where('slug', $roleSlug)->first();
            
            if (!$role) {
                // Fallback to employee role
                $role = Role::where('slug', 'employee')->first();
            }
            
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $roleSlug, // Keep legacy role field for backward compatibility
                'role_id' => $role ? $role->id : null,
            ]);

            $employeeData = collect($validated)->except(['email', 'password', 'role'])->toArray();
            $employeeData['user_id'] = $user->id;

            $employee = Employee::create($employeeData);

            DB::commit();

            return response()->json($employee->load(['user', 'department', 'designation', 'manager']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating employee: ' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, Employee $employee)
    {
        $user = $request->user();
        
        // Permission middleware already validated access
        // Apply data scope validation based on user context
        if ($user->hasRole('employee')) {
            // Employees can only view their own profile
            if (!$user->employee || $user->employee->id !== $employee->id) {
                return response()->json(['message' => 'You can only view your own profile'], 403);
            }
        } elseif ($user->hasRole('manager')) {
            // Managers can view themselves and their direct reports
            $isSelf = $user->employee && (int) $user->employee->id === (int) $employee->id;
            if (! $isSelf && (int) $employee->manager_id !== (int) $user->id) {
                return response()->json(['message' => 'You can only view your team members'], 403);
            }
        } elseif ($user->hasRole('section_head')) {
            // Section heads can only view employees in their department
            $sectionHeadEmployee = $user->employee;
            if (!$sectionHeadEmployee || $employee->department_id !== $sectionHeadEmployee->department_id) {
                return response()->json(['message' => 'You can only view employees in your department'], 403);
            }
        }
        // hr_admin, super_admin, admin, and users with employees.view permission can view all
        
        return response()->json($employee->load([
            'user',
            'department',
            'designation',
            'manager',
            'documents',
            'contracts',
            'salaries.components.salaryComponent',
            'leaveBalances.leaveType',
        ]));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'role' => 'nullable|in:super_admin,hr_admin,section_head,admin,manager,employee',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'manager_id' => 'nullable|exists:users,id',
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'string',
            'national_id' => 'nullable|string',
            'employment_type' => 'in:full_time,part_time,contract,intern',
            'employment_status' => 'in:active,on_leave,terminated,resigned',
        ]);

        // Update employee record (exclude role as it belongs to users table)
        $employeeData = collect($validated)->except(['role'])->toArray();
        $employee->update($employeeData);

        // Update associated user record
        $userData = [];
        if (isset($validated['first_name']) || isset($validated['last_name'])) {
            $userData['name'] = ($validated['first_name'] ?? $employee->first_name) . ' ' . 
                               ($validated['last_name'] ?? $employee->last_name);
        }
        if (isset($validated['role'])) {
            // Update both legacy role field and new role_id
            $role = Role::where('slug', $validated['role'])->first();
            $userData['role'] = $validated['role'];
            if ($role) {
                $userData['role_id'] = $role->id;
            }
        }
        if (!empty($userData)) {
            $employee->user->update($userData);
        }

        return response()->json($employee->load(['user', 'department', 'designation', 'manager']));
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
