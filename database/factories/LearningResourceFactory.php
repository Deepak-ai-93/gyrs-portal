<?php

namespace Database\Factories;

use App\Models\LearningResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningResource>
 */
class LearningResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skill_id' => null,
            'title' => fake()->sentence(4),
            'provider' => 'SWAYAM',
            'url' => fake()->unique()->url(),
            'skill_tags' => [],
            'cost' => 'free',
            'language' => 'English',
            'format' => 'Online course',
            'is_active' => true,
            'verified_at' => now(),
        ];
    }
}
