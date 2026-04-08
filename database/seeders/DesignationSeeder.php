<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            // Executive Level
            ['title' => 'Chief Executive Officer', 'level' => 1, 'description' => 'Top executive responsible for overall operations'],
            ['title' => 'Chief Technology Officer', 'level' => 1, 'description' => 'Head of technology department'],
            ['title' => 'Chief Financial Officer', 'level' => 1, 'description' => 'Head of finance department'],
            ['title' => 'Chief Operating Officer', 'level' => 1, 'description' => 'Head of operations'],
            
            // Senior Management
            ['title' => 'Vice President', 'level' => 2, 'description' => 'Senior leadership position'],
            ['title' => 'Director', 'level' => 2, 'description' => 'Department director'],
            ['title' => 'Senior Manager', 'level' => 3, 'description' => 'Experienced management position'],
            
            // Middle Management
            ['title' => 'Manager', 'level' => 4, 'description' => 'Team manager'],
            ['title' => 'Assistant Manager', 'level' => 5, 'description' => 'Supports manager in team operations'],
            ['title' => 'Team Lead', 'level' => 5, 'description' => 'Leads a team of employees'],
            ['title' => 'Project Manager', 'level' => 4, 'description' => 'Manages projects and deliverables'],
            
            // Senior Staff
            ['title' => 'Senior Engineer', 'level' => 6, 'description' => 'Experienced engineering professional'],
            ['title' => 'Senior Developer', 'level' => 6, 'description' => 'Experienced software developer'],
            ['title' => 'Senior Analyst', 'level' => 6, 'description' => 'Experienced business analyst'],
            ['title' => 'Senior Consultant', 'level' => 6, 'description' => 'Experienced consultant'],
            
            // Mid-Level Staff
            ['title' => 'Software Engineer', 'level' => 7, 'description' => 'Software development professional'],
            ['title' => 'Developer', 'level' => 7, 'description' => 'Software developer'],
            ['title' => 'Business Analyst', 'level' => 7, 'description' => 'Business analysis professional'],
            ['title' => 'QA Engineer', 'level' => 7, 'description' => 'Quality assurance professional'],
            ['title' => 'HR Executive', 'level' => 7, 'description' => 'Human resources professional'],
            ['title' => 'Accountant', 'level' => 7, 'description' => 'Financial accounting professional'],
            ['title' => 'Marketing Executive', 'level' => 7, 'description' => 'Marketing professional'],
            ['title' => 'Sales Executive', 'level' => 7, 'description' => 'Sales professional'],
            
            // Junior Staff
            ['title' => 'Junior Engineer', 'level' => 8, 'description' => 'Entry-level engineering position'],
            ['title' => 'Junior Developer', 'level' => 8, 'description' => 'Entry-level developer'],
            ['title' => 'Junior Analyst', 'level' => 8, 'description' => 'Entry-level analyst'],
            ['title' => 'Associate', 'level' => 8, 'description' => 'Entry-level professional'],
            ['title' => 'Trainee', 'level' => 9, 'description' => 'Training position'],
            ['title' => 'Intern', 'level' => 10, 'description' => 'Internship position'],
        ];

        foreach ($designations as $designation) {
            Designation::create($designation);
        }
        
        $this->command->info('Created ' . count($designations) . ' designations');
    }
}
