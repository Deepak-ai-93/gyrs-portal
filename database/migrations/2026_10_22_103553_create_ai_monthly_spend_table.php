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
        Schema::create('ai_monthly_spend', function (Blueprint $table) {
            $table->char('month', 7)->primary();
            $table->decimal('spent_usd', 12, 6)->default(0);
            $table->decimal('reserved_usd', 12, 6)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_monthly_spend');
    }
};
