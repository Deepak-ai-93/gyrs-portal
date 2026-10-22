<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('skill_id')->nullable()->constrained('skills')->nullOnDelete();
            $table->string('title');
            $table->string('provider');
            $table->string('url', 2048);
            $table->json('skill_tags')->nullable();
            $table->string('cost')->default('free');
            $table->string('language')->default('English');
            $table->string('format')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'provider']);
            $table->index('skill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_resources');
    }
};
