<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $notificationTypes = [
            ['type' => 'leave_approved', 'title' => 'Leave Approved', 'message' => 'Your leave application has been approved', 'priority' => 'normal'],
            ['type' => 'leave_rejected', 'title' => 'Leave Rejected', 'message' => 'Your leave application has been rejected', 'priority' => 'high'],
            ['type' => 'payroll_generated', 'title' => 'Payroll Generated', 'message' => 'Your salary for this month has been processed', 'priority' => 'normal'],
            ['type' => 'timesheet_reminder', 'title' => 'Timesheet Reminder', 'message' => 'Please submit your timesheet for this week', 'priority' => 'normal'],
            ['type' => 'training_enrolled', 'title' => 'Training Enrollment', 'message' => 'You have been enrolled in a new training course', 'priority' => 'normal'],
            ['type' => 'shift_assigned', 'title' => 'Shift Assigned', 'message' => 'New shift schedule has been published', 'priority' => 'normal'],
            ['type' => 'document_uploaded', 'title' => 'Document Uploaded', 'message' => 'A new document has been uploaded to your profile', 'priority' => 'low'],
            ['type' => 'ticket_resolved', 'title' => 'Ticket Resolved', 'message' => 'Your helpdesk ticket has been resolved', 'priority' => 'normal'],
            ['type' => 'expense_approved', 'title' => 'Expense Approved', 'message' => 'Your expense claim has been approved', 'priority' => 'normal'],
            ['type' => 'meeting_reminder', 'title' => 'Meeting Reminder', 'message' => 'You have a meeting in 30 minutes', 'priority' => 'high']
        ];

        // Create notifications for each user
        foreach ($users as $user) {
            $notificationsToCreate = rand(5, 15);
            
            for ($i = 0; $i < $notificationsToCreate; $i++) {
                $notifData = $notificationTypes[array_rand($notificationTypes)];
                $createdAt = now()->subDays(rand(1, 30))->subHours(rand(0, 23));
                
                Notification::create([
                    'user_id' => $user->id,
                    'type' => $notifData['type'],
                    'title' => $notifData['title'],
                    'message' => $notifData['message'],
                    'priority' => $notifData['priority'],
                    'data' => json_encode([
                        'action' => 'view_details',
                        'resource_id' => rand(1, 100),
                        'resource_type' => ['leave', 'payroll', 'timesheet', 'training', 'expense'][array_rand(['leave', 'payroll', 'timesheet', 'training', 'expense'])]
                    ]),
                    'action_url' => ['/', '/leaves', '/payroll', '/timesheets', '/travel-expenses'][array_rand(['/', '/leaves', '/payroll', '/timesheets', '/travel-expenses'])],
                    'is_read' => rand(0, 10) > 6, // 60-70% unread
                    'read_at' => rand(0, 10) > 6 ? now()->subDays(rand(0, 5)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt
                ]);
            }

            // Create notification preferences for each user
            NotificationPreference::create([
                'user_id' => $user->id,
                'notification_type' => 'email',
                'enabled_events' => json_encode([
                    'leave_approved',
                    'leave_rejected',
                    'payroll_generated',
                    'expense_approved',
                    'ticket_resolved'
                ])
            ]);

            NotificationPreference::create([
                'user_id' => $user->id,
                'notification_type' => 'push',
                'enabled_events' => json_encode([
                    'meeting_reminder',
                    'timesheet_reminder',
                    'shift_assigned'
                ])
            ]);

            NotificationPreference::create([
                'user_id' => $user->id,
                'notification_type' => 'in_app',
                'enabled_events' => json_encode([
                    'leave_approved',
                    'leave_rejected',
                    'payroll_generated',
                    'timesheet_reminder',
                    'training_enrolled',
                    'shift_assigned',
                    'document_uploaded',
                    'ticket_resolved',
                    'expense_approved',
                    'meeting_reminder'
                ])
            ]);
        }

        $this->command->info('Notification data created successfully!');
    }
}
