<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('department');
            $table->foreignUuid('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('salary_text');
            $table->string('qualification');
            $table->string('employment_type');
            $table->text('description');
            $table->unsignedTinyInteger('match_score')->default(0);
            $table->string('advertisement_number')->nullable();
            $table->unsignedInteger('total_posts')->default(0);
            $table->date('posted_at')->nullable();
            $table->date('last_date')->nullable();
            $table->string('age_limit')->nullable();
            $table->string('fees')->nullable();
            $table->text('vacancy_breakup')->nullable();
            $table->string('official_website_url')->nullable();
            $table->string('apply_url')->nullable();
            $table->string('notification_pdf_path')->nullable();
            $table->string('status')->default('draft');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('district_id');
            $table->index('employment_type');
            $table->index('last_date');
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
