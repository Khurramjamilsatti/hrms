<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('logos_title')->nullable()->after('stats_title');
            $table->string('highlights_title')->nullable()->after('logos_title');
            $table->text('highlights_subtitle')->nullable()->after('highlights_title');
            $table->string('industries_title')->nullable()->after('highlights_subtitle');
            $table->text('industries_subtitle')->nullable()->after('industries_title');
            $table->string('integrations_title')->nullable()->after('industries_subtitle');
            $table->text('integrations_subtitle')->nullable()->after('integrations_title');
            $table->string('mobile_title')->nullable()->after('integrations_subtitle');
            $table->text('mobile_subtitle')->nullable()->after('mobile_title');
            $table->text('mobile_body')->nullable()->after('mobile_subtitle');
        });

        Schema::create('landing_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // logo | highlight | industry | integration
            $table->string('icon')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_blocks');

        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn([
                'logos_title',
                'highlights_title',
                'highlights_subtitle',
                'industries_title',
                'industries_subtitle',
                'integrations_title',
                'integrations_subtitle',
                'mobile_title',
                'mobile_subtitle',
                'mobile_body',
            ]);
        });
    }
};
