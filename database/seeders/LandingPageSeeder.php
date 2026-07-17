<?php

namespace Database\Seeders;

use App\Models\LandingBlock;
use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingPage;
use App\Models\LandingPlan;
use App\Models\LandingSetting;
use App\Models\LandingStat;
use App\Models\LandingStep;
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
            'hero_cta_text' => 'Book a Demo',
            'hero_cta_link' => '/contact?intent=demo',
            'hero_secondary_cta_text' => 'See Features',
            'hero_secondary_cta_link' => '#features',
            'about_title' => 'People operations, simplified',
            'about_body' => 'Payroll Digital brings payroll, attendance, leave, recruitment, and employee self-service together so HR and finance teams can focus on people — not paperwork.',
            'features_title' => 'Everything you need to run HR',
            'features_subtitle' => 'Powerful modules that work together out of the box.',
            'stats_title' => 'Built for real workplaces',
            'logos_title' => 'Trusted by growing teams',
            'highlights_title' => 'Why teams choose Payroll Digital',
            'highlights_subtitle' => 'Purpose-built for HR, finance, and people managers who need clarity—not another spreadsheet.',
            'industries_title' => 'Built for every industry',
            'industries_subtitle' => 'From retail floors to software labs, Payroll Digital adapts to how your workforce actually operates.',
            'integrations_title' => 'Connects with your stack',
            'integrations_subtitle' => 'Export payroll, sync attendance, and plug into the tools your finance and IT teams already use.',
            'mobile_title' => 'HR in your pocket',
            'mobile_subtitle' => 'iOS & Android apps for employees and managers',
            'mobile_body' => "Check in, apply for leave, approve requests, and view payslips from anywhere. Push notifications keep everyone in sync—no extra logins, no clunky mobile sites.",
            'testimonials_title' => 'Loved by HR & finance teams',
            'pricing_title' => 'Simple, transparent pricing',
            'pricing_subtitle' => 'Pick the plan that matches your headcount today. Every plan scales with you as your team grows — upgrade or downgrade anytime.',
            'faq_title' => 'Frequently asked questions',
            'faq_subtitle' => 'Everything you need to know about payroll, security, and getting your team onboarded.',
            'how_it_works_title' => 'Live in days, not months',
            'how_it_works_subtitle' => 'A guided rollout gets your organization, employees, and first payroll run ready without disrupting the current cycle.',
            'security_title' => 'Enterprise-grade security, by default',
            'security_body' => "Payroll and people data deserve bank-grade protection. Payroll Digital encrypts data in transit and at rest, enforces role-based access control across every module, and keeps a full audit trail of approvals and changes.\n\nOur infrastructure runs on hardened cloud providers with routine backups, and every account is protected by granular permissions so employees, managers, and administrators only see what they are supposed to see.",
            'contact_title' => 'Talk to our team',
            'contact_body' => "Questions about rollout, pricing, custom modules, or migrating from spreadsheets? Our team typically responds within one business day.\n\nReach us at hello@payroll-digital.com and we will help you find the right plan for your organization.",
            'cta_title' => 'Ready to simplify your HR stack?',
            'cta_body' => 'Book a demo and see payroll, attendance, and workforce tools in one place.',
            'cta_button_text' => 'Book a Demo',
            'cta_button_link' => '/contact?intent=demo',
            'contact_email' => 'hello@payroll-digital.com',
            'contact_phone' => null,
            'social_linkedin' => 'https://www.linkedin.com/company/payroll-digital',
            'social_twitter' => 'https://twitter.com/payrolldigital',
            'social_facebook' => 'https://www.facebook.com/payrolldigital',
            'app_store_url' => 'https://apps.apple.com/app/payroll-digital',
            'play_store_url' => 'https://play.google.com/store/apps/details?id=com.payrolldigital.app',
            'footer_text' => '© ' . date('Y') . ' Payroll Digital. All rights reserved.',
            'is_published' => true,
        ]);

        $features = [
            ['icon' => 'payroll', 'title' => 'Payroll Processing', 'description' => 'Generate, review, and mark payroll paid with clear earnings and deductions.', 'sort_order' => 1],
            ['icon' => 'attendance', 'title' => 'Attendance Tracking', 'description' => 'Track check-ins, summaries, and presence with manager-friendly views.', 'sort_order' => 2],
            ['icon' => 'leaves', 'title' => 'Leave Management', 'description' => 'Apply, approve, and balance leave types with multi-level workflows.', 'sort_order' => 3],
            ['icon' => 'employees', 'title' => 'Employee Records', 'description' => 'Keep profiles, documents, salaries, and org structure in sync.', 'sort_order' => 4],
            ['icon' => 'shifts', 'title' => 'Shift Scheduling', 'description' => 'Plan rosters, assign shifts, and let teams see their schedules.', 'sort_order' => 5],
            ['icon' => 'recruitment', 'title' => 'Recruitment & CV Bank', 'description' => 'Manage openings, applications, and candidate CVs in one place.', 'sort_order' => 6],
            ['icon' => 'helpdesk', 'title' => 'Helpdesk & Support', 'description' => 'Let employees raise tickets and get IT, HR, or admin issues resolved quickly.', 'sort_order' => 7],
            ['icon' => 'travel', 'title' => 'Travel & Expenses', 'description' => 'Submit trip requests, track expenses, and approve reimbursements without paper trails.', 'sort_order' => 8],
        ];

        if (LandingFeature::count() < count($features)) {
            foreach ($features as $feature) {
                LandingFeature::updateOrCreate(
                    ['title' => $feature['title']],
                    $feature + ['is_active' => true]
                );
            }
        }

        $stats = [
            ['label' => 'Modules', 'value' => '25+', 'icon' => 'modules', 'sort_order' => 1],
            ['label' => 'HR Workflows', 'value' => 'End-to-end', 'icon' => 'workflows', 'sort_order' => 2],
            ['label' => 'Self-service', 'value' => 'Employees', 'icon' => 'users', 'sort_order' => 3],
            ['label' => 'Approvals', 'value' => 'Multi-level', 'icon' => 'approvals', 'sort_order' => 4],
            ['label' => 'Uptime', 'value' => '99.9%', 'icon' => 'uptime', 'sort_order' => 5],
            ['label' => 'Onboarding', 'value' => '< 1 week', 'icon' => 'onboarding', 'sort_order' => 6],
        ];

        if (LandingStat::count() < count($stats)) {
            foreach ($stats as $stat) {
                LandingStat::updateOrCreate(
                    ['label' => $stat['label']],
                    $stat + ['is_active' => true]
                );
            }
        }

        $testimonials = [
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
            [
                'name' => 'Bilal Raza',
                'role' => 'CEO',
                'company' => 'Vertex Manufacturing',
                'quote' => 'We moved 300 employees off spreadsheets in under two weeks. Support was hands-on through the entire rollout.',
                'sort_order' => 4,
            ],
        ];

        if (LandingTestimonial::count() < count($testimonials)) {
            foreach ($testimonials as $item) {
                LandingTestimonial::updateOrCreate(
                    ['name' => $item['name'], 'company' => $item['company']],
                    $item + ['is_active' => true]
                );
            }
        }

        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price' => 'PKR 10,000',
                'price_period' => '/month',
                'badge' => null,
                'description' => 'For small organizations moving from spreadsheets to a complete HR system.',
                'features' => [
                    'Up to 100 employees',
                    'Payroll processing',
                    'Attendance & leave management',
                    'Employee self-service portal',
                    'Email support',
                ],
                'cta_text' => 'Book a Demo',
                'cta_link' => '/contact?intent=demo',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price' => 'PKR 25,000',
                'price_period' => '/month',
                'badge' => null,
                'description' => 'For growing teams that need stronger workflows, scheduling, and support.',
                'features' => [
                    'Up to 125 employees',
                    'Everything in Starter',
                    'Shift scheduling & rosters',
                    'Multi-level approval workflows',
                    'Recruitment & CV bank',
                    'Priority email support',
                ],
                'cta_text' => 'Book a Demo',
                'cta_link' => '/contact?intent=demo',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'price' => 'PKR 40,000',
                'price_period' => '/month',
                'badge' => 'Most Popular',
                'description' => 'For established organizations that need complete people operations.',
                'features' => [
                    'Up to 150 employees',
                    'Everything in Basic',
                    'Shift scheduling & rosters',
                    'Multi-level approval workflows',
                    'Recruitment & CV bank',
                    'Helpdesk & travel expenses',
                    'Priority support',
                ],
                'cta_text' => 'Book a Demo',
                'cta_link' => '/contact?intent=demo',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 'Custom',
                'price_period' => 'pricing',
                'badge' => null,
                'description' => 'For large or multi-location organizations with custom workflows and compliance needs.',
                'features' => [
                    'Unlimited employees',
                    'Everything in Professional',
                    'Custom roles & permissions',
                    'Dedicated onboarding specialist',
                    'Custom integrations & API access',
                    'SLA-backed uptime & support',
                ],
                'cta_text' => 'Book a Demo',
                'cta_link' => '/contact?intent=demo',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            LandingPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        $faqs = [
            [
                'question' => 'How does payroll processing work in Payroll Digital?',
                'answer' => 'You configure salary components, allowances, and deductions once per employee, then run payroll each cycle with a review step before marking it paid. The system automatically calculates earnings, statutory deductions, loans, and advances, and keeps a full history of every payroll run.',
                'category' => 'Payroll',
                'sort_order' => 1,
            ],
            [
                'question' => 'Is our employee and payroll data secure?',
                'answer' => 'Yes. Data is encrypted in transit, access is controlled through granular role-based permissions, and every sensitive action is logged for audit purposes. Only authorized administrators and the employees themselves can view salary and personal information.',
                'category' => 'Security',
                'sort_order' => 2,
            ],
            [
                'question' => 'Can employees apply for leave and see their balance?',
                'answer' => 'Employees can apply for leave, attach documents where required, and track approval status from their self-service portal. Leave balances update automatically once a request is approved, and managers can configure multi-level approval chains per leave type.',
                'category' => 'Leave',
                'sort_order' => 3,
            ],
            [
                'question' => 'What is included in the pricing plans?',
                'answer' => 'Every plan includes payroll, attendance, and leave management. Higher tiers add shift scheduling, recruitment, helpdesk, travel & expense management, and dedicated support. See the pricing section above for a full feature breakdown, or contact sales for a custom quote.',
                'category' => 'Pricing',
                'sort_order' => 4,
            ],
            [
                'question' => 'How long does onboarding take?',
                'answer' => 'Most teams are fully onboarded within a week. You import your employee roster, configure departments and designations, set up salary structures, and invite your team — our onboarding guide and support team walk you through each step.',
                'category' => 'Onboarding',
                'sort_order' => 5,
            ],
            [
                'question' => 'What kind of support do you offer?',
                'answer' => 'All plans include email support, with priority and dedicated support available on higher tiers. Our team helps with setup, payroll questions, and troubleshooting so you are never stuck during a payroll run.',
                'category' => 'Support',
                'sort_order' => 6,
            ],
            [
                'question' => 'Who owns our company data?',
                'answer' => 'You do — always. Payroll Digital never sells or shares your employee or company data with third parties. You can export your data at any time, and it is permanently deleted from our systems if you choose to close your account.',
                'category' => 'Data',
                'sort_order' => 7,
            ],
            [
                'question' => 'Can employees access the system from their phones?',
                'answer' => 'Yes. The employee self-service portal is fully responsive and works on any mobile browser, so employees can check attendance, apply for leave, and view payslips from their phone without installing anything extra. We also offer native mobile apps for iOS and Android for an even faster experience.',
                'category' => 'Mobile',
                'sort_order' => 8,
            ],
            [
                'question' => 'Do you have mobile apps for iOS and Android?',
                'answer' => 'Yes. Payroll Digital has dedicated apps for both iPhone and Android, available on the Apple App Store and Google Play. The apps give employees on-the-go access to attendance check-in, leave requests, payslips, announcements, and approvals — with push notifications so nothing gets missed. Download links are available in the site header and footer.',
                'category' => 'Mobile',
                'sort_order' => 9,
            ],
        ];

        foreach ($faqs as $faq) {
            LandingFaq::updateOrCreate(['question' => $faq['question']], $faq + ['is_active' => true]);
        }

        $steps = [
            [
                'title' => 'Create your account',
                'description' => 'Sign up and set your organization name, industry, and primary admin in under two minutes.',
                'icon' => 'account',
                'sort_order' => 1,
            ],
            [
                'title' => 'Configure your organization',
                'description' => 'Add departments, designations, leave types, and salary components that match how your company works.',
                'icon' => 'organization',
                'sort_order' => 2,
            ],
            [
                'title' => 'Invite your team',
                'description' => 'Import employees in bulk or invite them one by one — everyone gets self-service access instantly.',
                'icon' => 'team',
                'sort_order' => 3,
            ],
            [
                'title' => 'Run your first payroll',
                'description' => 'Review calculated earnings and deductions, then process and mark payroll as paid with full confidence.',
                'icon' => 'payroll',
                'sort_order' => 4,
            ],
        ];

        foreach ($steps as $step) {
            LandingStep::updateOrCreate(['title' => $step['title']], $step + ['is_active' => true]);
        }

        $this->seedBlocks();

        $this->seedPages();
    }

    protected function seedBlocks(): void
    {
        $logos = [
            ['title' => 'Northwind Retail', 'sort_order' => 1],
            ['title' => 'Brightpath Labs', 'sort_order' => 2],
            ['title' => 'Cascade Soft', 'sort_order' => 3],
            ['title' => 'Vertex Mfg', 'sort_order' => 4],
            ['title' => 'Summit Logistics', 'sort_order' => 5],
            ['title' => 'Horizon Health', 'sort_order' => 6],
        ];

        foreach ($logos as $logo) {
            LandingBlock::updateOrCreate(
                ['type' => 'logo', 'title' => $logo['title']],
                $logo + ['type' => 'logo', 'is_active' => true]
            );
        }

        $highlights = [
            ['icon' => 'speed', 'title' => 'Faster payroll cycles', 'description' => 'Run payroll in hours, not days—with review steps and audit trails built in.', 'sort_order' => 1],
            ['icon' => 'shield', 'title' => 'Role-based security', 'description' => 'Granular permissions so employees, managers, and admins only see what they should.', 'sort_order' => 2],
            ['icon' => 'workflow', 'title' => 'Approval workflows', 'description' => 'Multi-level leave, advance, and expense approvals without email chains.', 'sort_order' => 3],
            ['icon' => 'mobile', 'title' => 'Mobile-first ESS', 'description' => 'Employees check attendance, apply leave, and view payslips from any phone.', 'sort_order' => 4],
            ['icon' => 'report', 'title' => 'Finance-ready reports', 'description' => 'Export payroll summaries and compliance-friendly reports for your auditors.', 'sort_order' => 5],
            ['icon' => 'support', 'title' => 'Hands-on onboarding', 'description' => 'Guided rollout gets your org live in days—with support through the first payroll.', 'sort_order' => 6],
        ];

        foreach ($highlights as $item) {
            LandingBlock::updateOrCreate(
                ['type' => 'highlight', 'title' => $item['title']],
                $item + ['type' => 'highlight', 'is_active' => true]
            );
        }

        $industries = [
            ['icon' => 'retail', 'title' => 'Retail & hospitality', 'description' => 'Shift rosters, attendance, and variable pay for frontline teams.', 'sort_order' => 1],
            ['icon' => 'manufacturing', 'title' => 'Manufacturing', 'description' => 'Track shifts, overtime, and shop-floor attendance at scale.', 'sort_order' => 2],
            ['icon' => 'tech', 'title' => 'Technology & services', 'description' => 'Flexible leave policies, remote attendance, and project-based teams.', 'sort_order' => 3],
            ['icon' => 'healthcare', 'title' => 'Healthcare', 'description' => 'Roster-heavy schedules with compliant leave and payroll records.', 'sort_order' => 4],
            ['icon' => 'logistics', 'title' => 'Logistics & field ops', 'description' => 'Mobile check-in, deployment tracking, and expense reimbursements.', 'sort_order' => 5],
            ['icon' => 'finance', 'title' => 'Finance & professional', 'description' => 'Structured approvals, loans, advances, and audit-friendly payroll.', 'sort_order' => 6],
        ];

        foreach ($industries as $item) {
            LandingBlock::updateOrCreate(
                ['type' => 'industry', 'title' => $item['title']],
                $item + ['type' => 'industry', 'is_active' => true]
            );
        }

        $integrations = [
            ['icon' => 'excel', 'title' => 'Excel & CSV export', 'description' => 'Export payroll, attendance, and employee data for finance workflows.', 'sort_order' => 1],
            ['icon' => 'email', 'title' => 'Email notifications', 'description' => 'Automated alerts for approvals, payroll runs, and leave decisions.', 'sort_order' => 2],
            ['icon' => 'calendar', 'title' => 'Calendar sync', 'description' => 'Leave and shift views that align with team calendars.', 'sort_order' => 3],
            ['icon' => 'api', 'title' => 'REST API', 'description' => 'Enterprise plans include API access for custom integrations.', 'sort_order' => 4],
            ['icon' => 'sso', 'title' => 'SSO ready', 'description' => 'Connect with your identity provider for centralized login.', 'sort_order' => 5],
            ['icon' => 'storage', 'title' => 'Document storage', 'description' => 'Employee files, contracts, and CVs stored securely in one place.', 'sort_order' => 6],
        ];

        foreach ($integrations as $item) {
            LandingBlock::updateOrCreate(
                ['type' => 'integration', 'title' => $item['title']],
                $item + ['type' => 'integration', 'is_active' => true]
            );
        }
    }

    protected function seedPages(): void
    {
        $pages = [
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'nav_label' => 'Privacy Policy',
                'excerpt' => 'How Payroll Digital collects, uses, and protects your data.',
                'sort_order' => 1,
                'content' => $this->privacyPolicyContent(),
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms of Service',
                'nav_label' => 'Terms of Service',
                'excerpt' => 'The terms that govern your use of Payroll Digital.',
                'sort_order' => 2,
                'content' => $this->termsOfServiceContent(),
            ],
            [
                'slug' => 'cookies',
                'title' => 'Cookie Policy',
                'nav_label' => 'Cookie Policy',
                'excerpt' => 'How we use cookies and similar technologies.',
                'sort_order' => 3,
                'content' => $this->cookiePolicyContent(),
            ],
            [
                'slug' => 'security',
                'title' => 'Security Overview',
                'nav_label' => 'Security',
                'excerpt' => 'How we protect your payroll and employee data.',
                'sort_order' => 4,
                'content' => $this->securityOverviewContent(),
            ],
            [
                'slug' => 'about',
                'title' => 'About Us',
                'nav_label' => 'About',
                'excerpt' => 'Our mission, our story, and the team behind Payroll Digital.',
                'sort_order' => 5,
                'content' => $this->aboutUsContent(),
            ],
            [
                'slug' => 'refund',
                'title' => 'Refund Policy',
                'nav_label' => 'Refund Policy',
                'excerpt' => 'Our policy on refunds, cancellations, and billing disputes.',
                'sort_order' => 6,
                'content' => $this->refundPolicyContent(),
            ],
        ];

        foreach ($pages as $page) {
            LandingPage::updateOrCreate(
                ['slug' => $page['slug']],
                $page + ['show_in_footer' => true, 'is_published' => true]
            );
        }
    }

    protected function privacyPolicyContent(): string
    {
        return <<<'TEXT'
Last updated: 2026

1. Introduction

Payroll Digital ("we", "us", "our") provides a human resources and payroll management platform ("Service") for organizations and their employees. This Privacy Policy explains what information we collect, how we use it, and the choices you have. By using Payroll Digital, you agree to the practices described in this policy.

2. Information We Collect

We collect information in the following categories:

Account and organization information: company name, industry, address, and administrator contact details provided when your organization signs up.

Employee information: names, contact details, job titles, department and designation, dates of employment, and other HR data entered by your organization's administrators for payroll, attendance, and leave management purposes.

Payroll and financial information: salary structures, earnings, deductions, bank details, and payment history required to process payroll.

Usage information: log data such as IP address, browser type, device information, pages visited, and timestamps, collected automatically when you use the Service.

Support communications: information you provide when you contact our support team, including emails and any attachments you send us.

3. How We Use Information

We use the information we collect to:

- Provide, operate, and maintain the Service, including processing payroll, attendance, and leave requests.
- Authenticate users and enforce role-based access controls.
- Communicate with you about your account, updates, and support requests.
- Improve the Service through aggregated, non-identifying analysis of usage patterns.
- Detect, investigate, and prevent fraudulent, unauthorized, or illegal activity.
- Comply with legal obligations, including tax and labor recordkeeping requirements applicable to your organization.

4. How We Share Information

We do not sell employee or company data. We may share information in these limited circumstances:

- With your organization's own authorized administrators and managers, according to the roles and permissions your organization configures.
- With service providers who help us operate the Service (such as cloud hosting providers), under contractual confidentiality obligations.
- When required by law, regulation, legal process, or governmental request.
- In connection with a merger, acquisition, or sale of assets, subject to standard confidentiality commitments.

5. Data Security

We apply technical and organizational measures designed to protect your data, including encryption in transit, role-based access control, and regular backups. No system can be guaranteed 100% secure, but we continuously review and improve our safeguards. See our Security Overview page for more detail.

6. Data Retention

We retain organization and employee data for as long as your account is active, or as needed to provide the Service, comply with legal obligations, resolve disputes, and enforce our agreements. Upon account closure, data is deleted or anonymized within a reasonable period, except where retention is required by law.

7. Your Rights

Depending on your jurisdiction, you may have rights to access, correct, export, or request deletion of your personal data. Employees should contact their organization's HR administrator to exercise these rights, as your employer controls the underlying HR data. Administrators may contact us directly at hello@payroll-digital.com.

8. Cookies

We use cookies and similar technologies to keep you signed in, remember preferences, and understand how the Service is used. See our Cookie Policy for details on the categories of cookies we use and how to manage them.

9. Children's Privacy

The Service is intended for use by businesses and their employees and is not directed at children. We do not knowingly collect personal information from individuals under the age of 16.

10. Changes to This Policy

We may update this Privacy Policy from time to time. We will post the updated version with a new "Last updated" date, and material changes will be communicated to organization administrators by email or in-app notice.

11. Contact Us

If you have questions about this Privacy Policy or how your data is handled, contact us at hello@payroll-digital.com.
TEXT;
    }

    protected function termsOfServiceContent(): string
    {
        return <<<'TEXT'
Last updated: 2026

1. Acceptance of Terms

By creating an account or using Payroll Digital ("Service"), you agree to be bound by these Terms of Service ("Terms"). If you are using the Service on behalf of an organization, you represent that you have the authority to bind that organization to these Terms.

2. Accounts and Registration

You must provide accurate and complete information when creating an account. Organization administrators are responsible for managing user access, roles, and permissions within their account, and for ensuring that employees who are granted access use the Service in accordance with these Terms.

You are responsible for maintaining the confidentiality of login credentials and for all activity that occurs under your account. Notify us immediately at hello@payroll-digital.com if you suspect unauthorized access.

3. Acceptable Use

You agree not to:

- Use the Service for any unlawful purpose or in violation of any applicable labor, tax, or data protection law.
- Attempt to gain unauthorized access to any part of the Service, other accounts, or connected systems.
- Upload malicious code, or interfere with or disrupt the integrity or performance of the Service.
- Reverse engineer, decompile, or attempt to extract the source code of the Service, except where permitted by law.
- Use the Service to store or process data you do not have the right to collect or process.

4. Subscriptions and Billing

Paid plans are billed in advance on a recurring basis (monthly or as otherwise agreed) based on the plan selected. Fees are non-refundable except as described in our Refund Policy. We may change pricing with reasonable prior notice; continued use of the Service after a price change takes effect constitutes acceptance of the new pricing.

You are responsible for keeping billing information current. Failure to pay may result in suspension or termination of access to the Service after reasonable notice.

5. Intellectual Property

Payroll Digital and its licensors retain all right, title, and interest in and to the Service, including all software, design, and trademarks. These Terms do not grant you any rights to our intellectual property except the limited right to use the Service as described herein.

You retain all rights to the data you input into the Service ("Customer Data"). You grant us a limited license to host, process, and display Customer Data solely to provide the Service to you.

6. Limitation of Liability

To the maximum extent permitted by law, Payroll Digital shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, arising from your use of the Service. Our total liability for any claim relating to the Service shall not exceed the amount you paid us in the twelve (12) months preceding the claim.

The Service is provided "as is" and "as available." We do not warrant that the Service will be uninterrupted, error-free, or that it will meet all of your specific requirements.

7. Termination

You may cancel your subscription at any time from your account settings or by contacting support. We may suspend or terminate your access to the Service if you breach these Terms, fail to pay applicable fees, or if required by law. Upon termination, your right to use the Service ends immediately, though certain provisions of these Terms will survive termination, including intellectual property, limitation of liability, and governing law provisions.

8. Governing Law

These Terms are governed by the laws of the jurisdiction in which Payroll Digital is registered to do business, without regard to conflict of law principles. Any disputes arising under these Terms shall be resolved in the courts of that jurisdiction, unless otherwise required by applicable law.

9. Changes to These Terms

We may modify these Terms from time to time. We will notify organization administrators of material changes. Continued use of the Service after changes take effect constitutes acceptance of the revised Terms.

10. Contact

Questions about these Terms can be sent to hello@payroll-digital.com.
TEXT;
    }

    protected function cookiePolicyContent(): string
    {
        return <<<'TEXT'
Last updated: 2026

1. What Are Cookies

Cookies are small text files placed on your device when you visit a website or use a web application. They help the site remember information about your visit, such as your preferred language and other settings, which can make your next visit easier and the site more useful to you.

2. How We Use Cookies

Payroll Digital uses cookies and similar technologies (such as local storage) for the following purposes:

Essential cookies: required for the Service to function, such as keeping you signed in and maintaining session security. These cannot be disabled without affecting core functionality.

Preference cookies: remember choices you make, such as display settings, so you do not have to reconfigure them on every visit.

Analytics cookies: help us understand how the Service is used in aggregate, so we can improve performance and usability. These do not identify you individually.

3. Third-Party Cookies

We may use trusted third-party services (such as analytics providers) that set their own cookies subject to their respective privacy policies. We do not permit third parties to use cookies for advertising purposes on the Service.

4. Managing Cookies

Most web browsers allow you to control cookies through their settings, including blocking or deleting cookies. Please note that disabling essential cookies may prevent you from signing in or using core features of the Service.

5. Changes to This Policy

We may update this Cookie Policy periodically to reflect changes in the technologies we use or for legal reasons. We encourage you to review this page occasionally to stay informed.

6. Contact

If you have questions about our use of cookies, contact us at hello@payroll-digital.com.
TEXT;
    }

    protected function securityOverviewContent(): string
    {
        return <<<'TEXT'
Last updated: 2026

1. Our Approach to Security

Payroll and employee data is some of the most sensitive information an organization holds. Payroll Digital is built with security as a foundational requirement, not an afterthought, across our infrastructure, application, and internal processes.

2. Data Encryption

All data transmitted between your browser and our servers is encrypted using industry-standard TLS. Sensitive fields, including bank and salary details, are protected with additional safeguards at rest.

3. Access Control

Payroll Digital uses granular, role-based access control. Administrators define exactly which roles can view, create, edit, or delete each type of data — from employee records to payroll runs. Employees can only access their own self-service information unless explicitly granted broader permissions.

4. Audit Trails

Every significant action — including payroll approvals, leave approvals, and permission changes — is logged with a timestamp and the responsible user, giving administrators a clear audit trail for compliance and internal review.

5. Infrastructure and Hosting

Our infrastructure runs on reputable cloud providers with strong physical and network security controls, redundant backups, and monitored uptime. We follow the principle of least privilege for internal system access, and production access is restricted to authorized personnel only.

6. Backups and Business Continuity

Data is backed up on a regular schedule to protect against data loss. We maintain procedures to restore service quickly in the event of an incident, minimizing disruption to your organization's payroll and HR operations.

7. Employee Awareness

Our team follows internal security best practices, including secure credential management and regular review of access permissions, to reduce the risk of accidental or unauthorized data exposure.

8. Responsible Disclosure

If you believe you have discovered a security vulnerability in Payroll Digital, please report it to hello@payroll-digital.com. We take all reports seriously and will work with you to understand and address the issue promptly.

9. Your Role in Security

Organizations play an important role in keeping their data secure — including choosing strong passwords, reviewing user access regularly, and promptly removing access for employees who leave the organization.

10. Contact

For security-related questions or to report a concern, contact hello@payroll-digital.com.
TEXT;
    }

    protected function aboutUsContent(): string
    {
        return <<<'TEXT'
Our Mission

Payroll Digital exists to take the friction out of running HR and payroll. We believe HR teams should spend their time on people, not paperwork, and that every organization — regardless of size — deserves payroll and workforce tools that are clear, reliable, and easy to trust.

Our Story

Payroll Digital was born out of a simple frustration: too many HR and finance teams were stuck juggling spreadsheets, disconnected tools, and manual approval chains just to run a single payroll cycle. We set out to build a single platform that brings payroll, attendance, leave, recruitment, and employee self-service together — without the complexity of legacy enterprise software.

What We Do

Our platform helps organizations manage the full employee lifecycle: from recruitment and onboarding, through attendance, leave, and shift scheduling, to payroll processing and offboarding. Employees get a self-service portal to manage their own information, apply for leave, and view payslips, while administrators get the controls and visibility they need to run HR with confidence.

Who We Serve

We work with growing businesses across retail, manufacturing, technology, and services who need dependable payroll and HR tooling without the overhead of legacy enterprise systems. Whether you have 25 employees or several hundred, our plans are designed to scale with your organization.

Our Values

Clarity — payroll and HR data should be easy to understand, not buried in confusing reports.

Reliability — payroll runs on a schedule, and our platform is built to be there when you need it.

Security — people data deserves careful, deliberate protection at every layer.

Partnership — we succeed when the organizations and people who use Payroll Digital succeed.

Get in Touch

We would love to hear from you. Reach out at hello@payroll-digital.com to learn more about Payroll Digital or to talk through your organization's HR and payroll needs.
TEXT;
    }

    protected function refundPolicyContent(): string
    {
        return <<<'TEXT'
Last updated: 2026

1. Overview

This Refund Policy explains how billing disputes, cancellations, and refund requests are handled for Payroll Digital subscriptions. It should be read together with our Terms of Service.

2. Subscription Cancellations

You may cancel your subscription at any time from your account settings or by contacting hello@payroll-digital.com. Cancellations take effect at the end of your current billing period, and you will retain access to the Service until that date. We do not provide prorated refunds for partial billing periods unless required by law.

3. Free Trials

If your plan includes a free trial, you will not be charged until the trial period ends, provided you cancel before the trial expires. If you do not cancel, your subscription will automatically convert to a paid plan and billing will begin.

4. Refund Eligibility

Refunds may be issued at our discretion in the following circumstances:

- A billing error resulted in an incorrect or duplicate charge.
- A service outage materially prevented you from using the Service for an extended period, as reasonably determined by our support team.
- You were charged after a cancellation request was submitted and confirmed prior to the renewal date.

Refunds are generally not provided for partial use of a billing period, dissatisfaction with features after extended use, or failure to cancel before a renewal date.

5. How to Request a Refund

To request a refund, contact hello@payroll-digital.com with your organization name, billing details, and the reason for your request. We aim to review and respond to refund requests within five (5) business days.

6. Enterprise and Custom Agreements

Organizations on custom Enterprise agreements are subject to the refund and cancellation terms specified in their signed agreement, which will take precedence over this policy in case of conflict.

7. Changes to This Policy

We may update this Refund Policy from time to time. Material changes will be communicated to organization administrators. Continued use of the Service after changes take effect constitutes acceptance of the revised policy.

8. Contact

For billing questions or refund requests, contact us at hello@payroll-digital.com.
TEXT;
    }
}
