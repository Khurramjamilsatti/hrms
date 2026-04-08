<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnboardingTemplate;
use App\Models\OnboardingTemplateTask;
use App\Models\EmployeeOnboarding;
use App\Models\EmployeeOnboardingTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OnboardingController extends Controller
{
    // Templates
    public function getTemplates()
    {
        $templates = OnboardingTemplate::with(['department', 'tasks'])
            ->latest()
            ->paginate(15);
        
        return response()->json($templates);
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $template = OnboardingTemplate::create($validated);
        return response()->json($template, 201);
    }

    public function getTemplate($id)
    {
        $template = OnboardingTemplate::with(['department', 'tasks'])->findOrFail($id);
        return response()->json($template);
    }

    public function updateTemplate(Request $request, $id)
    {
        $template = OnboardingTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'duration_days' => 'sometimes|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $template->update($validated);
        return response()->json($template);
    }

    // Template Tasks
    public function storeTemplateTask(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:onboarding_templates,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'day_number' => 'required|integer|min:1',
            'task_type' => 'required|in:document,training,meeting,system_access,other',
            'assigned_to_role' => 'nullable|integer',
            'is_mandatory' => 'nullable|boolean',
        ]);

        $task = OnboardingTemplateTask::create($validated);
        return response()->json($task, 201);
    }

    public function updateTemplateTask(Request $request, $id)
    {
        $task = OnboardingTemplateTask::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'day_number' => 'sometimes|integer|min:1',
            'task_type' => 'sometimes|in:document,training,meeting,system_access,other',
            'assigned_to_role' => 'nullable|integer',
            'is_mandatory' => 'nullable|boolean',
        ]);

        $task->update($validated);
        return response()->json($task);
    }

    public function deleteTemplateTask($id)
    {
        $task = OnboardingTemplateTask::findOrFail($id);
        $task->delete();
        return response()->json(['message' => 'Template task deleted successfully']);
    }

    // Employee Onboarding
    public function getOnboardings(Request $request)
    {
        $query = EmployeeOnboarding::with(['employee.user', 'template', 'buddy.user', 'tasks']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $onboardings = $query->latest()->paginate(15);
        return response()->json($onboardings);
    }

    public function startOnboarding(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'template_id' => 'required|exists:onboarding_templates,id',
            'start_date' => 'required|date',
            'buddy_id' => 'nullable|exists:employees,id',
        ]);

        $template = OnboardingTemplate::with('tasks')->findOrFail($validated['template_id']);
        
        $expectedCompletionDate = Carbon::parse($validated['start_date'])
            ->addDays($template->duration_days);

        $onboarding = EmployeeOnboarding::create([
            'employee_id' => $validated['employee_id'],
            'template_id' => $validated['template_id'],
            'start_date' => $validated['start_date'],
            'expected_completion_date' => $expectedCompletionDate,
            'buddy_id' => $validated['buddy_id'] ?? null,
            'status' => 'in_progress',
            'completion_percentage' => 0,
        ]);

        // Create tasks from template
        foreach ($template->tasks as $templateTask) {
            $dueDate = Carbon::parse($validated['start_date'])
                ->addDays($templateTask->day_number - 1);

            EmployeeOnboardingTask::create([
                'onboarding_id' => $onboarding->id,
                'template_task_id' => $templateTask->id,
                'title' => $templateTask->title,
                'description' => $templateTask->description,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }

        return response()->json($onboarding->load(['employee', 'template', 'tasks']), 201);
    }

    public function show($id)
    {
        $onboarding = EmployeeOnboarding::with(['employee.user', 'employee.department', 'template', 'buddy.user', 'tasks'])
            ->findOrFail($id);
        return response()->json($onboarding);
    }

    public function updateOnboarding(Request $request, $id)
    {
        $onboarding = EmployeeOnboarding::findOrFail($id);
        
        $validated = $request->validate([
            'employee_id' => 'sometimes|exists:employees,id',
            'template_id' => 'sometimes|exists:onboarding_templates,id',
            'start_date' => 'sometimes|date',
            'buddy_id' => 'nullable|exists:employees,id',
            'status' => 'sometimes|in:not_started,in_progress,completed',
        ]);

        if (isset($validated['template_id']) && $validated['template_id'] != $onboarding->template_id) {
            $template = OnboardingTemplate::with('tasks')->findOrFail($validated['template_id']);
            $onboarding->tasks()->delete();
            
            foreach ($template->tasks as $templateTask) {
                $dueDate = Carbon::parse($validated['start_date'] ?? $onboarding->start_date)
                    ->addDays($templateTask->day_number - 1);

                EmployeeOnboardingTask::create([
                    'onboarding_id' => $onboarding->id,
                    'template_task_id' => $templateTask->id,
                    'title' => $templateTask->title,
                    'description' => $templateTask->description,
                    'due_date' => $dueDate,
                    'status' => 'pending',
                ]);
            }
        }

        if (isset($validated['start_date'])) {
            $template = $onboarding->template;
            $validated['expected_completion_date'] = Carbon::parse($validated['start_date'])
                ->addDays($template->duration_days);
        }

        $onboarding->update($validated);
        return response()->json($onboarding->load(['employee', 'template', 'buddy', 'tasks']));
    }

    public function deleteOnboarding($id)
    {
        $onboarding = EmployeeOnboarding::findOrFail($id);
        $onboarding->tasks()->delete();
        $onboarding->delete();
        return response()->json(['message' => 'Onboarding deleted successfully']);
    }

    public function completeTask(Request $request, $taskId)
    {
        $task = EmployeeOnboardingTask::findOrFail($taskId);
        
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $task->update([
            'status' => 'completed',
            'completed_date' => now(),
            'completed_by' => $request->user()->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update onboarding progress
        $this->updateOnboardingProgress($task->onboarding_id);

        return response()->json($task);
    }

    public function skipTask(Request $request, $taskId)
    {
        $task = EmployeeOnboardingTask::findOrFail($taskId);
        
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $task->update([
            'status' => 'skipped',
            'completed_date' => now(),
            'completed_by' => $request->user()->id,
            'notes' => $validated['notes'],
        ]);

        $this->updateOnboardingProgress($task->onboarding_id);

        return response()->json($task);
    }

    private function updateOnboardingProgress($onboardingId)
    {
        $onboarding = EmployeeOnboarding::with('tasks')->findOrFail($onboardingId);
        
        $totalTasks = $onboarding->tasks->count();
        $completedTasks = $onboarding->tasks->whereIn('status', ['completed', 'skipped'])->count();
        
        $percentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        $updateData = [
            'completion_percentage' => $percentage,
        ];

        // Automatically change status to completed when 100%
        if ($percentage >= 100) {
            $updateData['status'] = 'completed';
            $updateData['actual_completion_date'] = now();
        } elseif ($percentage > 0) {
            $updateData['status'] = 'in_progress';
        } else {
            $updateData['status'] = 'not_started';
        }

        $onboarding->update($updateData);
    }
}
