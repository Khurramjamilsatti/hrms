<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShiftRoster;
use App\Models\ShiftAssignment;
use App\Models\ShiftSwapRequest;
use Illuminate\Http\Request;

class ShiftSchedulingController extends Controller
{
    // Rosters
    public function getRosters(Request $request)
    {
        $query = ShiftRoster::with(['department', 'creator']);

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $rosters = $query->latest()->paginate(50);
        return response()->json($rosters);
    }

    public function storeRoster(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $roster = ShiftRoster::create([
            ...$validated,
            'status' => 'draft',
            'created_by' => $request->user()->id,
        ]);

        return response()->json($roster->load(['department', 'creator']), 201);
    }

    public function getRoster($id)
    {
        $roster = ShiftRoster::with(['department', 'creator', 'assignments.employee', 'assignments.shift'])
            ->findOrFail($id);
        return response()->json($roster);
    }

    public function updateRoster(Request $request, $id)
    {
        $roster = ShiftRoster::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
        ]);

        $roster->update($validated);
        return response()->json($roster->load(['department', 'creator']));
    }

    public function publishRoster($id)
    {
        $roster = ShiftRoster::findOrFail($id);
        $roster->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
        return response()->json($roster);
    }

    // Shift Assignments
    public function getAssignments(Request $request)
    {
        $query = ShiftAssignment::with(['roster', 'employee', 'shift']);

        if ($request->has('roster_id')) {
            $query->where('roster_id', $request->roster_id);
        }

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        $assignments = $query->latest('date')->paginate(50);
        return response()->json($assignments);
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'roster_id' => 'required|exists:shift_rosters,id',
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'is_day_off' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $assignment = ShiftAssignment::create($validated);
        return response()->json($assignment->load(['roster', 'employee', 'shift']), 201);
    }

    public function bulkAssignShifts(Request $request)
    {
        $validated = $request->validate([
            'roster_id' => 'required|exists:shift_rosters,id',
            'assignments' => 'required|array',
            'assignments.*.employee_id' => 'required|exists:employees,id',
            'assignments.*.shift_id' => 'required|exists:shifts,id',
            'assignments.*.date' => 'required|date',
            'assignments.*.start_time' => 'required|date_format:H:i',
            'assignments.*.end_time' => 'required|date_format:H:i',
            'assignments.*.is_day_off' => 'nullable|boolean',
        ]);

        $created = [];
        foreach ($validated['assignments'] as $assignment) {
            $assignment['roster_id'] = $validated['roster_id'];
            $created[] = ShiftAssignment::create($assignment);
        }

        return response()->json($created, 201);
    }

    public function updateAssignment(Request $request, $id)
    {
        $assignment = ShiftAssignment::findOrFail($id);
        
        $validated = $request->validate([
            'shift_id' => 'sometimes|exists:shifts,id',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'is_day_off' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $assignment->update($validated);
        return response()->json($assignment->load(['roster', 'employee', 'shift']));
    }

    public function deleteAssignment($id)
    {
        $assignment = ShiftAssignment::findOrFail($id);
        $assignment->delete();
        return response()->json(['message' => 'Shift assignment deleted successfully']);
    }

    // Shift Swap Requests
    public function getSwapRequests(Request $request)
    {
        $query = ShiftSwapRequest::with([
            'requester', 
            'requesterAssignment.shift', 
            'swapper', 
            'swapperAssignment.shift',
            'approver'
        ]);

        if ($request->user()->role === 'employee') {
            $employeeId = $request->user()->employee->id;
            $query->where(function($q) use ($employeeId) {
                $q->where('requester_id', $employeeId)
                  ->orWhere('swapper_id', $employeeId);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $swapRequests = $query->latest()->paginate(50);
        return response()->json($swapRequests);
    }

    public function requestShiftSwap(Request $request)
    {
        $validated = $request->validate([
            'requester_id' => 'required|exists:employees,id',
            'requester_assignment_id' => 'required|exists:shift_assignments,id',
            'swapper_id' => 'required|exists:employees,id',
            'swapper_assignment_id' => 'nullable|exists:shift_assignments,id',
            'reason' => 'nullable|string',
        ]);

        $validated['status'] = 'pending';

        $swapRequest = ShiftSwapRequest::create($validated);
        return response()->json($swapRequest->load([
            'requester', 
            'requesterAssignment', 
            'swapper', 
            'swapperAssignment'
        ]), 201);
    }

    public function respondToSwapRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'response' => 'required|in:accept,reject',
        ]);

        $swapRequest = ShiftSwapRequest::findOrFail($id);
        
        $status = $validated['response'] === 'accept' ? 'accepted' : 'rejected';
        $swapRequest->update(['status' => $status]);

        return response()->json($swapRequest);
    }

    public function approveShiftSwap(Request $request, $id)
    {
        $swapRequest = ShiftSwapRequest::findOrFail($id);
        
        if ($swapRequest->status !== 'accepted') {
            return response()->json(['message' => 'Swap request must be accepted by swapper first'], 400);
        }

        $swapRequest->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        // Perform the actual swap
        $requesterAssignment = $swapRequest->requesterAssignment;
        $swapperAssignment = $swapRequest->swapperAssignment;

        if ($swapperAssignment) {
            $tempShift = $requesterAssignment->shift_id;
            $tempStartTime = $requesterAssignment->start_time;
            $tempEndTime = $requesterAssignment->end_time;

            $requesterAssignment->update([
                'shift_id' => $swapperAssignment->shift_id,
                'start_time' => $swapperAssignment->start_time,
                'end_time' => $swapperAssignment->end_time,
            ]);

            $swapperAssignment->update([
                'shift_id' => $tempShift,
                'start_time' => $tempStartTime,
                'end_time' => $tempEndTime,
            ]);
        }

        return response()->json($swapRequest);
    }

    public function declineShiftSwap(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $swapRequest = ShiftSwapRequest::findOrFail($id);
        $swapRequest->update([
            'status' => 'declined',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return response()->json($swapRequest);
    }
}
