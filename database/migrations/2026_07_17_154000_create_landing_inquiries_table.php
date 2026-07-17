<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('contact'); // contact | demo
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status')->default('new'); // new | read | archived
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_inquiries');
    }
};
