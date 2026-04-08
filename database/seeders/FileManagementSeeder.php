<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FileCategory;
use App\Models\EmployeeFile;
use App\Models\FileAccessLog;
use App\Models\User;

class FileManagementSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();

        // Create File Categories
        $categories = [
            ['name' => 'Employee Documents', 'description' => 'Personal and employment documents', 'parent_id' => null],
            ['name' => 'Contracts', 'description' => 'Employment contracts and agreements', 'parent_id' => null],
            ['name' => 'Certifications', 'description' => 'Professional certifications and licenses', 'parent_id' => null],
            ['name' => 'Policies', 'description' => 'Company policies and procedures', 'parent_id' => null],
            ['name' => 'Training Materials', 'description' => 'Training resources and materials', 'parent_id' => null],
            ['name' => 'Payroll Documents', 'description' => 'Payslips and tax documents', 'parent_id' => null],
            ['name' => 'Performance Reviews', 'description' => 'Performance evaluation documents', 'parent_id' => null],
            ['name' => 'Miscellaneous', 'description' => 'Other documents', 'parent_id' => null]
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $createdCategories[] = FileCategory::create($categoryData);
        }

        // Create Employee Files
        $fileTypes = [
            ['title' => 'Employment Contract', 'category' => 1, 'confidential' => true, 'ext' => 'pdf'],
            ['title' => 'CNIC Copy', 'category' => 0, 'confidential' => true, 'ext' => 'pdf'],
            ['title' => 'Educational Certificates', 'category' => 2, 'confidential' => false, 'ext' => 'pdf'],
            ['title' => 'Professional Certifications', 'category' => 2, 'confidential' => false, 'ext' => 'pdf'],
            ['title' => 'Tax Returns', 'category' => 5, 'confidential' => true, 'ext' => 'pdf'],
            ['title' => 'Performance Review 2025', 'category' => 6, 'confidential' => true, 'ext' => 'pdf'],
            ['title' => 'NDA Agreement', 'category' => 1, 'confidential' => true, 'ext' => 'pdf'],
            ['title' => 'Onboarding Checklist', 'category' => 7, 'confidential' => false, 'ext' => 'pdf']
        ];

        foreach ($employees->take(15) as $employee) {
            foreach ($fileTypes as $index => $fileType) {
                // Not all employees have all files
                if ($index > 3 && rand(0, 1)) continue;

                $uploadedDate = now()->subDays(rand(30, 365));
                
                $file = EmployeeFile::create([
                    'employee_id' => $employee->id,
                    'category_id' => $createdCategories[$fileType['category']]->id,
                    'title' => $fileType['title'],
                    'description' => 'Employee ' . strtolower($fileType['title']) . ' document',
                    'file_name' => strtolower(str_replace(' ', '_', $fileType['title'])) . '_' . $employee->id . '.' . $fileType['ext'],
                    'file_path' => 'employee_files/' . $employee->id . '/' . strtolower(str_replace(' ', '_', $fileType['title'])) . '_' . $employee->id . '.' . $fileType['ext'],
                    'file_size' => rand(100000, 5000000),
                    'file_type' => 'application/pdf',
                    'version' => 1,
                    'is_confidential' => $fileType['confidential'],
                    'uploaded_by' => $employee->id,
                    'expiry_date' => in_array($index, [0, 6]) ? now()->addYear()->format('Y-m-d') : null,
                    'created_at' => $uploadedDate,
                    'updated_at' => $uploadedDate
                ]);

                // Some files have multiple versions
                if (rand(0, 10) > 7) {
                    EmployeeFile::create([
                        'employee_id' => $employee->id,
                        'category_id' => $file->category_id,
                        'title' => $file->title,
                        'description' => $file->description . ' (Updated)',
                        'file_name' => $file->file_name,
                        'file_path' => str_replace('.' . $fileType['ext'], '_v2.' . $fileType['ext'], $file->file_path),
                        'file_size' => rand(100000, 5000000),
                        'file_type' => 'application/pdf',
                        'version' => 2,
                        'is_confidential' => $file->is_confidential,
                        'uploaded_by' => $employee->id,
                        'expiry_date' => $file->expiry_date,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(1, 30))
                    ]);
                }

                // Create access logs
                for ($j = 0; $j < rand(2, 8); $j++) {
                    $accessedAt = now()->subDays(rand(1, 90));
                    FileAccessLog::create([
                        'file_id' => $file->id,
                        'user_id' => [$employee->id, User::where('role', 'manager')->first()->id][array_rand([$employee->id, User::where('role', 'manager')->first()->id])],
                        'action' => ['view', 'view', 'view', 'download', 'edit'][array_rand(['view', 'view', 'view', 'download', 'edit'])],
                        'accessed_at' => $accessedAt,
                        'created_at' => $accessedAt,
                        'updated_at' => $accessedAt
                    ]);
                }
            }
        }

        // Create company-wide policy documents
        $policies = [
            'Company Code of Conduct',
            'Information Security Policy',
            'Work From Home Policy',
            'Leave Policy',
            'Travel & Expense Policy',
            'Performance Management Guidelines'
        ];

        foreach ($policies as $policy) {
            $file = EmployeeFile::create([
                'employee_id' => null,
                'category_id' => $createdCategories[3]->id,
                'title' => $policy,
                'description' => $policy . ' - Effective from January 2025',
                'file_name' => strtolower(str_replace(' ', '_', $policy)) . '.pdf',
                'file_path' => 'policies/' . strtolower(str_replace(' ', '_', $policy)) . '.pdf',
                'file_size' => rand(200000, 1000000),
                'file_type' => 'application/pdf',
                'version' => 1,
                'is_confidential' => false,
                'uploaded_by' => User::where('role', 'admin')->first()->id,
                'expiry_date' => null,
                'created_at' => now()->subDays(rand(90, 365)),
                'updated_at' => now()->subDays(rand(90, 365))
            ]);

            // Many employees have accessed these policies
            $employeeCount = $employees->count();
            $accessCount = min(rand(1, $employeeCount), max(1, (int)($employeeCount * 0.8)));
            foreach ($employees->random($accessCount) as $emp) {
                $accessedAt = now()->subDays(rand(1, 60));
                FileAccessLog::create([
                    'file_id' => $file->id,
                    'user_id' => $emp->id,
                    'action' => ['view', 'view', 'download'][array_rand(['view', 'view', 'download'])],
                    'accessed_at' => $accessedAt,
                    'created_at' => $accessedAt,
                    'updated_at' => $accessedAt
                ]);
            }
        }

        $this->command->info('File Management data created successfully!');
    }
}
