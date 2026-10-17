<?php

namespace App\Services;

use App\Models\User;

class ProfileCompletenessService
{
    /**
     * Calculate a 0-100 completeness score for a candidate profile.
     */
    public function calculate(User $user): int
    {
        $score = 0;

        $score += $this->filled($user->name) ? 15 : 0;
        $score += $this->filled($user->phone) ? 15 : 0;
        $score += $this->filled($user->email) ? 10 : 0;
        $score += $user->district_id ? 10 : 0;
        $score += $this->filled($user->qualification) ? 15 : 0;
        $score += $this->filled($user->cgpa) ? 10 : 0;
        $score += $this->filled($user->enrollment_id) ? 10 : 0;

        $skillCount = $user->relationLoaded('skills')
            ? $user->skills->count()
            : $user->skills()->count();

        $score += match (true) {
            $skillCount >= 3 => 15,
            $skillCount === 2 => 10,
            $skillCount === 1 => 5,
            default => 0,
        };

        return max(0, min(100, $score));
    }

    private function filled(mixed $value): bool
    {
        return is_string($value) ? trim($value) !== '' : ! empty($value);
    }
}
