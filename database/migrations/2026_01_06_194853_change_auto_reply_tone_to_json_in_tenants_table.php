<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Store existing data
        $existingData = DB::table('tenants')->select('id', 'auto_reply_tone')->get();

        // Drop the enum column
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('auto_reply_tone');
        });

        // Add new JSON column
        Schema::table('tenants', function (Blueprint $table) {
            $table->json('auto_reply_tone')->nullable()->after('auto_reply_enabled');
        });

        // Migrate existing data
        foreach ($existingData as $tenant) {
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['auto_reply_tone' => json_encode([$tenant->auto_reply_tone])]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to enum with first value
        DB::table('tenants')->get()->each(function ($tenant) {
            $tones = json_decode($tenant->auto_reply_tone, true);
            $firstTone = is_array($tones) && count($tones) > 0 ? $tones[0] : 'professional';
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['auto_reply_tone' => $firstTone]);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('auto_reply_tone', ['friendly', 'professional', 'recovery'])->default('professional')->change();
        });
    }
};
