<?php

namespace Database\Factories;

use App\Models\ResumeAnalysis;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeAnalysis>
 */
class ResumeAnalysisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->candidate(),
            'status' => ResumeAnalysis::STATUS_COMPLETED,
            'source_type' => 'text',
            'original_filename' => 'pasted-resume.txt',
            'file_size' => 120,
            'content_hash' => hash('sha256', fake()->sentence()),
            'target_role' => null,
            'extracted_profile' => [],
            'ai_analysis' => [],
            'ai_plan' => [],
            'is_ai_generated' => false,
            'prompt_version' => 'resume-v1',
            'consent_version' => '2026-10',
            'consent_at' => now(),
            'input_tokens' => 0,
            'output_tokens' => 0,
            'cost_usd' => 0,
            'completed_at' => now(),
        ];
    }
}
