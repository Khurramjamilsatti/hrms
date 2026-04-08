<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Attendance::with(['employee.user', 'approver']);

        // Permission middleware already validated access
        // Apply data scope filters based on user context
        if ($user->hasRole('employee')) {
            // Employees can only see their own attendance
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->whereRaw('1 = 0'); // No results
            }
        } elseif ($user->hasRole('manager')) {
            // Managers see their team's attendance
            $teamEmployeeIds = Employee::where('manager_id', $user->id)->pluck('id');
            $query->whereIn('employee_id', $teamEmployeeIds);
        } elseif ($user->hasRole('section_head')) {
            // Section heads see their department's attendance
            $sectionHeadEmployee = $user->employee;
            if ($sectionHeadEmployee && $sectionHeadEmployee->department_id) {
                $deptEmployeeIds = Employee::where('department_id', $sectionHeadEmployee->department_id)->pluck('id');
                $query->whereIn('employee_id', $deptEmployeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        // hr_admin, super_admin, admin, and users with attendance.view permission see all attendance

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'ilike', "%{$search}%");
                  });
            });
        }

        // Get all attendance records first (ordered by date desc, then check_in asc with nulls last)
        $allAttendances = $query->orderBy('date', 'desc')
            ->orderByRaw('check_in ASC NULLS LAST')
            ->get();

        // Group by employee_id and date
        $grouped = $allAttendances->groupBy(function ($attendance) {
            return $attendance->employee_id . '_' . $attendance->date;
        })->map(function ($sessions, $key) {
            // Get first session for the main record
            $firstSession = $sessions->first();
            
            // Calculate total working hours for the day
            $totalWorkingHours = $sessions->sum('working_hours');
            $totalOvertimeHours = $sessions->sum('overtime_hours');
            
            return [
                'id' => $firstSession->id, // Use first session ID
                'employee_id' => $firstSession->employee_id,
                'employee' => $firstSession->employee ? [
                    'id' => $firstSession->employee->id,
                    'full_name' => $firstSession->employee->full_name,
                    'employee_code' => $firstSession->employee->employee_code,
                    'first_name' => $firstSession->employee->first_name,
                    'last_name' => $firstSession->employee->last_name,
                ] : null,
                'date' => $firstSession->date,
                'status' => $firstSession->status,
                'remarks' => $firstSession->remarks,
                'approved_by' => $firstSession->approved_by,
                'approver' => $firstSession->approver ? [
                    'id' => $firstSession->approver->id,
                    'name' => $firstSession->approver->name,
                ] : null,
                'total_working_hours' => round($totalWorkingHours, 2),
                'total_overtime_hours' => round($totalOvertimeHours, 2),
                'sessions_count' => $sessions->count(),
                'sessions' => $sessions->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'check_in' => $session->check_in,
                        'check_out' => $session->check_out,
                        'working_hours' => $session->working_hours,
                        'overtime_hours' => $session->overtime_hours,
                    ];
                })->values()->toArray(),
                'created_at' => $firstSession->created_at,
                'updated_at' => $firstSession->updated_at,
            ];
        })->values();

        // Manual pagination
        $perPage = (int) ($request->per_page ?? 15);
        $page = (int) ($request->page ?? 1);
        $total = $grouped->count();
        $lastPage = $total > 0 ? (int) ceil($total / $perPage) : 1;
        
        $offset = ($page - 1) * $perPage;
        $paginatedData = $grouped->slice($offset, $perPage)->values()->toArray();

        return response()->json([
            'data' => $paginatedData,
            'current_page' => $page,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'total' => $total,
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|unique:attendances,date,NULL,id,employee_id,' . $request->employee_id,
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,half_day,on_leave',
            'remarks' => 'nullable|string',
        ]);

        if ($validated['check_in'] && $validated['check_out']) {
            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);
            $validated['working_hours'] = $checkOut->diffInHours($checkIn, true);
        }

        $attendance = Attendance::create($validated);

        return response()->json($attendance->load(['employee.user']), 201);
    }

    public function checkIn(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        // If no employee_id provided, use authenticated user's employee ID
        $employeeId = $validated['employee_id'] ?? $user->employee->id ?? null;
        
        if (!$employeeId) {
            return response()->json(['message' => 'Employee record not found for this user'], 400);
        }

        // Regular employees can only check in for themselves
        if ($user->hasRole('employee') && $employeeId != $user->employee->id) {
            return response()->json(['message' => 'You can only check in for yourself'], 403);
        }

        $today = Carbon::today();
        
        // Check if there's an incomplete check-in (no check-out yet)
        $incompleteAttendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if ($incompleteAttendance) {
            return response()->json([
                'message' => 'Please check out first before checking in again',
                'attendance' => $incompleteAttendance
            ], 400);
        }

        // Create new attendance record (allows multiple check-ins per day)
        $attendance = Attendance::create([
            'employee_id' => $employeeId,
            'date' => $today,
            'check_in' => Carbon::now()->format('H:i'),
            'status' => 'present',
        ]);

        return response()->json($attendance->load(['employee.user']));
    }

    public function checkOut(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        // If no employee_id provided, use authenticated user's employee ID
        $employeeId = $validated['employee_id'] ?? $user->employee->id ?? null;
        
        if (!$employeeId) {
            return response()->json(['message' => 'Employee record not found for this user'], 400);
        }

        // Regular employees can only check out for themselves
        if ($user->hasRole('employee') && $employeeId != $user->employee->id) {
            return response()->json(['message' => 'You can only check out for yourself'], 403);
        }

        $today = Carbon::today();
        
        // Find the latest incomplete attendance (has check-in but no check-out)
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->orderBy('id', 'desc')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No active check-in found for today. Please check in first.'], 400);
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::now();
        $workingHours = $checkOut->diffInHours($checkIn, true);

        $attendance->update([
            'check_out' => $checkOut->format('H:i'),
            'working_hours' => $workingHours,
            'overtime_hours' => max(0, $workingHours - 8),
        ]);

        return response()->json($attendance);
    }

    public function show(Attendance $attendance)
    {
        return response()->json($attendance->load(['employee.user', 'approver']));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'in:present,absent,late,half_day,on_leave',
            'remarks' => 'nullable|string',
        ]);

        if (isset($validated['check_in']) && isset($validated['check_out'])) {
            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);
            $validated['working_hours'] = $checkOut->diffInHours($checkIn, true);
        }

        $attendance->update($validated);

        return response()->json($attendance->load(['employee.user']));
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return response()->json(['message' => 'Attendance record deleted successfully']);
    }

    public function summary(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        $attendances = Attendance::where('employee_id', $request->employee_id)
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->get();

        return response()->json([
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'half_days' => $attendances->where('status', 'half_day')->count(),
            'leave_days' => $attendances->where('status', 'on_leave')->count(),
            'total_working_hours' => $attendances->sum('working_hours'),
            'total_overtime_hours' => $attendances->sum('overtime_hours'),
        ]);
    }
}
