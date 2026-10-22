<?php

namespace App\Services\Ai;

use App\Models\Job;
use App\Models\LearningResource;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SkillPlanBuilder
{
    /**
     * @param  Collection<int, array{job: Job, score: int, matched: array<int, string>, missing: array<int, string>, breakdown: array<string, int>, reasons: array<int, string>}>  $matches
     * @param  Collection<int, LearningResource>  $resources
     * @param  array<int, array<string, mixed>>  $aiPlan
     * @return array<int, array<string, mixed>>
     */
    public function build(User $user, Collection $matches, Collection $resources, array $aiPlan = []): array
    {
        $local = $this->localPlan($matches, $resources);
        $merged = [];

        foreach ($aiPlan as $item) {
            $plan = $this->resolveAiItem($item, $resources);

            if ($plan !== null) {
                $merged[] = $plan;
            }
        }

        foreach ($local as $item) {
            if (count($merged) >= 5) {
                break;
            }

            if (! $this->containsSkill($merged, $item['skill_name'])) {
                $merged[] = $item;
            }
        }

        return collect($merged)
            ->sortBy(fn (array $plan) => match ($plan['priority'] ?? 'medium') {
                'high' => 0,
                'medium' => 1,
                default => 2,
            })
            ->take(5)
            ->values()
            ->map(function (array $plan, int $index): array {
                $plan['roadmap_days'] = $index < 2 ? 30 : ($index < 4 ? 60 : 90);

                return $plan;
            })
            ->all();
    }

    /**
     * @param  Collection<int, array{job: Job, score: int, matched: array<int, string>, missing: array<int, string>, breakdown: array<string, int>, reasons: array<int, string>}>  $matches
     * @param  Collection<int, LearningResource>  $resources
     * @return array<int, array<string, mixed>>
     */
    private function localPlan(Collection $matches, Collection $resources): array
    {
        $counts = $matches
            ->flatMap(fn (array $match) => $match['missing'])
            ->filter(fn ($skill) => is_string($skill) && trim($skill) !== '')
            ->countBy()
            ->sortDesc();

        $plans = [];
        $index = 0;

        foreach ($counts as $skill => $jobCount) {
            $priority = $index < 2 ? 'high' : ($index < 4 ? 'medium' : 'low');
            $weeks = $priority === 'high' ? 6 : ($priority === 'medium' ? 4 : 3);
            $resourceItems = $this->resourcesForSkill((string) $skill, $resources);

            $plans[] = [
                'skill_name' => (string) $skill,
                'skill_id' => Skill::query()->where('slug', Str::slug((string) $skill))->value('id'),
                'priority' => $priority,
                'why' => 'Requested by '.$jobCount.' of your matched '.Str::plural('vacancy', $jobCount).'. Building it should improve both your match score and interview confidence.',
                'estimated_weeks' => $weeks,
                'actions' => [
                    'Review the fundamentals with a structured course or official documentation.',
                    'Practise with two small exercises that use '.$skill.' in a real scenario.',
                    'Build a portfolio project and write a short note about what you learned.',
                    'Add '.$skill.' to your GYRS profile once you can explain your work confidently.',
                ],
                'resources' => $resourceItems,
                'project' => 'Create a small project that demonstrates '.$skill.' and document the problem, your approach and the result.',
                'proof' => 'Publish the project (for example on GitHub) and add a one-page summary that you can share in an interview.',
            ];

            $index++;

            if (count($plans) >= 5) {
                break;
            }
        }

        return $plans;
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  Collection<int, LearningResource>  $resources
     * @return array<string, mixed>|null
     */
    private function resolveAiItem(array $item, Collection $resources): ?array
    {
        $skill = is_string($item['skill'] ?? null) ? trim($item['skill']) : '';

        if ($skill === '') {
            return null;
        }

        $selected = collect($item['resource_ids'] ?? [])
            ->map(fn ($id) => $resources->firstWhere('id', $id))
            ->filter()
            ->map(fn (LearningResource $resource) => [
                'id' => $resource->getKey(),
                'title' => $resource->title,
                'provider' => $resource->provider,
                'url' => $resource->url,
            ])
            ->values()
            ->all();

        if ($selected === []) {
            $selected = $this->resourcesForSkill($skill, $resources);
        }

        $priority = mb_strtolower((string) ($item['priority'] ?? 'medium'));
        $priority = in_array($priority, ['high', 'medium', 'low'], true) ? $priority : 'medium';

        return [
            'skill_name' => $skill,
            'skill_id' => Skill::query()->where('slug', Str::slug($skill))->value('id'),
            'priority' => $priority,
            'why' => is_string($item['why'] ?? null) ? $item['why'] : 'A gap shared by your matched vacancies.',
            'estimated_weeks' => max(2, min(12, (int) ($item['estimated_weeks'] ?? 4))),
            'actions' => collect($item['actions'] ?? [])
                ->filter(fn ($action) => is_string($action) && trim($action) !== '')
                ->take(6)
                ->values()
                ->all(),
            'resources' => $selected,
            'project' => is_string($item['project'] ?? null) ? $item['project'] : null,
            'proof' => is_string($item['proof'] ?? null) ? $item['proof'] : null,
        ];
    }

    /**
     * @param  Collection<int, LearningResource>  $resources
     * @return array<int, array{id: string, title: string, provider: string, url: string}>
     */
    private function resourcesForSkill(string $skill, Collection $resources): array
    {
        $needle = mb_strtolower($skill);

        return $resources
            ->filter(function (LearningResource $resource) use ($needle): bool {
                $tags = collect($resource->skill_tags ?? [])
                    ->map(fn ($tag) => mb_strtolower((string) $tag));

                return $tags->contains($needle)
                    || $tags->contains(fn (string $tag) => str_contains($needle, $tag) || str_contains($tag, $needle));
            })
            ->take(3)
            ->map(fn (LearningResource $resource) => [
                'id' => $resource->getKey(),
                'title' => $resource->title,
                'provider' => $resource->provider,
                'url' => $resource->url,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $plans
     */
    private function containsSkill(array $plans, string $skill): bool
    {
        return collect($plans)->contains(
            fn (array $plan) => mb_strtolower((string) ($plan['skill_name'] ?? '')) === mb_strtolower($skill)
        );
    }
}
