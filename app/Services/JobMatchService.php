<?php

namespace App\Services;

use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class JobMatchService
{
    /**
     * Percentage of a job's required skills the candidate already has.
     */
    public function score(User $user, Job $job): int
    {
        $required = $job->relationLoaded('skills')
            ? $job->skills->pluck('name')
            : $job->skills()->pluck('name');

        if ($required->isEmpty()) {
            return $job->match_score ?: 100;
        }

        $owned = ($user->relationLoaded('skills')
            ? $user->skills->pluck('name')
            : $user->skills()->pluck('name'))
            ->map(fn (string $name) => mb_strtolower($name));

        $have = $required->filter(fn (string $name) => $owned->contains(mb_strtolower($name)));

        return (int) round(($have->count() / $required->count()) * 100);
    }

    /**
     * Skills shared between candidate and job.
     */
    public function matchedSkills(User $user, Job $job): Collection
    {
        $owned = ($user->relationLoaded('skills')
            ? $user->skills->pluck('name')
            : $user->skills()->pluck('name'))
            ->map(fn (string $name) => mb_strtolower($name));

        return ($job->relationLoaded('skills') ? $job->skills : $job->skills()->get())
            ->filter(fn ($skill) => $owned->contains(mb_strtolower($skill->name)))
            ->values();
    }

    /**
     * Open jobs ordered by best skill match for the candidate.
     *
     * @return Collection<int, Job>
     */
    public function recommendedJobs(User $user, int $limit = 6): Collection
    {
        $appliedJobIds = $user->applications()->pluck('job_id');

        return Job::query()
            ->open()
            ->with(['district', 'skills'])
            ->whereNotIn('id', $appliedJobIds)
            ->get()
            ->sortByDesc(fn (Job $job) => $this->score($user, $job))
            ->take($limit)
            ->values();
    }

    /**
     * Rank open vacancies for a résumé analysis.
     *
     * The score is deliberately deterministic: the LLM may explain a match,
     * but it never decides whether a vacancy exists or whether a candidate is
     * eligible.
     *
     * @param  array<int, string>  $skillNames
     * @return Collection<int, array{
     *     job: Job,
     *     score: int,
     *     matched: array<int, string>,
     *     missing: array<int, string>,
     *     breakdown: array<string, int>,
     *     reasons: array<int, string>
     * }>
     */
    public function rankedMatches(
        User $user,
        array $skillNames,
        ?string $qualification = null,
        int $limit = 8,
    ): Collection {
        $owned = collect($skillNames)
            ->merge($user->relationLoaded('skills') ? $user->skills->pluck('name') : $user->skills()->pluck('name'))
            ->filter(fn ($name) => is_string($name) && trim($name) !== '')
            ->map(fn (string $name) => mb_strtolower(trim($name)))
            ->unique()
            ->values();

        $qualification = $qualification ?: $user->qualification;

        return Job::query()
            ->open()
            ->with(['district', 'skills'])
            ->get()
            ->map(function (Job $job) use ($user, $owned, $qualification): array {
                $required = $job->skills
                    ->pluck('name')
                    ->filter(fn ($name) => is_string($name) && trim($name) !== '')
                    ->values();

                $matched = $required
                    ->filter(fn (string $name) => $owned->contains(mb_strtolower($name)))
                    ->values();

                $missing = $required
                    ->reject(fn (string $name) => $owned->contains(mb_strtolower($name)))
                    ->values();

                $skillScore = $required->isEmpty()
                    ? 55
                    : (int) round(($matched->count() / $required->count()) * 100);

                $qualificationScore = $this->qualificationScore($qualification, $job->qualification);
                $districtScore = $this->districtScore($user, $job);
                $recencyScore = $this->recencyScore($job);

                $score = (int) round(
                    ($skillScore * 0.60)
                    + ($qualificationScore * 0.15)
                    + ($districtScore * 0.15)
                    + ($recencyScore * 0.10)
                );

                $reasons = [];

                if ($matched->isNotEmpty()) {
                    $reasons[] = 'Matches your skills in '.$matched->take(3)->join(', ', ' and ').'.';
                }

                if ($qualificationScore >= 100) {
                    $reasons[] = 'Your qualification aligns with the advertised requirement.';
                } elseif ($qualificationScore >= 70) {
                    $reasons[] = 'Your qualification is close to the advertised requirement — check the official notification.';
                }

                if ($districtScore >= 100) {
                    $reasons[] = 'This vacancy is in your home district.';
                }

                if ($recencyScore >= 80) {
                    $reasons[] = 'Recently posted and currently open.';
                }

                return [
                    'job' => $job,
                    'score' => max(0, min(100, $score)),
                    'matched' => $matched->all(),
                    'missing' => $missing->all(),
                    'breakdown' => [
                        'skills' => $skillScore,
                        'qualification' => $qualificationScore,
                        'district' => $districtScore,
                        'recency' => $recencyScore,
                    ],
                    'reasons' => $reasons ?: ['An open vacancy worth exploring while you build the missing skills.'],
                ];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    /**
     * In-demand skills drawn from open vacancies that the candidate does not
     * yet list, ordered by how many roles request them.
     *
     * @return Collection<int, array{name: string, jobs: int}>
     */
    public function suggestedSkills(User $user, int $limit = 6): Collection
    {
        $owned = ($user->relationLoaded('skills')
            ? $user->skills->pluck('name')
            : $user->skills()->pluck('name'))
            ->map(fn (string $name) => mb_strtolower($name))
            ->all();

        return Job::query()
            ->open()
            ->with('skills')
            ->get()
            ->flatMap(fn (Job $job) => $job->skills)
            ->filter(fn ($skill) => ! in_array(mb_strtolower($skill->name), $owned, true))
            ->groupBy(fn ($skill) => $skill->name)
            ->map(fn (Collection $group, string $name) => ['name' => $name, 'jobs' => $group->count()])
            ->sortByDesc('jobs')
            ->take($limit)
            ->values();
    }

    private function qualificationScore(?string $candidate, ?string $job): int
    {
        $candidateLevel = $this->qualificationLevel($candidate);
        $jobLevel = $this->qualificationLevel($job);

        if ($jobLevel === null) {
            return 60;
        }

        if ($candidateLevel === null) {
            return 50;
        }

        if ($candidateLevel >= $jobLevel) {
            return 100;
        }

        return $candidateLevel === $jobLevel - 1 ? 70 : 40;
    }

    private function qualificationLevel(?string $value): ?int
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = mb_strtolower($value);

        if (preg_match('/\b(ph\.?d|doctorate|doctoral)\b/u', $value)) {
            return 7;
        }

        if (preg_match('/\b(m\.?tech|m\.?e\.?|m\.?sc|mca|mba|master|post[ -]?graduate)\b/u', $value)) {
            return 6;
        }

        if (preg_match('/\b(b\.?tech|b\.?e\.?|b\.?sc|bca|bachelor|degree|graduate)\b/u', $value)) {
            return 5;
        }

        if (preg_match('/\b(diploma|polytechnic)\b/u', $value)) {
            return 3;
        }

        if (preg_match('/\biti\b/u', $value)) {
            return 2;
        }

        if (preg_match('/\b(12th|hsc|higher secondary|senior secondary)\b/u', $value)) {
            return 1;
        }

        if (preg_match('/\b(10th|ssc|matriculation)\b/u', $value)) {
            return 0;
        }

        return null;
    }

    private function districtScore(User $user, Job $job): int
    {
        if ($job->district_id && $job->district_id === $user->district_id) {
            return 100;
        }

        if ($job->district_id === null && $user->district_id === null) {
            return 60;
        }

        return $job->district_id === null ? 55 : 45;
    }

    private function recencyScore(Job $job): int
    {
        $date = $job->posted_at ?? $job->created_at;

        if (! $date instanceof Carbon) {
            return 55;
        }

        $days = (int) $date->diffInDays(now(), false);

        return match (true) {
            $days <= 7 => 100,
            $days <= 30 => 80,
            $days <= 60 => 65,
            $days <= 90 => 50,
            default => 35,
        };
    }
}
