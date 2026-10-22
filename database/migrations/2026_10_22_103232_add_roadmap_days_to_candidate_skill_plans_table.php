<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidate_skill_plans', function (Blueprint $table) {
            $table->unsignedTinyInteger('roadmap_days')->default(30)->after('estimated_weeks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_skill_plans', function (Blueprint $table) {
            $table->dropColumn('roadmap_days');
        });
    }
};
