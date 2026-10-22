<?php

namespace App\Models;

use Database\Factories\ResumeJobMatchFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeJobMatch extends Model
{
    /** @use HasFactory<ResumeJobMatchFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'resume_analysis_id',
        'job_id',
        'score',
        'rank',
        'matched_skills',
        'missing_skills',
        'breakdown',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'rank' => 'integer',
            'matched_skills' => 'array',
            'missing_skills' => 'array',
            'breakdown' => 'array',
        ];
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(ResumeAnalysis::class, 'resume_analysis_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
