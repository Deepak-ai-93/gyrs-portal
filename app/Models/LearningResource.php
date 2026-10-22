<?php

namespace App\Models;

use Database\Factories\LearningResourceFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningResource extends Model
{
    /** @use HasFactory<LearningResourceFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'skill_id',
        'title',
        'provider',
        'url',
        'skill_tags',
        'cost',
        'language',
        'format',
        'is_active',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'skill_tags' => 'array',
            'is_active' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
