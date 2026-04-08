<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalendarEvent;
use App\Models\EventAttendee;
use App\Models\Reminder;
use App\Models\User;

class CalendarSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();
        $managers = User::where('role', 'manager')->get();
        if ($managers->isEmpty()) {
            $managers = User::where('role', 'admin')->get();
        }
        $allUsers = User::all();

        // Company-wide events
        $companyEvents = [
            // Payroll and Salary Related Events
            [
                'title' => 'Salary Processing Day',
                'type' => 'company_event',
                'start' => now()->setDay(25)->setTime(0, 0, 0),
                'end' => now()->setDay(25)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'Monthly salary payment processing',
                'all_day' => true
            ],
            [
                'title' => 'Salary Processing Day',
                'type' => 'company_event',
                'start' => now()->addMonth()->setDay(25)->setTime(0, 0, 0),
                'end' => now()->addMonth()->setDay(25)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'Monthly salary payment processing',
                'all_day' => true
            ],
            [
                'title' => 'Payroll Verification Deadline',
                'type' => 'company_event',
                'start' => now()->setDay(20)->setTime(17, 0, 0),
                'end' => now()->setDay(20)->setTime(17, 0, 0),
                'location' => 'Finance Department',
                'desc' => 'Last date to submit payroll corrections and updates',
                'all_day' => false
            ],
            
            // Town Hall and Company Meetings
            [
                'title' => 'Town Hall Meeting - Q1 2026',
                'type' => 'company_event',
                'start' => now()->addDays(5)->setTime(10, 0, 0),
                'end' => now()->addDays(5)->setTime(11, 30, 0),
                'location' => 'Main Auditorium',
                'desc' => 'Quarterly company update and Q&A session with leadership',
                'all_day' => false
            ],
            [
                'title' => 'Board Meeting',
                'type' => 'meeting',
                'start' => now()->addDays(15)->setTime(14, 0, 0),
                'end' => now()->addDays(15)->setTime(17, 0, 0),
                'location' => 'Executive Conference Room',
                'desc' => 'Quarterly board meeting',
                'all_day' => false
            ],
            
            // Performance Reviews
            [
                'title' => 'Mid-Year Performance Review Cycle Begins',
                'type' => 'company_event',
                'start' => now()->addDays(20)->setTime(9, 0, 0),
                'end' => now()->addDays(40)->setTime(17, 0, 0),
                'location' => 'HR Department',
                'desc' => 'Mid-year performance review period for all employees',
                'all_day' => false
            ],
            [
                'title' => 'Probation Period Reviews',
                'type' => 'company_event',
                'start' => now()->addDays(10)->setTime(9, 0, 0),
                'end' => now()->addDays(12)->setTime(17, 0, 0),
                'location' => 'HR Department',
                'desc' => 'Probation period completion reviews for new employees',
                'all_day' => false
            ],
            
            // National Holidays (Pakistan)
            [
                'title' => 'Kashmir Day',
                'type' => 'holiday',
                'start' => now()->setMonth(2)->setDay(5)->setTime(0, 0, 0),
                'end' => now()->setMonth(2)->setDay(5)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'National Holiday - Office Closed',
                'all_day' => true
            ],
            [
                'title' => 'Pakistan Day',
                'type' => 'holiday',
                'start' => now()->setMonth(3)->setDay(23)->setTime(0, 0, 0),
                'end' => now()->setMonth(3)->setDay(23)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'National Holiday - Office Closed',
                'all_day' => true
            ],
            [
                'title' => 'Labour Day',
                'type' => 'holiday',
                'start' => now()->setMonth(5)->setDay(1)->setTime(0, 0, 0),
                'end' => now()->setMonth(5)->setDay(1)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'National Holiday - Office Closed',
                'all_day' => true
            ],
            [
                'title' => 'Independence Day',
                'type' => 'holiday',
                'start' => now()->setMonth(8)->setDay(14)->setTime(0, 0, 0),
                'end' => now()->setMonth(8)->setDay(14)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'National Holiday - Office Closed',
                'all_day' => true
            ],
            [
                'title' => 'Iqbal Day',
                'type' => 'holiday',
                'start' => now()->setMonth(11)->setDay(9)->setTime(0, 0, 0),
                'end' => now()->setMonth(11)->setDay(9)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'National Holiday - Office Closed',
                'all_day' => true
            ],
            [
                'title' => 'Quaid-e-Azam Birthday',
                'type' => 'holiday',
                'start' => now()->setMonth(12)->setDay(25)->setTime(0, 0, 0),
                'end' => now()->setMonth(12)->setDay(25)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'National Holiday - Office Closed',
                'all_day' => true
            ],
            [
                'title' => 'Eid-ul-Fitr Holidays',
                'type' => 'holiday',
                'start' => now()->addDays(60)->setTime(0, 0, 0),
                'end' => now()->addDays(63)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'Eid-ul-Fitr Holidays (3 days)',
                'all_day' => true
            ],
            [
                'title' => 'Eid-ul-Adha Holidays',
                'type' => 'holiday',
                'start' => now()->addDays(130)->setTime(0, 0, 0),
                'end' => now()->addDays(133)->setTime(23, 59, 59),
                'location' => null,
                'desc' => 'Eid-ul-Adha Holidays (3 days)',
                'all_day' => true
            ],
            
            // Company Events
            [
                'title' => 'Company Annual Picnic',
                'type' => 'company_event',
                'start' => now()->addDays(45)->setTime(11, 0, 0),
                'end' => now()->addDays(45)->setTime(17, 0, 0),
                'location' => 'Clifton Beach',
                'desc' => 'Annual company picnic and team building activities',
                'all_day' => false
            ],
            [
                'title' => 'Health & Safety Training',
                'type' => 'training',
                'start' => now()->addDays(25)->setTime(14, 0, 0),
                'end' => now()->addDays(25)->setTime(16, 0, 0),
                'location' => 'Training Hall',
                'desc' => 'Mandatory health and safety training for all employees',
                'all_day' => false
            ],
            [
                'title' => 'Fire Drill',
                'type' => 'company_event',
                'start' => now()->addDays(30)->setTime(11, 0, 0),
                'end' => now()->addDays(30)->setTime(11, 30, 0),
                'location' => 'All Floors',
                'desc' => 'Quarterly fire safety drill',
                'all_day' => false
            ],
            
            // Policy and Compliance
            [
                'title' => 'HR Policy Review Meeting',
                'type' => 'meeting',
                'start' => now()->addDays(18)->setTime(14, 0, 0),
                'end' => now()->addDays(18)->setTime(16, 0, 0),
                'location' => 'HR Conference Room',
                'desc' => 'Review and update company HR policies',
                'all_day' => false
            ],
            [
                'title' => 'Benefits Enrollment Deadline',
                'type' => 'company_event',
                'start' => now()->addDays(35)->setTime(17, 0, 0),
                'end' => now()->addDays(35)->setTime(17, 0, 0),
                'location' => 'HR Department',
                'desc' => 'Last date to enroll or update employee benefits',
                'all_day' => false
            ],
            [
                'title' => 'Tax Documentation Submission',
                'type' => 'company_event',
                'start' => now()->addDays(50)->setTime(17, 0, 0),
                'end' => now()->addDays(50)->setTime(17, 0, 0),
                'location' => 'Finance Department',
                'desc' => 'Deadline for tax documentation submission',
                'all_day' => false
            ],
            
            // Wellness and Culture
            [
                'title' => 'Mental Health Awareness Session',
                'type' => 'company_event',
                'start' => now()->addDays(28)->setTime(15, 0, 0),
                'end' => now()->addDays(28)->setTime(16, 30, 0),
                'location' => 'Main Hall',
                'desc' => 'Mental health and wellness awareness session',
                'all_day' => false
            ],
            [
                'title' => 'Employee Recognition Ceremony',
                'type' => 'company_event',
                'start' => now()->addDays(55)->setTime(16, 0, 0),
                'end' => now()->addDays(55)->setTime(18, 0, 0),
                'location' => 'Main Auditorium',
                'desc' => 'Quarterly employee recognition and awards ceremony',
                'all_day' => false
            ]
        ];

        foreach ($companyEvents as $eventData) {
            $event = CalendarEvent::create([
                'created_by' => User::where('role', 'admin')->first()->id,
                'title' => $eventData['title'],
                'description' => $eventData['desc'],
                'event_type' => $eventData['type'],
                'start_datetime' => $eventData['start'],
                'end_datetime' => $eventData['end'],
                'location' => $eventData['location'],
                'meeting_link' => null,
                'is_all_day' => $eventData['all_day'],
                'is_recurring' => false,
                'recurrence_rule' => null
            ]);

            // Add all employees as attendees for company events
            if ($eventData['type'] === 'company_event') {
                foreach ($allUsers->take(30) as $user) {
                    if ($user->employee) {
                        EventAttendee::create([
                            'event_id' => $event->id,
                            'employee_id' => $user->employee->id,
                            'status' => ['invited', 'accepted', 'accepted', 'accepted'][array_rand(['invited', 'accepted', 'accepted', 'accepted'])],
                            'response_note' => null
                        ]);

                        // Add reminder for some attendees
                        if (rand(0, 1)) {
                            Reminder::create([
                                'event_id' => $event->id,
                                'employee_id' => $user->employee->id,
                                'remind_before_minutes' => [15, 30, 60][array_rand([15, 30, 60])],
                                'is_sent' => false
                            ]);
                        }
                    }
                }
            }
        }

        // Team meetings
        foreach ($managers as $manager) {
            for ($i = 0; $i < rand(3, 6); $i++) {
                $daysAhead = $i * 7 + rand(1, 5);
                $startTime = rand(9, 15);
                
                $event = CalendarEvent::create([
                    'created_by' => $manager->id,
                    'title' => 'Team Sync Meeting',
                    'description' => 'Weekly team synchronization and updates',
                    'event_type' => 'meeting',
                    'start_datetime' => now()->addDays($daysAhead)->setTime($startTime, 0, 0),
                    'end_datetime' => now()->addDays($daysAhead)->setTime($startTime + 1, 0, 0),
                    'location' => 'Meeting Room ' . chr(65 + rand(0, 3)),
                    'meeting_link' => 'https://meet.google.com/' . strtolower(substr(md5(rand()), 0, 10)),
                    'is_all_day' => false,
                    'is_recurring' => true,
                    'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=MO'
                ]);

                // Add team members
                $employeeCount = $employees->count();
                $teamMemberCount = min(rand(5, 10), max(1, $employeeCount));
                $teamMembers = $employees->random($teamMemberCount);
                foreach ($teamMembers as $member) {
                    if ($member->employee) {
                        EventAttendee::create([
                            'event_id' => $event->id,
                            'employee_id' => $member->employee->id,
                            'status' => ['accepted', 'accepted', 'invited', 'tentative'][array_rand(['accepted', 'accepted', 'invited', 'tentative'])],
                            'response_note' => null
                        ]);

                        Reminder::create([
                            'event_id' => $event->id,
                            'employee_id' => $member->employee->id,
                            'remind_before_minutes' => 15,
                            'is_sent' => false
                        ]);
                    }
                }

                // Add manager as attendee
                if ($manager->employee) {
                    EventAttendee::create([
                        'event_id' => $event->id,
                        'employee_id' => $manager->employee->id,
                        'status' => 'accepted',
                        'response_note' => null
                    ]);
                }
            }
        }

        // Training sessions
        $trainingSessions = [
            'Laravel Best Practices Workshop',
            'Vue.js Advanced Techniques',
            'Agile Methodology Training',
            'Communication Skills Seminar',
            'Time Management Workshop'
        ];

        foreach ($trainingSessions as $index => $training) {
            $daysAhead = ($index + 1) * 10;
            $startTime = 14;
            
            $event = CalendarEvent::create([
                'created_by' => User::where('role', 'admin')->first()->id,
                'title' => $training,
                'description' => 'Professional development training session',
                'event_type' => 'training',
                'start_datetime' => now()->addDays($daysAhead)->setTime($startTime, 0, 0),
                'end_datetime' => now()->addDays($daysAhead)->setTime($startTime + 3, 0, 0),
                'location' => 'Training Room, 5th Floor',
                'meeting_link' => 'https://meet.google.com/' . strtolower(substr(md5(rand()), 0, 10)),
                'is_all_day' => false,
                'is_recurring' => false,
                'recurrence_rule' => null
            ]);

            $employeeCount = $employees->count();
            $attendeeCount = min(rand(8, 15), max(1, $employeeCount));
            $attendees = $employees->random($attendeeCount);
            foreach ($attendees as $attendee) {
                if ($attendee->employee) {
                    EventAttendee::create([
                        'event_id' => $event->id,
                        'employee_id' => $attendee->employee->id,
                        'status' => ['accepted', 'accepted', 'invited'][array_rand(['accepted', 'accepted', 'invited'])],
                        'response_note' => null
                    ]);

                    Reminder::create([
                        'event_id' => $event->id,
                        'employee_id' => $attendee->employee->id,
                        'remind_before_minutes' => 30,
                        'is_sent' => false
                    ]);
                }
            }
        }

        // Interviews
        for ($i = 0; $i < 10; $i++) {
            $daysAhead = rand(3, 30);
            $startTime = rand(10, 15);
            
            $event = CalendarEvent::create([
                'created_by' => $managers->random()->id,
                'title' => 'Candidate Interview - ' . ['Software Engineer', 'Product Manager', 'UI/UX Designer', 'Sales Executive'][array_rand(['Software Engineer', 'Product Manager', 'UI/UX Designer', 'Sales Executive'])],
                'description' => 'Interview with potential candidate',
                'event_type' => 'interview',
                'start_datetime' => now()->addDays($daysAhead)->setTime($startTime, 0, 0),
                'end_datetime' => now()->addDays($daysAhead)->setTime($startTime + 1, 0, 0),
                'location' => 'HR Meeting Room',
                'meeting_link' => 'https://meet.google.com/' . strtolower(substr(md5(rand()), 0, 10)),
                'is_all_day' => false,
                'is_recurring' => false,
                'recurrence_rule' => null
            ]);

            // Add interviewers
            $interviewers = collect([$managers->random(), $employees->random()]);
            foreach ($interviewers as $interviewer) {
                if ($interviewer->employee) {
                    EventAttendee::create([
                        'event_id' => $event->id,
                        'employee_id' => $interviewer->employee->id,
                        'status' => 'accepted',
                        'response_note' => null
                    ]);

                    Reminder::create([
                        'event_id' => $event->id,
                        'employee_id' => $interviewer->employee->id,
                        'remind_before_minutes' => 60,
                        'is_sent' => false
                    ]);
                }
            }
        }

        $this->command->info('Calendar data created successfully!');
    }
}
