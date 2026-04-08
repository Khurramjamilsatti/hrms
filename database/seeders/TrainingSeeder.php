<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingCourse;
use App\Models\TrainingSession;
use App\Models\TrainingEnrollment;
use App\Models\TrainingCertificate;
use App\Models\User;

class TrainingSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();

        // Create Training Courses
        $courses = [
            [
                'name' => 'Laravel Advanced Development',
                'description' => 'Master advanced Laravel concepts including queues, events, and microservices',
                'type' => 'technical',
                'duration_hours' => 24,
                'delivery_mode' => 'online',
                'cost' => 50000,
                'is_active' => true
            ],
            [
                'name' => 'Vue.js 3 Mastery',
                'description' => 'Complete guide to Vue 3 Composition API and state management',
                'type' => 'technical',
                'duration_hours' => 20,
                'delivery_mode' => 'online',
                'cost' => 45000,
                'is_active' => true
            ],
            [
                'name' => 'Leadership & Management Skills',
                'description' => 'Develop essential leadership and team management skills',
                'type' => 'leadership',
                'duration_hours' => 16,
                'delivery_mode' => 'classroom',
                'cost' => 75000,
                'is_active' => true
            ],
            [
                'name' => 'Effective Communication',
                'description' => 'Improve workplace communication and presentation skills',
                'type' => 'soft_skills',
                'duration_hours' => 8,
                'delivery_mode' => 'hybrid',
                'cost' => 30000,
                'is_active' => true
            ],
            [
                'name' => 'Data Protection & GDPR Compliance',
                'description' => 'Understanding data protection laws and compliance requirements',
                'type' => 'compliance',
                'duration_hours' => 6,
                'delivery_mode' => 'online',
                'cost' => 20000,
                'is_active' => true
            ],
            [
                'name' => 'Agile & Scrum Methodology',
                'description' => 'Learn Agile principles and Scrum framework implementation',
                'type' => 'technical',
                'duration_hours' => 12,
                'delivery_mode' => 'online',
                'cost' => 35000,
                'is_active' => true
            ],
            [
                'name' => 'Public Speaking Mastery',
                'description' => 'Overcome stage fear and deliver impactful presentations',
                'type' => 'soft_skills',
                'duration_hours' => 10,
                'delivery_mode' => 'classroom',
                'cost' => 40000,
                'is_active' => true
            ],
            [
                'name' => 'Docker & Kubernetes',
                'description' => 'Container orchestration and microservices deployment',
                'type' => 'technical',
                'duration_hours' => 18,
                'delivery_mode' => 'online',
                'cost' => 55000,
                'is_active' => true
            ]
        ];

        $createdCourses = [];
        foreach ($courses as $courseData) {
            $createdCourses[] = TrainingCourse::create($courseData);
        }

        // Create Training Sessions
        foreach ($createdCourses as $course) {
            // Past session (completed)
            $session1 = TrainingSession::create([
                'course_id' => $course->id,
                'session_name' => $course->name . ' - Q4 2025',
                'start_date' => now()->subDays(rand(60, 120))->format('Y-m-d'),
                'end_date' => now()->subDays(rand(30, 59))->format('Y-m-d'),
                'start_time' => '09:00',
                'end_time' => '17:00',
                'location' => $course->delivery_mode === 'online' ? null : 'Training Room A, 5th Floor',
                'meeting_link' => $course->delivery_mode === 'online' ? 'https://meet.google.com/' . uniqid() : null,
                'available_seats' => rand(15, 30),
                'status' => 'completed'
            ]);

            // Upcoming session
            $session2 = TrainingSession::create([
                'course_id' => $course->id,
                'session_name' => $course->name . ' - Q1 2026',
                'start_date' => now()->addDays(rand(10, 30))->format('Y-m-d'),
                'end_date' => now()->addDays(rand(40, 60))->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '18:00',
                'location' => $course->delivery_mode === 'online' ? null : 'Training Room B, 5th Floor',
                'meeting_link' => $course->delivery_mode === 'online' ? 'https://meet.google.com/' . uniqid() : null,
                'available_seats' => rand(15, 30),
                'status' => 'scheduled'
            ]);

            // Create enrollments for past session
            $employeeCount = $employees->count();
            $enrollmentCount = min(rand(1, $employeeCount), max(1, (int)($employeeCount * 0.7)));
            $enrolledEmployees = $employees->random($enrollmentCount);
            $adminUser = User::where('role', 'admin')->first();
            
            foreach ($enrolledEmployees as $employee) {
                $score = rand(50, 100);
                $passed = $score >= 70;
                $enrollment = TrainingEnrollment::create([
                    'session_id' => $session1->id,
                    'employee_id' => $employee->id,
                    'enrolled_by' => $adminUser->id,
                    'enrolled_date' => now()->subDays(rand(90, 130))->format('Y-m-d'),
                    'status' => 'completed',
                    'completion_date' => now()->subDays(rand(20, 40))->format('Y-m-d'),
                    'attendance_percentage' => rand(85, 100),
                    'score' => $score,
                    'feedback' => 'Great course! Very informative and well-structured.',
                    'rating' => rand(4, 5),
                    'certificate_issued' => $passed
                ]);

                // Issue certificate if passed
                if ($passed) {
                    TrainingCertificate::create([
                        'enrollment_id' => $enrollment->id,
                        'certificate_number' => 'CERT-' . strtoupper(uniqid()),
                        'issue_date' => now()->subDays(rand(20, 40))->format('Y-m-d'),
                        'expiry_date' => now()->addYears(2)->format('Y-m-d')
                    ]);
                }
            }

            // Create enrollments for upcoming session
            $upcomingCount = min(rand(1, $employeeCount), max(1, (int)($employeeCount * 0.5)));
            $upcomingEnrolled = $employees->random($upcomingCount);
            foreach ($upcomingEnrolled as $employee) {
                TrainingEnrollment::create([
                    'session_id' => $session2->id,
                    'employee_id' => $employee->id,
                    'enrolled_by' => $adminUser->id,
                    'enrolled_date' => now()->subDays(rand(1, 10))->format('Y-m-d'),
                    'status' => 'enrolled',
                    'attendance_percentage' => null,
                    'score' => null,
                    'feedback' => null,
                    'rating' => null
                ]);
            }
        }

        $this->command->info('Training data created successfully!');
    }
}
