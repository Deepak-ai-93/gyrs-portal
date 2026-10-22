<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\ResumeAnalysis;
use App\Models\ResumeJobMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeJobMatch>
 */
class ResumeJobMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resume_analysis_id' => ResumeAnalysis::factory(),
            'job_id' => Job::factory(),
            'score' => fake()->numberBetween(0, 100),
            'rank' => 0,
            'matched_skills' => [],
            'missing_skills' => [],
            'breakdown' => [],
            'reason' => 'An open vacancy worth exploring.',
        ];
    }
}
