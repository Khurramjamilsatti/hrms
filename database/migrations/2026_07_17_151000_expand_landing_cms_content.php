<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('pricing_title')->nullable()->after('testimonials_title');
            $table->text('pricing_subtitle')->nullable()->after('pricing_title');
            $table->string('faq_title')->nullable()->after('pricing_subtitle');
            $table->text('faq_subtitle')->nullable()->after('faq_title');
            $table->string('how_it_works_title')->nullable()->after('faq_subtitle');
            $table->text('how_it_works_subtitle')->nullable()->after('how_it_works_title');
            $table->string('security_title')->nullable()->after('how_it_works_subtitle');
            $table->text('security_body')->nullable()->after('security_title');
            $table->string('contact_title')->nullable()->after('security_body');
            $table->text('contact_body')->nullable()->after('contact_title');
            $table->string('social_linkedin')->nullable()->after('contact_address');
            $table->string('social_twitter')->nullable()->after('social_linkedin');
            $table->string('social_facebook')->nullable()->after('social_twitter');
        });

        Schema::create('landing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('price')->nullable();
            $table->string('price_period')->nullable();
            $table->string('badge')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_link')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('nav_label')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->boolean('show_in_footer')->default(true);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
        Schema::dropIfExists('landing_steps');
        Schema::dropIfExists('landing_faqs');
        Schema::dropIfExists('landing_plans');

        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn([
                'pricing_title',
                'pricing_subtitle',
                'faq_title',
                'faq_subtitle',
                'how_it_works_title',
                'how_it_works_subtitle',
                'security_title',
                'security_body',
                'contact_title',
                'contact_body',
                'social_linkedin',
                'social_twitter',
                'social_facebook',
            ]);
        });
    }
};
