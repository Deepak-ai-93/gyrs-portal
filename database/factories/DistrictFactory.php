<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    protected $model = District::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'region' => fake()->randomElement(['North Gujarat', 'Central Gujarat', 'South Gujarat', 'Saurashtra', 'Kutch']),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
