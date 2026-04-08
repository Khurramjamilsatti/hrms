<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Human Resources', 'description' => 'Manages employee relations, recruitment, and training', 'is_active' => true],
            ['name' => 'Information Technology', 'description' => 'Manages IT infrastructure, software development, and cybersecurity', 'is_active' => true],
            ['name' => 'Finance & Accounting', 'description' => 'Manages company finances, accounting, and budgeting', 'is_active' => true],
            ['name' => 'Marketing & Sales', 'description' => 'Manages marketing campaigns, sales, and customer relations', 'is_active' => true],
            ['name' => 'Operations', 'description' => 'Manages day-to-day business operations and logistics', 'is_active' => true],
            ['name' => 'Customer Support', 'description' => 'Handles customer inquiries and support tickets', 'is_active' => true],
            ['name' => 'Product Development', 'description' => 'Manages product design, development, and innovation', 'is_active' => true],
            ['name' => 'Quality Assurance', 'description' => 'Ensures product quality through testing and validation', 'is_active' => true],
            ['name' => 'Legal & Compliance', 'description' => 'Manages legal matters and regulatory compliance', 'is_active' => true],
            ['name' => 'Research & Development', 'description' => 'Conducts research and develops new technologies', 'is_active' => true],
            ['name' => 'Business Development', 'description' => 'Identifies and pursues business opportunities', 'is_active' => true],
            ['name' => 'Supply Chain Management', 'description' => 'Manages procurement, inventory, and logistics', 'is_active' => true],
            ['name' => 'Administration', 'description' => 'Manages administrative tasks and office operations', 'is_active' => true],
            ['name' => 'Public Relations', 'description' => 'Manages company image and media relations', 'is_active' => true],
            ['name' => 'Training & Development', 'description' => 'Provides employee training and skill development programs', 'is_active' => true],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
        
        $this->command->info('Created ' . count($departments) . ' departments');
    }
}
