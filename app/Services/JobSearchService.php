<?php

namespace App\Services;

use App\Models\District;
use App\Models\Job;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class JobSearchService
{
    public const PER_PAGE = 9;

    /**
     * Public-facing search limited to published, non-expired jobs.
     *
     * @param  array{keyword?: string|null, district?: string|null, type?: string|null}  $filters
     */
    public function search(array $filters = [], int $perPage = self::PER_PAGE): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->search($this->keyword($filters), $this->normalizedFilters($filters))
            ->orderByDesc('posted_at')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Admin listing includes every status and every date.
     *
     * @param  array{keyword?: string|null, district?: string|null, type?: string|null, status?: string|null}  $filters
     */
    public function adminSearch(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Job::query()
            ->with(['district'])
            ->when($this->keyword($filters), function (Builder $query, string $term) {
                $like = '%'.str_replace('%', '\%', $term).'%';
                $query->where(function (Builder $query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('department', 'like', $like)
                        ->orWhere('advertisement_number', 'like', $like);
                });
            })
            ->search(null, $this->normalizedFilters($filters))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function filtersFromRequest(array $filters): array
    {
        return [
            'keyword' => $this->keyword($filters),
            'district' => $filters['district'] ?? null,
            'type' => $filters['type'] ?? null,
        ];
    }

    /**
     * @return Collection<int, District>
     */
    public function districts(): Collection
    {
        return District::query()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function employmentTypes(): array
    {
        return Job::TYPES;
    }

    public function publishedJobCount(): int
    {
        return Job::query()->open()->count();
    }

    /**
     * Open job totals keyed by district id, for the homepage district ribbon.
     *
     * @return Collection<string, int>
     */
    public function openJobCountsByDistrict(): Collection
    {
        return Job::query()
            ->open()
            ->whereNotNull('district_id')
            ->selectRaw('district_id, COUNT(*) as jobs_count')
            ->groupBy('district_id')
            ->pluck('jobs_count', 'district_id');
    }

    protected function baseQuery(): Builder
    {
        return Job::query()->open()->with(['district', 'skills']);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function keyword(array $filters): ?string
    {
        $keyword = $filters['keyword'] ?? null;

        if (! is_string($keyword)) {
            return null;
        }

        $keyword = trim($keyword);

        return $keyword === '' ? null : $keyword;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function normalizedFilters(array $filters): array
    {
        $normalized = [];

        foreach (['district', 'type', 'status'] as $key) {
            $value = $filters[$key] ?? null;

            if (is_string($value) && trim($value) !== '' && strtoupper($value) !== 'ALL') {
                $normalized[$key] = trim($value);
            }
        }

        return $normalized;
    }
}
