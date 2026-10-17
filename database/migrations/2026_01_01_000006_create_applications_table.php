<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->string('status')->default('applied');
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('external_reference')->nullable();
            $table->string('external_url')->nullable();
            $table->timestamps();

            $table->unique(['candidate_id', 'job_id']);
            $table->index('candidate_id');
            $table->index('job_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
