<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->jobTitle();

        return [
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'title' => $title,
            'department' => 'Gujarat Informatics Ltd (GIL)',
            'district_id' => District::factory(),
            'location' => 'Gandhinagar',
            'salary_text' => '₹35,000 - ₹45,000 / month',
            'qualification' => 'B.Tech / B.E. (Computer / IT)',
            'employment_type' => fake()->randomElement(Job::TYPES),
            'description' => fake()->sentence(12),
            'match_score' => fake()->numberBetween(60, 100),
            'advertisement_number' => 'GYRS/2026/'.fake()->numberBetween(1, 999),
            'total_posts' => fake()->numberBetween(1, 150),
            'posted_at' => now()->subDays(5)->toDateString(),
            'last_date' => now()->addDays(20)->toDateString(),
            'age_limit' => '18 - 35 Years',
            'fees' => 'Refer notification',
            'vacancy_breakup' => 'As per roster',
            'official_website_url' => 'https://ojas.gujarat.gov.in/',
            'apply_url' => 'https://ojas.gujarat.gov.in/',
            'notification_pdf_path' => null,
            'status' => Job::STATUS_PUBLISHED,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_DRAFT,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_PUBLISHED,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_PUBLISHED,
            'last_date' => now()->subDay()->toDateString(),
        ]);
    }
}
