<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketCategory;
use App\Models\HelpdeskTicket;
use App\Models\TicketReply;
use App\Models\User;

class HelpdeskSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();
        $managers = User::where('role', 'manager')->get();
        if ($managers->isEmpty()) {
            $managers = User::where('role', 'admin')->get();
        }
        $admins = User::where('role', 'admin')->get();

        // Create Ticket Categories
        $categories = [
            ['name' => 'Payroll Issues', 'description' => 'Salary, deductions, and payslip related queries', 'department_id' => null, 'is_active' => true],
            ['name' => 'Leave & Attendance', 'description' => 'Leave applications and attendance queries', 'department_id' => null, 'is_active' => true],
            ['name' => 'IT Support', 'description' => 'Technical issues and system access', 'department_id' => null, 'is_active' => true],
            ['name' => 'HR Policies', 'description' => 'Questions about company policies and procedures', 'department_id' => null, 'is_active' => true],
            ['name' => 'Benefits', 'description' => 'Health insurance, provident fund queries', 'department_id' => null, 'is_active' => true],
            ['name' => 'Training', 'description' => 'Training enrollment and certificate issues', 'department_id' => null, 'is_active' => true],
            ['name' => 'Equipment', 'description' => 'Office equipment and asset requests', 'department_id' => null, 'is_active' => true],
            ['name' => 'Other', 'description' => 'General queries and other issues', 'department_id' => null, 'is_active' => true]
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $createdCategories[] = TicketCategory::create($categoryData);
        }

        // Create Tickets
        $subjects = [
            'Salary not received for last month',
            'Unable to download payslip',
            'Leave application not showing in system',
            'Cannot access email account',
            'Need clarification on medical policy',
            'Training certificate not issued',
            'Laptop keyboard not working',
            'Overtime payment query',
            'How to apply for work from home',
            'Password reset required',
            'Provident fund contribution query',
            'Travel expense reimbursement status',
            'Need access to project repository',
            'Annual leave balance incorrect',
            'System showing wrong attendance'
        ];

        $descriptions = [
            'I have not received my salary for last month. Please check and resolve this issue urgently.',
            'When I try to download my payslip from the portal, it shows an error. Kindly fix this.',
            'I submitted a leave application 3 days ago but it\'s not showing in my profile.',
            'My email account is locked and I cannot access it. Please help me reset it.',
            'I need details about the medical insurance policy and how to claim.',
            'I completed the Laravel training last month but haven\'t received the certificate yet.',
            'My laptop keyboard stopped working. Some keys are not responding.',
            'I worked 10 hours overtime last week but it\'s not reflected in the timesheet.',
            'What is the process to apply for work from home? Is approval required?',
            'I forgot my system password and need it to be reset urgently.',
            'I want to check my provident fund contribution details for this year.',
            'I submitted travel expenses 2 weeks ago. What is the status of reimbursement?',
            'I need access to the GitHub repository for the new project.',
            'My leave balance shows 5 days but I should have 10 days remaining.',
            'The system is showing me absent on days when I was present in office.'
        ];

        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['open', 'in_progress', 'resolved', 'closed', 'reopened'];

        foreach ($employees->take(20) as $employee) {
            for ($i = 0; $i < rand(1, 4); $i++) {
                $categoryIndex = array_rand($createdCategories);
                $subjectIndex = array_rand($subjects);
                
                $createdDays = rand(1, 30);
                $status = $createdDays > 20 ? 'closed' : ($createdDays > 10 ? 'resolved' : ($createdDays > 5 ? 'in_progress' : 'open'));
                
                $ticket = HelpdeskTicket::create([
                    'employee_id' => $employee->id,
                    'category_id' => $createdCategories[$categoryIndex]->id,
                    'ticket_number' => 'TKT-' . strtoupper(uniqid()),
                    'subject' => $subjects[$subjectIndex],
                    'description' => $descriptions[$subjectIndex],
                    'priority' => $priorities[array_rand($priorities)],
                    'status' => $status,
                    'assigned_to' => $status !== 'open' ? $managers->random()->id : null,
                    'assigned_at' => $status !== 'open' ? now()->subDays($createdDays - 1) : null,
                    'resolved_at' => in_array($status, ['resolved', 'closed']) ? now()->subDays(rand(1, 5)) : null,
                    'closed_at' => $status === 'closed' ? now()->subDays(rand(1, 3)) : null,
                    'resolution_notes' => in_array($status, ['resolved', 'closed']) ? 'Issue resolved successfully. All necessary steps taken.' : null,
                    'rating' => $status === 'closed' ? rand(3, 5) : null,
                    'feedback' => $status === 'closed' && rand(0, 1) ? 'Thank you for the quick resolution!' : null,
                    'created_at' => now()->subDays($createdDays),
                    'updated_at' => now()->subDays(rand(1, $createdDays))
                ]);

                // Create replies if ticket is not just opened
                if ($status !== 'open') {
                    // Employee's initial clarification
                    if (rand(0, 1)) {
                        TicketReply::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => $employee->id,
                            'message' => 'This is urgent. Please look into it as soon as possible.',
                            'is_internal' => false,
                            'attachment' => null
                        ]);
                    }

                    // Manager's response
                    if ($ticket->assigned_to) {
                        TicketReply::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => $ticket->assigned_to,
                            'message' => 'Thank you for contacting us. I am looking into this issue and will update you soon.',
                            'is_internal' => false,
                            'attachment' => null
                        ]);

                        // Internal note
                        TicketReply::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => $ticket->assigned_to,
                            'message' => 'Checking with IT team. Waiting for their response.',
                            'is_internal' => true,
                            'attachment' => null
                        ]);

                        // Resolution message
                        if (in_array($status, ['resolved', 'closed'])) {
                            TicketReply::create([
                                'ticket_id' => $ticket->id,
                                'user_id' => $ticket->assigned_to,
                                'message' => 'The issue has been resolved. Please check and confirm if everything is working fine now.',
                                'is_internal' => false,
                                'attachment' => null
                            ]);

                            // Employee confirmation
                            if ($status === 'closed') {
                                TicketReply::create([
                                    'ticket_id' => $ticket->id,
                                    'user_id' => $employee->id,
                                    'message' => 'Yes, everything is working fine now. Thank you for your help!',
                                    'is_internal' => false,
                                    'attachment' => null
                                ]);
                            }
                        }
                    }
                }
            }
        }

        $this->command->info('Helpdesk data created successfully!');
    }
}
