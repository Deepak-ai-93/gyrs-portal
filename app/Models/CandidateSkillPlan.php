<?php

namespace App\Models;

use Database\Factories\CandidateSkillPlanFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateSkillPlan extends Model
{
    /** @use HasFactory<CandidateSkillPlanFactory> */
    use HasFactory, HasUuids;

    public const STATUS_NOT_STARTED = 'not_started';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'resume_analysis_id',
        'skill_id',
        'skill_name',
        'priority',
        'why',
        'estimated_weeks',
        'roadmap_days',
        'actions',
        'resources',
        'project',
        'proof',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_weeks' => 'integer',
            'roadmap_days' => 'integer',
            'actions' => 'array',
            'resources' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(ResumeAnalysis::class, 'resume_analysis_id');
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_NOT_STARTED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
        ];
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_IN_PROGRESS => 'In progress',
            self::STATUS_COMPLETED => 'Completed',
            default => 'Not started',
        };
    }
}
