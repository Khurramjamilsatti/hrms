<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Timesheet;
use App\Models\User;

class TimesheetSeeder extends Seeder
{
    public function run(): void
    {
        $allEmployees = Employee::with('user')->get();
        $managers = $allEmployees->filter(fn($e) => in_array($e->user->role, ['manager', 'admin']));
        $employees = $allEmployees;

        // If no managers, use first employee
        if ($managers->isEmpty()) {
            $managers = $allEmployees->take(1);
        }

        // Create Projects
        $projects = [
            [
                'name' => 'HRMS Development',
                'client_id' => null,
                'manager_id' => $managers->first()->id,
                'start_date' => '2026-01-01',
                'end_date' => '2026-06-30',
                'budget' => 5000000,
                'description' => 'Complete HR Management System development',
                'status' => 'active'
            ],
            [
                'name' => 'Mobile App Development',
                'client_id' => null,
                'manager_id' => $managers->first()->id,
                'start_date' => '2026-01-01',
                'end_date' => '2026-05-31',
                'budget' => 3500000,
                'description' => 'React Native mobile application for Tech Solutions Ltd',
                'status' => 'active'
            ],
            [
                'name' => 'E-Commerce Platform',
                'client_id' => null,
                'manager_id' => $managers->count() > 1 ? $managers->skip(1)->first()->id : $managers->first()->id,
                'start_date' => '2026-01-15',
                'end_date' => '2026-04-15',
                'budget' => 4500000,
                'description' => 'Full-featured e-commerce solution for Retail Corp',
                'status' => 'active'
            ],
            [
                'name' => 'CRM System',
                'client_id' => null,
                'manager_id' => $managers->first()->id,
                'start_date' => '2025-11-01',
                'end_date' => '2026-01-31',
                'budget' => 2500000,
                'description' => 'Customer Relationship Management system for Sales Pro Inc',
                'status' => 'completed'
            ],
            [
                'name' => 'Data Migration Project',
                'client_id' => null,
                'manager_id' => $managers->count() > 1 ? $managers->skip(1)->first()->id : $managers->first()->id,
                'start_date' => '2026-03-01',
                'end_date' => null,
                'budget' => 1500000,
                'description' => 'Legacy system data migration for Enterprise Solutions',
                'status' => 'planning'
            ]
        ];

        $createdProjects = [];
        foreach ($projects as $projectData) {
            $createdProjects[] = Project::create($projectData);
        }

        // Create Tasks for each project
        $taskTemplates = [
            ['name' => 'Requirements Analysis', 'priority' => 'high'],
            ['name' => 'UI/UX Design', 'priority' => 'high'],
            ['name' => 'Backend Development', 'priority' => 'high'],
            ['name' => 'Frontend Development', 'priority' => 'high'],
            ['name' => 'API Integration', 'priority' => 'medium'],
            ['name' => 'Testing & QA', 'priority' => 'high'],
            ['name' => 'Documentation', 'priority' => 'medium'],
            ['name' => 'Deployment', 'priority' => 'high'],
            ['name' => 'Bug Fixes', 'priority' => 'medium'],
            ['name' => 'Code Review', 'priority' => 'medium']
        ];

        $createdTasks = [];
        foreach ($createdProjects as $project) {
            foreach ($taskTemplates as $index => $template) {
                $createdTasks[] = ProjectTask::create([
                    'project_id' => $project->id,
                    'title' => $template['name'],
                    'description' => 'Complete ' . $template['name'] . ' for ' . $project->name,
                    'assigned_to' => $employees->random()->id,  // employee.id
                    'priority' => $template['priority'],
                    'status' => $index < 5 ? 'completed' : ($index < 8 ? 'in_progress' : 'todo'),
                    'due_date' => now()->addDays(rand(10, 60))->format('Y-m-d')
                ]);
            }
        }

        // Create Timesheets for all employees
        $statuses = ['draft', 'submitted', 'approved', 'rejected'];
        $descriptions = [
            'Implemented new feature', 'Fixed critical bug', 'Code review and refactoring',
            'Database optimization', 'API endpoint development', 'Unit testing',
            'Frontend UI improvements', 'Documentation update', 'Sprint planning meeting',
            'Client requirements discussion', 'Deployed to staging', 'Performance tuning',
        ];
        $tasksCollection = collect($createdTasks);

        foreach ($employees as $employee) {
            // Each employee gets 20-30 timesheet entries across last 60 days
            for ($i = 0; $i < rand(20, 30); $i++) {
                $project = $createdProjects[array_rand($createdProjects)];
                $projectTasks = $tasksCollection->where('project_id', $project->id);
                $task = $projectTasks->isNotEmpty() ? $projectTasks->random() : null;
                $date = now()->subDays(rand(0, 60))->format('Y-m-d');
                $startHour = rand(8, 11);
                $duration = rand(2, 8);
                
                Timesheet::create([
                    'employee_id' => $employee->id,
                    'project_id' => $project->id,
                    'task_id' => $task?->id,
                    'date' => $date,
                    'start_time' => sprintf('%02d:00:00', $startHour),
                    'end_time' => sprintf('%02d:00:00', $startHour + $duration),
                    'hours_worked' => $duration * 60 + rand(0, 3) * 15,
                    'description' => $descriptions[array_rand($descriptions)],
                    'billable' => rand(0, 1),
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        }

        $this->command->info('Timesheet data created successfully!');
    }
}
