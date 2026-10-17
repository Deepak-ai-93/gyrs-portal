<?php

namespace App\Models;

use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory, HasUuids;

    public const STATUS_APPLIED = 'applied';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_SHORTLISTED = 'shortlisted';

    public const STATUS_INTERVIEW_CALL = 'interview_call';

    public const STATUS_PLACED = 'placed';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'candidate_id',
        'job_id',
        'status',
        'applied_at',
        'status_changed_at',
        'notes',
        'external_reference',
        'external_url',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
            'status_changed_at' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class)->orderBy('created_at');
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_APPLIED,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_SHORTLISTED,
            self::STATUS_INTERVIEW_CALL,
            self::STATUS_PLACED,
            self::STATUS_REJECTED,
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_APPLIED => 'Applied',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_INTERVIEW_CALL => 'Interview Call',
            self::STATUS_PLACED => 'Placed',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst((string) $this->status);
    }
}
