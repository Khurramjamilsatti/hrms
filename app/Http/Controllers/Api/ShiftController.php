<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\EmployeeShift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    /**
     * Display a listing of shifts.
     */
    public function index(Request $request)
    {
        $query = Shift::query();

        // Search by name (case-insensitive for PostgreSQL)
        if ($request->has('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        // Filter by status (only apply filter if value is not empty)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true' || $request->is_active === '1');
        }

        // Include employee count
        $query->withCount('employeeShifts');

        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $shifts = $query->paginate($request->get('per_page', 15));

        return response()->json($shifts);
    }

    /**
     * Store a newly created shift.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shifts',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'grace_period_minutes' => 'nullable|integer|min:0|max:60',
            'is_active' => 'boolean',
        ]);

        $shift = Shift::create($validated);

        return response()->json([
            'message' => 'Shift created successfully',
            'data' => $shift
        ], 201);
    }

    /**
     * Display the specified shift.
     */
    public function show($id)
    {
        $shift = Shift::withCount('employeeShifts')
            ->with(['employeeShifts' => function($query) {
                $query->with(['employee:id,first_name,last_name,employee_code'])
                      ->latest()
                      ->limit(10);
            }])
            ->findOrFail($id);

        return response()->json($shift);
    }

    /**
     * Update the specified shift.
     */
    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('shifts')->ignore($shift->id)],
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'grace_period_minutes' => 'nullable|integer|min:0|max:60',
            'is_active' => 'boolean',
        ]);

        $shift->update($validated);

        return response()->json([
            'message' => 'Shift updated successfully',
            'data' => $shift
        ]);
    }

    /**
     * Remove the specified shift.
     */
    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);

        // Check if shift is assigned to any employees
        $assignmentCount = $shift->employeeShifts()->count();
        
        if ($assignmentCount > 0) {
            return response()->json([
                'message' => "Cannot delete shift. It is assigned to {$assignmentCount} employee(s).",
                'error' => 'This shift is currently in use'
            ], 422);
        }

        $shift->delete();

        return response()->json([
            'message' => 'Shift deleted successfully'
        ], 200);
    }

    /**
     * Toggle shift status (active/inactive).
     */
    public function toggleStatus($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->update(['is_active' => !$shift->is_active]);

        return response()->json([
            'message' => 'Shift status updated successfully',
            'data' => $shift
        ]);
    }

    /**
     * Get all active shifts (for dropdowns).
     */
    public function active()
    {
        $shifts = Shift::where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return response()->json($shifts);
    }

    /**
     * Get shift statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_shifts' => Shift::count(),
            'active_shifts' => Shift::where('is_active', true)->count(),
            'inactive_shifts' => Shift::where('is_active', false)->count(),
            'total_assignments' => Shift::withCount('employeeShifts')->get()->sum('employee_shifts_count'),
            'shifts_with_employees' => Shift::has('employeeShifts')->count(),
        ];

        return response()->json($stats);
    }

    // Employee Shift Assignments (Universal SOPs)
    
    /**
     * Get all employees assigned to a specific shift.
     */
    public function getAssignedEmployees($shiftId)
    {
        $shift = Shift::findOrFail($shiftId);
        
        $assignments = EmployeeShift::with(['employee.department', 'employee.designation'])
            ->where('shift_id', $shiftId)
            ->where(function($query) {
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', now());
            })
            ->latest()
            ->paginate(50);

        return response()->json($assignments);
    }

    /**
     * Assign a single employee to a shift.
     */
    public function assignEmployee(Request $request, $shiftId)
    {
        $shift = Shift::findOrFail($shiftId);

        if (!$shift->is_active) {
            return response()->json([
                'message' => 'Cannot assign employees to an inactive shift.'
            ], 400);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
        ]);

        // Check for conflicts
        $conflict = $this->checkShiftConflict(
            $validated['employee_id'],
            $shiftId,
            $validated['effective_from'],
            $validated['effective_to'] ?? null
        );

        if ($conflict) {
            return response()->json([
                'message' => 'Employee already assigned to another shift during this period.',
                'conflict' => $conflict
            ], 422);
        }

        $assignment = EmployeeShift::create([
            'employee_id' => $validated['employee_id'],
            'shift_id' => $shiftId,
            'effective_from' => $validated['effective_from'],
            'effective_to' => $validated['effective_to'] ?? null,
        ]);

        return response()->json(
            $assignment->load(['employee.department', 'employee.designation', 'shift']),
            201
        );
    }

    /**
     * Bulk assign multiple employees to a shift.
     */
    public function bulkAssignEmployees(Request $request, $shiftId)
    {
        $shift = Shift::findOrFail($shiftId);

        if (!$shift->is_active) {
            return response()->json([
                'message' => 'Cannot assign employees to an inactive shift.'
            ], 400);
        }

        $validated = $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'required|exists:employees,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
        ]);

        $assignments = [];
        $conflicts = [];
        $errors = [];

        foreach ($validated['employee_ids'] as $employeeId) {
            // Check for conflicts
            $conflict = $this->checkShiftConflict(
                $employeeId,
                $shiftId,
                $validated['effective_from'],
                $validated['effective_to'] ?? null,
                null
            );

            if ($conflict) {
                $conflicts[] = [
                    'employee_id' => $employeeId,
                    'conflict' => $conflict
                ];
                continue;
            }

            try {
                $assignment = EmployeeShift::create([
                    'employee_id' => $employeeId,
                    'shift_id' => $shiftId,
                    'effective_from' => $validated['effective_from'],
                    'effective_to' => $validated['effective_to'] ?? null,
                ]);
                $assignments[] = $assignment->load(['employee', 'shift']);
            } catch (\Exception $e) {
                $errors[] = [
                    'employee_id' => $employeeId,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => count($assignments) . ' employees assigned successfully',
            'assignments' => $assignments,
            'conflicts' => $conflicts,
            'errors' => $errors,
            'success_count' => count($assignments),
            'conflict_count' => count($conflicts),
            'error_count' => count($errors),
        ], count($assignments) > 0 ? 201 : 422);
    }

    /**
     * Remove an employee from a shift.
     */
    public function removeEmployeeAssignment($shiftId, $assignmentId)
    {
        $assignment = EmployeeShift::where('shift_id', $shiftId)
            ->where('id', $assignmentId)
            ->firstOrFail();

        $assignment->delete();

        return response()->json([
            'message' => 'Employee removed from shift successfully'
        ]);
    }

    /**
     * Update an employee's shift assignment dates.
     */
    public function updateAssignment(Request $request, $shiftId, $assignmentId)
    {
        $assignment = EmployeeShift::where('shift_id', $shiftId)
            ->where('id', $assignmentId)
            ->firstOrFail();

        $validated = $request->validate([
            'effective_from' => 'sometimes|date',
            'effective_to' => 'nullable|date|after:effective_from',
        ]);

        // Check for conflicts if dates are changing
        if (isset($validated['effective_from']) || isset($validated['effective_to'])) {
            $conflict = $this->checkShiftConflict(
                $assignment->employee_id,
                $shiftId,
                $validated['effective_from'] ?? $assignment->effective_from,
                $validated['effective_to'] ?? $assignment->effective_to,
                $assignmentId
            );

            if ($conflict) {
                return response()->json([
                    'message' => 'Cannot update assignment: Employee already assigned to another shift during this period.',
                    'conflict' => $conflict
                ], 422);
            }
        }

        $assignment->update($validated);

        return response()->json(
            $assignment->load(['employee.department', 'employee.designation', 'shift'])
        );
    }

    /**
     * Get available employees (not assigned to any shift during specified period).
     */
    public function getAvailableEmployees(Request $request, $shiftId)
    {
        $shift = Shift::findOrFail($shiftId);

        $validated = $request->validate([
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'search' => 'nullable|string',
        ]);

        // Default to today if not provided
        $effectiveFrom = $validated['effective_from'] ?? now()->format('Y-m-d');
        $effectiveTo = $validated['effective_to'] ?? null;

        // Get employees not assigned to any shift during the specified period
        $query = \App\Models\Employee::with(['department', 'designation'])
            ->where('employment_status', 'active')
            ->whereDoesntHave('employeeShifts', function($q) use ($effectiveFrom, $effectiveTo) {
                $q->where(function($query) use ($effectiveFrom, $effectiveTo) {
                    $query->where(function($q) use ($effectiveTo) {
                        // Assignment starts before or during our period
                        $q->where('effective_from', '<=', $effectiveTo ?? '9999-12-31');
                    })->where(function($q) use ($effectiveFrom) {
                        // Assignment ends after or during our period (or no end date)
                        $q->whereNull('effective_to')
                          ->orWhere('effective_to', '>=', $effectiveFrom);
                    });
                });
            });

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%");
            });
        }

        $employees = $query->paginate(50);

        return response()->json($employees);
    }

    /**
     * Get assignment history for a shift (including past assignments).
     */
    public function getAssignmentHistory($shiftId)
    {
        $shift = Shift::findOrFail($shiftId);

        $history = EmployeeShift::with(['employee.department', 'employee.designation'])
            ->where('shift_id', $shiftId)
            ->latest('created_at')
            ->paginate(50);

        return response()->json($history);
    }

    /**
     * Check for shift assignment conflicts.
     */
    private function checkShiftConflict($employeeId, $newShiftId, $effectiveFrom, $effectiveTo = null, $excludeAssignmentId = null)
    {
        $query = EmployeeShift::with('shift')
            ->where('employee_id', $employeeId)
            ->where('shift_id', '!=', $newShiftId);

        if ($excludeAssignmentId) {
            $query->where('id', '!=', $excludeAssignmentId);
        }

        // Check for overlapping date ranges
        $query->where(function($q) use ($effectiveFrom, $effectiveTo) {
            $q->where(function($query) use ($effectiveFrom, $effectiveTo) {
                // Existing assignment starts before or during our period
                $query->where('effective_from', '<=', $effectiveTo ?? '9999-12-31');
            })->where(function($query) use ($effectiveFrom) {
                // Existing assignment ends after or during our period (or no end date)
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', $effectiveFrom);
            });
        });

        return $query->first();
    }
}
