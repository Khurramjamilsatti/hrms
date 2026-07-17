<?php

namespace Database\Seeders;

use App\Models\LandingFeature;
use App\Models\LandingSetting;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        LandingSetting::current()->update([
            'brand_name' => 'Payroll Digital',
            'brand_tagline' => 'HR Management System',
            'hero_title' => 'Modern HR & Payroll for Growing Teams',
            'hero_subtitle' => 'Run payroll, attendance, leaves, and employee records from one beautiful platform designed for clarity and speed.',
            'hero_cta_text' => 'Sign In to HRMS',
            'hero_cta_link' => '/login',
            'hero_secondary_cta_text' => 'See Features',
            'hero_secondary_cta_link' => '#features',
            'about_title' => 'People operations, simplified',
            'about_body' => 'Payroll Digital brings payroll, attendance, leave, recruitment, and employee self-service together so HR and finance teams can focus on people — not paperwork.',
            'features_title' => 'Everything you need to run HR',
            'features_subtitle' => 'Powerful modules that work together out of the box.',
            'stats_title' => 'Built for real workplaces',
            'testimonials_title' => 'Loved by HR & finance teams',
            'cta_title' => 'Ready to simplify your HR stack?',
            'cta_body' => 'Sign in to manage payroll, attendance, and your entire workforce in one place.',
            'cta_button_text' => 'Go to Login',
            'cta_button_link' => '/login',
            'contact_email' => 'hello@payroll-digital.com',
            'footer_text' => '© ' . date('Y') . ' Payroll Digital. All rights reserved.',
            'is_published' => true,
        ]);

        if (LandingFeature::count() === 0) {
            $features = [
                ['icon' => 'payroll', 'title' => 'Payroll Processing', 'description' => 'Generate, review, and mark payroll paid with clear earnings and deductions.', 'sort_order' => 1],
                ['icon' => 'attendance', 'title' => 'Attendance Tracking', 'description' => 'Track check-ins, summaries, and presence with manager-friendly views.', 'sort_order' => 2],
                ['icon' => 'leaves', 'title' => 'Leave Management', 'description' => 'Apply, approve, and balance leave types with multi-level workflows.', 'sort_order' => 3],
                ['icon' => 'employees', 'title' => 'Employee Records', 'description' => 'Keep profiles, documents, salaries, and org structure in sync.', 'sort_order' => 4],
                ['icon' => 'shifts', 'title' => 'Shift Scheduling', 'description' => 'Plan rosters, assign shifts, and let teams see their schedules.', 'sort_order' => 5],
                ['icon' => 'recruitment', 'title' => 'Recruitment & CV Bank', 'description' => 'Manage openings, applications, and candidate CVs in one place.', 'sort_order' => 6],
            ];
            foreach ($features as $feature) {
                LandingFeature::create($feature + ['is_active' => true]);
            }
        }

        if (LandingStat::count() === 0) {
            foreach ([
                ['label' => 'Modules', 'value' => '25+', 'icon' => 'modules', 'sort_order' => 1],
                ['label' => 'HR Workflows', 'value' => 'End-to-end', 'icon' => 'workflows', 'sort_order' => 2],
                ['label' => 'Self-service', 'value' => 'Employees', 'icon' => 'users', 'sort_order' => 3],
                ['label' => 'Approvals', 'value' => 'Multi-level', 'icon' => 'approvals', 'sort_order' => 4],
            ] as $stat) {
                LandingStat::create($stat + ['is_active' => true]);
            }
        }

        if (LandingTestimonial::count() === 0) {
            foreach ([
                [
                    'name' => 'Ayesha Khan',
                    'role' => 'HR Manager',
                    'company' => 'Northwind Retail',
                    'quote' => 'Payroll Digital cut our monthly payroll cycle from days to hours. The leave and attendance views are exactly what managers needed.',
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Omar Farooq',
                    'role' => 'Finance Lead',
                    'company' => 'Brightpath Labs',
                    'quote' => 'Clear earnings, deductions, and advances in one place. Our finance team finally trusts the numbers without spreadsheet gymnastics.',
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Sara Ahmed',
                    'role' => 'People Ops',
                    'company' => 'Cascade Soft',
                    'quote' => 'Employees love the self-service portal. Recruitment and CV bank keep hiring organized without another tool.',
                    'sort_order' => 3,
                ],
            ] as $item) {
                LandingTestimonial::create($item + ['is_active' => true]);
            }
        }
    }
}
