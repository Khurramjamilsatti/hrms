<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeCv;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;

class CvBankSeeder extends Seeder
{
    public function run()
    {
        $employees = Employee::with('user')->get();
        
        if ($employees->count() < 3) {
            $this->command->info('Not enough employees to seed CVs. Please run EmployeeSeeder first.');
            return;
        }

        // Ensure CVs directory exists
        Storage::makeDirectory('public/cvs');

        $educationLevels = ['High School', 'Bachelor', 'Masters', 'PhD'];
        $skillsSets = [
            ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'Git'],
            ['Python', 'Django', 'React', 'PostgreSQL', 'Docker'],
            ['Java', 'Spring Boot', 'Angular', 'MongoDB', 'Kubernetes'],
            ['JavaScript', 'Node.js', 'React Native', 'AWS', 'CI/CD'],
            ['C#', '.NET Core', 'Azure', 'SQL Server', 'DevOps'],
        ];

        $certificationsSets = [
            ['AWS Certified Solutions Architect', 'PMP'],
            ['Google Cloud Professional', 'Scrum Master'],
            ['Microsoft Certified Azure Developer', 'ITIL'],
            ['Oracle Certified Professional', 'Six Sigma'],
            ['Cisco CCNA', 'CompTIA Security+'],
        ];

        // Create CVs for 80% of employees
        foreach ($employees->random(min(ceil($employees->count() * 0.8), $employees->count())) as $index => $employee) {
            $experienceYears = rand(1, 15);
            $versionCount = rand(1, 3); // Each employee may have 1-3 CV versions
            
            for ($v = 1; $v <= $versionCount; $v++) {
                $isCurrent = ($v === $versionCount); // Last version is current
                $skills = $skillsSets[rand(0, 4)];
                $certifications = rand(0, 1) ? $certificationsSets[rand(0, 4)] : [];
                
                // Create dummy file path (in real scenario, actual files would be uploaded)
                $fileName = $employee->user->name . '_CV_v' . $v . '.pdf';
                $filePath = 'cvs/' . time() . '_' . str_replace(' ', '_', $fileName);
                
                // Create a placeholder file
                Storage::put('public/' . $filePath, 'This is a dummy CV file for ' . $employee->user->name);

                EmployeeCv::create([
                    'employee_id' => $employee->id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'version' => $v,
                    'experience_years' => $experienceYears + ($v - 1), // Experience grows with versions
                    'education_level' => $educationLevels[rand(0, 3)],
                    'summary' => $this->generateSummary($employee->user->name, $experienceYears + ($v - 1)),
                    'skills' => $skills,
                    'certifications' => $certifications,
                    'is_current' => $isCurrent,
                    'uploaded_at' => now()->subDays(rand(1, 365 - ($v * 30))),
                ]);
            }
        }

        $this->command->info('CV Bank seeded successfully!');
    }

    private function generateSummary($name, $experience)
    {
        $summaries = [
            "Experienced software developer with $experience years of expertise in full-stack development. Proven track record in delivering high-quality solutions.",
            "Dedicated professional with $experience years in software engineering. Strong problem-solving skills and ability to work in team environments.",
            "Results-driven developer with $experience years of experience. Specializes in modern web technologies and agile methodologies.",
            "Skilled IT professional with $experience years of hands-on experience. Passionate about learning new technologies and best practices.",
            "Senior developer with $experience years of experience in enterprise applications. Expert in designing scalable and maintainable solutions.",
        ];

        return $summaries[rand(0, 4)];
    }
}
