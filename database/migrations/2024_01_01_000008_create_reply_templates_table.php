<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reply_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('tone', ['friendly', 'professional', 'recovery']);
            $table->text('body');
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('tone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reply_templates');
    }
};
