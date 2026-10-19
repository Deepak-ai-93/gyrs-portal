<?php

namespace App\Services;

use App\Models\Job;
use App\Models\User;
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
}
