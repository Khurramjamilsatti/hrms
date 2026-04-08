<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimesheetController extends Controller
{
    // Projects
    public function getProjects()
    {
        $projects = Project::with(['manager', 'client'])
            ->latest()
            ->paginate(15);
        
        return response()->json($projects);
    }

    public function storeProject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists:employees,id',
            'manager_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:planning,active,on_hold,completed,cancelled',
        ]);

        $project = Project::create($validated);
        return response()->json($project->load(['manager', 'client']), 201);
    }

    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists:employees,id',
            'manager_id' => 'sometimes|exists:employees,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:planning,active,on_hold,completed,cancelled',
        ]);

        $project->update($validated);
        return response()->json($project->load(['manager', 'client']));
    }

    // Project Tasks
    public function getProjectTasks($projectId)
    {
        $tasks = ProjectTask::where('project_id', $projectId)
            ->with('assignedTo')
            ->latest()
            ->get();
        
        return response()->json($tasks);
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'nullable|in:todo,in_progress,review,completed',
            'due_date' => 'nullable|date',
        ]);

        $task = ProjectTask::create($validated);
        return response()->json($task->load('assignedTo'), 201);
    }

    public function updateTask(Request $request, $id)
    {
        $task = ProjectTask::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'status' => 'sometimes|in:todo,in_progress,review,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);
        return response()->json($task->load('assignedTo'));
    }

    // Timesheets
    public function getTimesheets(Request $request)
    {
        $query = Timesheet::with(['employee.user', 'project', 'task']);

        if ($request->user()->role === 'employee') {
            $query->where('employee_id', $request->user()->employee->id);
        } elseif ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
        }

        $timesheets = $query->latest('date')->paginate($request->per_page ?? 20);
        return response()->json($timesheets);
    }

    public function storeTimesheet(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:project_tasks,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
            'billable' => 'nullable|boolean',
        ]);

        // Calculate hours worked in minutes
        $start = \Carbon\Carbon::parse($validated['start_time']);
        $end = \Carbon\Carbon::parse($validated['end_time']);
        $validated['hours_worked'] = $end->diffInMinutes($start);
        $validated['status'] = 'draft';

        $timesheet = Timesheet::create($validated);
        return response()->json($timesheet->load(['employee', 'project', 'task']), 201);
    }

    public function updateTimesheet(Request $request, $id)
    {
        $timesheet = Timesheet::findOrFail($id);
        
        $validated = $request->validate([
            'project_id' => 'sometimes|exists:projects,id',
            'task_id' => 'nullable|exists:project_tasks,id',
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'description' => 'nullable|string',
            'billable' => 'nullable|boolean',
        ]);

        if (isset($validated['start_time']) || isset($validated['end_time'])) {
            $start = \Carbon\Carbon::parse($validated['start_time'] ?? $timesheet->start_time);
            $end = \Carbon\Carbon::parse($validated['end_time'] ?? $timesheet->end_time);
            $validated['hours_worked'] = $end->diffInMinutes($start);
        }

        $timesheet->update($validated);
        return response()->json($timesheet->load(['employee', 'project', 'task']));
    }

    public function submitTimesheet(Request $request, $id)
    {
        $timesheet = Timesheet::findOrFail($id);
        $timesheet->update(['status' => 'submitted']);
        return response()->json($timesheet);
    }

    public function approveTimesheet(Request $request, $id)
    {
        $timesheet = Timesheet::findOrFail($id);
        $timesheet->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);
        return response()->json($timesheet);
    }

    public function rejectTimesheet(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $timesheet = Timesheet::findOrFail($id);
        $timesheet->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);
        return response()->json($timesheet);
    }

    public function getTimesheetSummary(Request $request)
    {
        $employeeId = $request->user()->role === 'employee' 
            ? $request->user()->employee->id 
            : $request->get('employee_id');

        $summary = Timesheet::where('employee_id', $employeeId)
            ->whereMonth('date', $request->get('month', now()->month))
            ->whereYear('date', $request->get('year', now()->year))
            ->select('project_id', DB::raw('SUM(hours_worked) as total_minutes'))
            ->groupBy('project_id')
            ->with('project')
            ->get()
            ->map(function($item) {
                return [
                    'project' => $item->project->name,
                    'total_hours' => round($item->total_minutes / 60, 2),
                ];
            });

        return response()->json($summary);
    }
}
