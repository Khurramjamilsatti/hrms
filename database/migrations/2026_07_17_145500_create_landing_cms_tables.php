<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name')->default('Payroll Digital');
            $table->string('brand_tagline')->nullable();
            $table->string('hero_title');
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_cta_text')->default('Get Started');
            $table->string('hero_cta_link')->default('/login');
            $table->string('hero_secondary_cta_text')->nullable();
            $table->string('hero_secondary_cta_link')->nullable();
            $table->string('about_title')->nullable();
            $table->text('about_body')->nullable();
            $table->string('features_title')->nullable();
            $table->string('features_subtitle')->nullable();
            $table->string('stats_title')->nullable();
            $table->string('testimonials_title')->nullable();
            $table->string('cta_title')->nullable();
            $table->text('cta_body')->nullable();
            $table->string('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_address')->nullable();
            $table->text('footer_text')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_features', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_stats', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('value');
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('company')->nullable();
            $table->text('quote');
            $table->string('avatar_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_testimonials');
        Schema::dropIfExists('landing_stats');
        Schema::dropIfExists('landing_features');
        Schema::dropIfExists('landing_settings');
    }
};
