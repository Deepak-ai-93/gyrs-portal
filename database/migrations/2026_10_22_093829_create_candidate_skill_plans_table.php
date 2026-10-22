<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_skill_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('resume_analysis_id')->nullable()->constrained('resume_analyses')->nullOnDelete();
            $table->foreignUuid('skill_id')->nullable()->constrained('skills')->nullOnDelete();
            $table->string('skill_name');
            $table->string('priority')->default('medium');
            $table->text('why')->nullable();
            $table->unsignedTinyInteger('estimated_weeks')->default(4);
            $table->json('actions')->nullable();
            $table->json('resources')->nullable();
            $table->text('project')->nullable();
            $table->text('proof')->nullable();
            $table->string('status')->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('resume_analysis_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_skill_plans');
    }
};
