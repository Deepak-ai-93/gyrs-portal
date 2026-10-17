<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_skills', function (Blueprint $table) {
            $table->foreignUuid('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignUuid('skill_id')->constrained('skills')->cascadeOnDelete();

            $table->unique(['job_id', 'skill_id']);
        });

        foreach (['job_eligibility', 'job_responsibilities', 'job_selection_steps', 'job_application_steps'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('job_id')->constrained('jobs')->cascadeOnDelete();
                $table->text('body');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['job_id', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_application_steps');
        Schema::dropIfExists('job_selection_steps');
        Schema::dropIfExists('job_responsibilities');
        Schema::dropIfExists('job_eligibility');
        Schema::dropIfExists('job_skills');
    }
};
