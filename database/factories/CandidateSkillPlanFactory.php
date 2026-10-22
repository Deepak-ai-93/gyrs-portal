<?php

namespace Database\Factories;

use App\Models\CandidateSkillPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CandidateSkillPlan>
 */
class CandidateSkillPlanFactory extends Factory
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
            'resume_analysis_id' => null,
            'skill_id' => null,
            'skill_name' => fake()->word(),
            'priority' => 'medium',
            'why' => 'This skill appears in matched vacancies.',
            'estimated_weeks' => 4,
            'roadmap_days' => 30,
            'actions' => [],
            'resources' => [],
            'project' => 'Build a small project that demonstrates the skill.',
            'proof' => 'Share the project and explain your approach.',
            'status' => CandidateSkillPlan::STATUS_NOT_STARTED,
        ];
    }
}
