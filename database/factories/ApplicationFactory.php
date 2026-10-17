<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'candidate_id' => User::factory()->candidate(),
            'job_id' => Job::factory()->published(),
            'status' => Application::STATUS_APPLIED,
            'applied_at' => now(),
            'status_changed_at' => now(),
            'notes' => null,
            'external_url' => 'https://ojas.gujarat.gov.in/',
        ];
    }
}
