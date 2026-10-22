<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_job_matches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('resume_analysis_id')->constrained('resume_analyses')->cascadeOnDelete();
            $table->foreignUuid('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->default(0);
            $table->unsignedTinyInteger('rank')->default(0);
            $table->json('matched_skills')->nullable();
            $table->json('missing_skills')->nullable();
            $table->json('breakdown')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['resume_analysis_id', 'job_id']);
            $table->index(['resume_analysis_id', 'rank']);
            $table->index('job_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_job_matches');
    }
};
