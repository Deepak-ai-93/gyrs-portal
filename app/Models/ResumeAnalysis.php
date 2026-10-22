<?php

namespace App\Models;

use Database\Factories\ResumeAnalysisFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResumeAnalysis extends Model
{
    /** @use HasFactory<ResumeAnalysisFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'status',
        'source_type',
        'original_filename',
        'file_size',
        'content_hash',
        'target_role',
        'extracted_profile',
        'ai_analysis',
        'ai_plan',
        'is_ai_generated',
        'model_used',
        'prompt_version',
        'consent_version',
        'consent_at',
        'input_tokens',
        'output_tokens',
        'cost_usd',
        'completed_at',
        'failed_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'extracted_profile' => 'encrypted:array',
            'ai_analysis' => 'encrypted:array',
            'ai_plan' => 'encrypted:array',
            'is_ai_generated' => 'boolean',
            'consent_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'cost_usd' => 'decimal:6',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobMatches(): HasMany
    {
        return $this->hasMany(ResumeJobMatch::class)->orderBy('rank');
    }

    public function skillPlans(): HasMany
    {
        return $this->hasMany(CandidateSkillPlan::class)->orderByRaw("case priority when 'high' then 0 when 'medium' then 1 else 2 end");
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function sourceLabel(): string
    {
        return match ($this->source_type) {
            'pdf' => 'PDF résumé',
            'docx' => 'Word résumé',
            'text' => 'Pasted résumé',
            default => 'Résumé',
        };
    }
}
