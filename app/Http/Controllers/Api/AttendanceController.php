<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEmployeeResource;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use AuthorizesEmployeeResource;

    public function index(Request $request)
    {
        $user = $request->user()->loadMissing('employee');
        $query = Attendance::query();

        $this->scopeToAccessibleEmployees($query, $request, 'attendance');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
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

        $perPage = max(1, min((int) ($request->per_page ?? 15), 100));

        // Paginate unique employee+date rows in the database (avoids loading all sessions into memory)
        $dayPage = (clone $query)
            ->select('employee_id', 'date')
            ->groupBy('employee_id', 'date')
            ->orderByDesc('date')
            ->orderBy('employee_id')
            ->paginate($perPage);

        $dayItems = collect($dayPage->items());

        if ($dayItems->isEmpty()) {
            return response()->json([
                'data' => [],
                'current_page' => $dayPage->currentPage(),
                'last_page' => $dayPage->lastPage(),
                'per_page' => $dayPage->perPage(),
                'total' => $dayPage->total(),
                'from' => 0,
                'to' => 0,
            ]);
        }

        $employeeIds = $dayItems->pluck('employee_id')->unique()->values();
        $dates = $dayItems->map(function ($row) {
            return Carbon::parse($row->date)->toDateString();
        })->unique()->values();

        $sessionsByDay = Attendance::with(['employee.user', 'approver'])
            ->whereIn('employee_id', $employeeIds)
            ->whereIn('date', $dates)
            ->orderByRaw('check_in ASC NULLS LAST')
            ->get()
            ->groupBy(function ($attendance) {
                return $attendance->employee_id.'_'.Carbon::parse($attendance->date)->toDateString();
            });

        $paginatedData = $dayItems->map(function ($row) use ($sessionsByDay) {
            $dateStr = Carbon::parse($row->date)->toDateString();
            $key = $row->employee_id.'_'.$dateStr;
            $sessions = $sessionsByDay->get($key, collect());

            if ($sessions->isEmpty()) {
                return null;
            }

            $firstSession = $sessions->first();
            $totalWorkingHours = $sessions->sum('working_hours');
            $totalOvertimeHours = $sessions->sum('overtime_hours');

            return [
                'id' => $firstSession->id,
                'employee_id' => $firstSession->employee_id,
                'employee' => $firstSession->employee ? [
                    'id' => $firstSession->employee->id,
                    'full_name' => $firstSession->employee->full_name,
                    'employee_code' => $firstSession->employee->employee_code,
                    'first_name' => $firstSession->employee->first_name,
                    'last_name' => $firstSession->employee->last_name,
                ] : null,
                'date' => $dateStr,
                'status' => $firstSession->status,
                'remarks' => $firstSession->remarks,
                'approved_by' => $firstSession->approved_by,
                'approver' => $firstSession->approver ? [
                    'id' => $firstSession->approver->id,
                    'name' => $firstSession->approver->name,
                ] : null,
                'total_working_hours' => round((float) $totalWorkingHours, 2),
                'total_overtime_hours' => round((float) $totalOvertimeHours, 2),
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
        })->filter()->values();

        return response()->json([
            'data' => $paginatedData,
            'current_page' => $dayPage->currentPage(),
            'last_page' => $dayPage->lastPage(),
            'per_page' => $dayPage->perPage(),
            'total' => $dayPage->total(),
            'from' => $dayPage->firstItem() ?? 0,
            'to' => $dayPage->lastItem() ?? 0,
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

        $this->assertCanAccessEmployeeRecord($request, (int) $validated['employee_id'], 'attendance');

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
        $user = $request->user()->loadMissing('employee');

        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $employeeId = $validated['employee_id'] ?? $user->employee?->id;

        if (!$employeeId) {
            return response()->json([
                'message' => 'No employee profile is linked to your account. Ask HR to link an employee record before checking in.',
            ], 400);
        }

        // Regular employees can only check in for themselves
        if ($user->hasRole('employee') && (int) $employeeId !== (int) $user->employee->id) {
            return response()->json(['message' => 'You can only check in for yourself'], 403);
        }

        $this->assertCanAccessEmployeeRecord($request, (int) $employeeId, 'attendance');

        $today = Carbon::today();

        // Check if there's an incomplete check-in (no check-out yet)
        $incompleteAttendance = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if ($incompleteAttendance) {
            return response()->json([
                'message' => 'Please check out first before checking in again',
                'attendance' => $incompleteAttendance,
            ], 400);
        }

        $now = Carbon::now();
        $status = 'present';

        // Optional late detection using default shift start 09:00
        if ($now->format('H:i') > '09:15') {
            $status = 'late';
        }

        $attendance = Attendance::create([
            'employee_id' => $employeeId,
            'date' => $today->toDateString(),
            'check_in' => $now->format('H:i'),
            'status' => $status,
        ]);

        return response()->json($attendance->load(['employee.user']), 201);
    }

    public function checkOut(Request $request)
    {
        $user = $request->user()->loadMissing('employee');

        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $employeeId = $validated['employee_id'] ?? $user->employee?->id;

        if (!$employeeId) {
            return response()->json([
                'message' => 'No employee profile is linked to your account. Ask HR to link an employee record before checking out.',
            ], 400);
        }

        if ($user->hasRole('employee') && (int) $employeeId !== (int) $user->employee->id) {
            return response()->json(['message' => 'You can only check out for yourself'], 403);
        }

        $this->assertCanAccessEmployeeRecord($request, (int) $employeeId, 'attendance');

        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->orderByDesc('id')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No active check-in found for today. Please check in first.'], 400);
        }

        $checkIn = Carbon::parse($attendance->date->format('Y-m-d').' '.$attendance->check_in);
        $checkOut = Carbon::now();
        $workingHours = round($checkIn->diffInMinutes($checkOut) / 60, 2);

        $attendance->update([
            'check_out' => $checkOut->format('H:i'),
            'working_hours' => $workingHours,
            'overtime_hours' => max(0, $workingHours - 8),
        ]);

        return response()->json($attendance->fresh()->load(['employee.user']));
    }

    public function show(Request $request, Attendance $attendance)
    {
        $this->assertCanAccessEmployeeRecord($request, $attendance->employee_id, 'attendance');

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

        $this->assertCanAccessEmployeeRecord($request, (int) $request->employee_id, 'attendance');

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
