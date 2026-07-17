<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('app_store_url')->nullable()->after('social_facebook');
            $table->string('play_store_url')->nullable()->after('app_store_url');
        });
    }

    public function down(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn(['app_store_url', 'play_store_url']);
        });
    }
};
