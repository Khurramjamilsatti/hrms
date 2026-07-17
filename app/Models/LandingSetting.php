<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $fillable = [
        'brand_name',
        'brand_tagline',
        'hero_title',
        'hero_subtitle',
        'hero_cta_text',
        'hero_cta_link',
        'hero_secondary_cta_text',
        'hero_secondary_cta_link',
        'about_title',
        'about_body',
        'features_title',
        'features_subtitle',
        'stats_title',
        'testimonials_title',
        'cta_title',
        'cta_body',
        'cta_button_text',
        'cta_button_link',
        'contact_email',
        'contact_phone',
        'contact_address',
        'footer_text',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? static::create([
            'brand_name' => 'Payroll Digital',
            'brand_tagline' => 'HR Management System',
            'hero_title' => 'Modern HR & Payroll for Growing Teams',
            'hero_subtitle' => 'Run payroll, attendance, leaves, and employee records from one beautiful platform.',
            'hero_cta_text' => 'Sign In',
            'hero_cta_link' => '/login',
            'hero_secondary_cta_text' => 'Explore Features',
            'hero_secondary_cta_link' => '#features',
            'about_title' => 'Built for people teams',
            'about_body' => 'Payroll Digital helps HR and finance teams automate payroll, track attendance, manage leave, and keep every employee record in sync.',
            'features_title' => 'Everything you need',
            'features_subtitle' => 'One platform for workforce, compensation, and compliance.',
            'stats_title' => 'Trusted by modern workplaces',
            'testimonials_title' => 'What teams say',
            'cta_title' => 'Ready to simplify HR?',
            'cta_body' => 'Sign in to your workspace and manage your workforce with confidence.',
            'cta_button_text' => 'Go to Login',
            'cta_button_link' => '/login',
            'contact_email' => 'hello@payroll-digital.com',
            'contact_phone' => null,
            'contact_address' => null,
            'footer_text' => '© Payroll Digital. All rights reserved.',
            'is_published' => true,
        ]);
    }
}
