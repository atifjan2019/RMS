<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('location_name'); // Reference to gbp_locations
            $table->string('review_name')->unique(); // accounts/{id}/locations/{id}/reviews/{id}
            $table->string('reviewer_name');
            $table->string('reviewer_photo_url')->nullable();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamp('created_at_google')->nullable();
            $table->text('reply_text')->nullable();
            $table->timestamp('replied_at_google')->nullable();
            $table->enum('status', ['replied', 'unreplied'])->default('unreplied');
            $table->json('raw')->nullable(); // Full API response
            $table->json('ai_drafts')->nullable(); // Store generated drafts
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('location_name');
            $table->index('status');
            $table->index('rating');
            $table->index('created_at_google');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
