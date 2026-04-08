<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OnboardingTemplate;
use App\Models\OnboardingTemplateTask;
use App\Models\EmployeeOnboarding;
use App\Models\EmployeeOnboardingTask;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;

class OnboardingSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();
        $managers = User::where('role', 'manager')->get();
        if ($managers->isEmpty()) {
            $managers = User::where('role', 'admin')->get();
        }

        // Create Onboarding Templates
        $templates = [
            [
                'name' => 'Software Engineer Onboarding',
                'description' => 'Complete onboarding process for software engineers',
                'department_id' => $departments->where('name', 'Engineering')->first()->id ?? null,
                'duration_days' => 30,
                'is_active' => true
            ],
            [
                'name' => 'Sales Representative Onboarding',
                'description' => 'Onboarding process for sales team members',
                'department_id' => $departments->where('name', 'Sales')->first()->id ?? null,
                'duration_days' => 21,
                'is_active' => true
            ],
            [
                'name' => 'HR Specialist Onboarding',
                'description' => 'Onboarding for HR department staff',
                'department_id' => $departments->where('name', 'Human Resources')->first()->id ?? null,
                'duration_days' => 14,
                'is_active' => true
            ],
            [
                'name' => 'Marketing Team Onboarding',
                'description' => 'Onboarding process for marketing professionals',
                'department_id' => $departments->where('name', 'Marketing')->first()->id ?? null,
                'duration_days' => 21,
                'is_active' => true
            ],
            [
                'name' => 'General Employee Onboarding',
                'description' => 'Standard onboarding for all employees',
                'department_id' => null,
                'duration_days' => 7,
                'is_active' => true
            ]
        ];

        $createdTemplates = [];
        foreach ($templates as $templateData) {
            $createdTemplates[] = OnboardingTemplate::create($templateData);
        }

        // Create Template Tasks
        $engineerTasks = [
            ['day' => 1, 'name' => 'Welcome & Office Tour', 'desc' => 'Meet the team and tour the office facilities'],
            ['day' => 1, 'name' => 'IT Setup', 'desc' => 'Setup laptop, email, and development tools'],
            ['day' => 2, 'name' => 'HR Documentation', 'desc' => 'Complete all HR paperwork and agreements'],
            ['day' => 3, 'name' => 'Code Repository Access', 'desc' => 'Get access to GitHub and review coding standards'],
            ['day' => 5, 'name' => 'First Code Commit', 'desc' => 'Make your first code contribution'],
            ['day' => 7, 'name' => 'Team Meeting Attendance', 'desc' => 'Attend daily standup and weekly planning'],
            ['day' => 10, 'name' => 'Architecture Overview', 'desc' => 'Learn about system architecture and tech stack'],
            ['day' => 14, 'name' => 'First Feature Deployment', 'desc' => 'Deploy your first feature to staging'],
            ['day' => 21, 'name' => 'Code Review Training', 'desc' => 'Learn code review process and best practices'],
            ['day' => 30, 'name' => 'Onboarding Feedback', 'desc' => 'Complete onboarding feedback survey']
        ];

        foreach ($engineerTasks as $task) {
            OnboardingTemplateTask::create([
                'template_id' => $createdTemplates[0]->id,
                'title' => $task['name'],
                'description' => $task['desc'],
                'day_number' => $task['day'],
                'is_mandatory' => true
            ]);
        }

        $salesTasks = [
            ['day' => 1, 'name' => 'Sales Team Introduction', 'desc' => 'Meet sales team and managers'],
            ['day' => 1, 'name' => 'CRM System Training', 'desc' => 'Learn to use the CRM system'],
            ['day' => 3, 'name' => 'Product Knowledge Training', 'desc' => 'Complete product training modules'],
            ['day' => 5, 'name' => 'Sales Process Overview', 'desc' => 'Understand the complete sales cycle'],
            ['day' => 7, 'name' => 'Shadow Senior Rep', 'desc' => 'Shadow a senior sales representative'],
            ['day' => 10, 'name' => 'First Client Call', 'desc' => 'Make your first supervised client call'],
            ['day' => 14, 'name' => 'Territory Assignment', 'desc' => 'Receive your sales territory assignment'],
            ['day' => 21, 'name' => 'First Solo Deal', 'desc' => 'Close your first independent deal']
        ];

        foreach ($salesTasks as $task) {
            OnboardingTemplateTask::create([
                'template_id' => $createdTemplates[1]->id,
                'title' => $task['name'],
                'description' => $task['desc'],
                'day_number' => $task['day'],
                'is_mandatory' => true
            ]);
        }

        // Create some ongoing onboarding processes
        $allEmployees = Employee::with('user')->get();
        $employees = $allEmployees->take(3);
        $buddyPool = $allEmployees->skip(1);

        foreach ($employees as $index => $employee) {
            $template = $createdTemplates[array_rand($createdTemplates)];
            $startDate = now()->subDays(rand(5, 20));
            $statuses = ['not_started', 'in_progress', 'in_progress', 'completed'];
            $status = $statuses[array_rand($statuses)];
            $buddy = $buddyPool->where('id', '!=', $employee->id)->random();
            
            $onboarding = EmployeeOnboarding::create([
                'employee_id' => $employee->id,
                'template_id' => $template->id,
                'start_date' => $startDate->format('Y-m-d'),
                'expected_completion_date' => $startDate->copy()->addDays($template->duration_days)->format('Y-m-d'),
                'actual_completion_date' => $status === 'completed' ? $startDate->copy()->addDays(rand(15, $template->duration_days))->format('Y-m-d') : null,
                'buddy_id' => $buddy->id,
                'status' => $status,
                'completion_percentage' => $status === 'completed' ? 100 : ($status === 'in_progress' ? rand(20, 80) : 0)
            ]);

            // Create tasks for this onboarding
            $templateTasks = $template->tasks;
            $completedBy = User::where('role', 'admin')->first();
            
            foreach ($templateTasks as $templateTask) {
                $taskStatus = ['pending', 'in_progress', 'completed', 'completed', 'pending'][array_rand(['pending', 'in_progress', 'completed', 'completed', 'pending'])];
                $dueDate = $startDate->copy()->addDays($templateTask->day_number);
                
                EmployeeOnboardingTask::create([
                    'onboarding_id' => $onboarding->id,
                    'template_task_id' => $templateTask->id,
                    'title' => $templateTask->title,
                    'description' => $templateTask->description,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'completed_date' => $taskStatus === 'completed' ? $dueDate->copy()->subDays(rand(0, 2))->format('Y-m-d') : null,
                    'completed_by' => $taskStatus === 'completed' ? $completedBy->id : null,
                    'status' => $taskStatus,
                    'notes' => $taskStatus === 'completed' ? 'Task completed successfully' : null
                ]);
            }
        }

        $this->command->info('Onboarding data created successfully!');
    }
}
