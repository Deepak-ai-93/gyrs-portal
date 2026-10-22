<?php

namespace App\Console\Commands;

use App\Models\ResumeAnalysis;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('ai:prune-resume-analyses')]
#[Description('Permanently delete résumé analyses past their retention period')]
class PruneResumeAnalyses extends Command
{
    public function handle(): int
    {
        $cutoff = now()->subDays(max(1, (int) config('ai.retention_days', 180)));
        $deleted = ResumeAnalysis::withTrashed()
            ->where('created_at', '<', $cutoff)
            ->forceDelete();

        $this->info('Permanently deleted '.$deleted.' expired résumé '.Str::plural('analysis', $deleted).'.');

        return self::SUCCESS;
    }
}
