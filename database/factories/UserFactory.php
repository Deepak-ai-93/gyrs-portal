<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('9#########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => User::ROLE_CANDIDATE,
            'district_id' => District::factory(),
            'enrollment_id' => 'GYRS-TEST-'.Str::upper(Str::random(8)),
            'qualification' => 'B.Tech / B.E. (Computer / IT)',
            'cgpa' => '8.0',
            'status' => User::STATUS_VERIFIED,
            'approved_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
            'district_id' => null,
            'enrollment_id' => null,
            'status' => User::STATUS_VERIFIED,
            'approved_at' => now(),
        ]);
    }

    public function candidate(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_CANDIDATE,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => User::STATUS_PENDING,
            'approved_at' => null,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
