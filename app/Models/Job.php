<?php

namespace App\Models;

use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Job extends Model
{
    /** @use HasFactory<JobFactory> */
    use HasFactory, HasUuids;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_ARCHIVED = 'archived';

    public const TYPES = [
        'Full-Time',
        'Govt Direct',
        'Govt Contract',
        'Apprenticeship',
    ];

    protected $fillable = [
        'slug',
        'title',
        'department',
        'district_id',
        'location',
        'salary_text',
        'qualification',
        'employment_type',
        'description',
        'match_score',
        'advertisement_number',
        'total_posts',
        'posted_at',
        'last_date',
        'age_limit',
        'fees',
        'vacancy_breakup',
        'official_website_url',
        'apply_url',
        'notification_pdf_path',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'match_score' => 'integer',
            'total_posts' => 'integer',
            'posted_at' => 'date',
            'last_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Job $job) {
            if (empty($job->slug)) {
                $job->slug = static::uniqueSlug($job->title, $job->id);
            }
        });
    }

    public static function uniqueSlug(string $title, ?string $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'job';
        $slug = $base;
        $suffix = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedBy(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function eligibility(): HasMany
    {
        return $this->hasMany(JobEligibility::class)->orderBy('sort_order');
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(JobResponsibility::class)->orderBy('sort_order');
    }

    public function selectionSteps(): HasMany
    {
        return $this->hasMany(JobSelectionStep::class)->orderBy('sort_order');
    }

    public function applicationSteps(): HasMany
    {
        return $this->hasMany(JobApplicationStep::class)->orderBy('sort_order');
    }

    public function isExpired(): bool
    {
        return $this->last_date !== null && $this->last_date->isPast();
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && ! $this->isExpired();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $query) {
                $query->whereNull('last_date')
                    ->orWhereDate('last_date', '>=', now()->toDateString());
            });
    }

    /**
     * Apply keyword + district + employment type filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeSearch(Builder $query, ?string $term = null, array $filters = []): Builder
    {
        return $query
            ->when($term, function (Builder $query, string $term) {
                $like = '%'.str_replace('%', '\%', $term).'%';
                $query->where(function (Builder $query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('department', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('qualification', 'like', $like)
                        ->orWhereHas('skills', fn (Builder $query) => $query->where('name', 'like', $like))
                        ->orWhereHas('district', fn (Builder $query) => $query->where('name', 'like', $like));
                });
            })
            ->when($filters['district'] ?? null, function (Builder $query, $district) {
                if (is_numeric($district)) {
                    $query->where('district_id', $district);
                } else {
                    $query->whereHas('district', fn (Builder $query) => $query->where('slug', $district)->orWhere('name', $district));
                }
            })
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('employment_type', $type))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));
    }
}
