<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_analyses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('processing');
            $table->string('source_type');
            $table->string('original_filename')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->string('content_hash', 64)->nullable();
            $table->string('target_role')->nullable();
            $table->longText('extracted_profile')->nullable();
            $table->longText('ai_analysis')->nullable();
            $table->longText('ai_plan')->nullable();
            $table->boolean('is_ai_generated')->default(false);
            $table->string('model_used')->nullable();
            $table->string('prompt_version')->nullable();
            $table->string('consent_version')->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->decimal('cost_usd', 12, 6)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_analyses');
    }
};
