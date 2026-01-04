<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('auto_reply_enabled')->default(false);
            $table->enum('auto_reply_tone', ['friendly', 'professional', 'recovery'])->default('professional');
            $table->json('auto_reply_stars')->nullable(); // Which star ratings to auto-reply to [1,2,3,4,5]
            $table->unsignedInteger('auto_reply_delay_minutes')->default(5); // Delay before auto-replying
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'auto_reply_enabled',
                'auto_reply_tone',
                'auto_reply_stars',
                'auto_reply_delay_minutes',
            ]);
        });
    }
};
