<?php

namespace App\Services\Ai;

use App\Models\AuditLog;
use App\Models\CandidateSkillPlan;
use App\Models\LearningResource;
use App\Models\ResumeAnalysis;
use App\Models\ResumeJobMatch;
use App\Models\Skill;
use App\Models\User;
use App\Services\JobMatchService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CareerAssistantService
{
    public function __construct(
        private readonly ResumeTextExtractor $extractor,
        private readonly ResumeAnalyzer $analyzer,
        private readonly JobMatchService $matcher,
        private readonly SkillPlanBuilder $planBuilder,
    ) {}

    public function analyze(
        User $user,
        ?UploadedFile $file,
        ?string $text,
        ?string $targetRole,
    ): ResumeAnalysis {
        $resume = $file
            ? $this->extractor->fromUpload($file, $user->name)
            : $this->extractor->fromText((string) $text, $user->name);

        $analysis = ResumeAnalysis::create([
            'user_id' => $user->getKey(),
            'status' => ResumeAnalysis::STATUS_PROCESSING,
            'source_type' => $resume['source_type'],
            'original_filename' => $resume['original_filename'],
            'file_size' => $resume['file_size'],
            'content_hash' => $resume['content_hash'],
            'target_role' => $targetRole ? trim($targetRole) : null,
            'prompt_version' => config('ai.prompt_version'),
            'consent_version' => config('ai.consent_version'),
            'consent_at' => now(),
        ]);

        try {
            $this->audit($user, 'resume.analysis.started', $analysis, [
                'source_type' => $resume['source_type'],
            ]);

            $heuristic = $this->analyzer->extractHeuristic($user, $resume['text'], $targetRole);
            $initialMatches = $this->matcher->rankedMatches(
                $user,
                array_column($heuristic['skills'] ?? [], 'name'),
                $heuristic['qualification'] ?? $user->qualification,
                10,
            );

            $resources = LearningResource::query()
                ->where('is_active', true)
                ->orderBy('provider')
                ->get();

            $result = $this->analyzer->analyze(
                $user,
                $resume['text'],
                $targetRole,
                $initialMatches,
                $resources,
                $heuristic,
            );

            $skills = collect($result['profile']['skills'] ?? [])
                ->pluck('name')
                ->filter()
                ->values()
                ->all();

            $matches = $this->matcher->rankedMatches(
                $user,
                $skills,
                $result['profile']['qualification'] ?? $user->qualification,
                8,
            );

            $fitNotes = $result['analysis']['job_fit_notes'] ?? [];
            $plans = $this->planBuilder->build($user, $matches, $resources, $result['plan']);

            DB::transaction(function () use ($analysis, $result, $matches, $plans, $fitNotes): void {
                foreach ($matches as $rank => $match) {
                    $jobId = $match['job']->getKey();
                    $reason = is_string($fitNotes[$jobId] ?? null) && $fitNotes[$jobId] !== ''
                        ? $fitNotes[$jobId]
                        : implode(' ', $match['reasons']);

                    ResumeJobMatch::create([
                        'resume_analysis_id' => $analysis->getKey(),
                        'job_id' => $jobId,
                        'score' => $match['score'],
                        'rank' => $rank,
                        'matched_skills' => $match['matched'],
                        'missing_skills' => $match['missing'],
                        'breakdown' => $match['breakdown'],
                        'reason' => $reason,
                    ]);
                }

                foreach ($plans as $plan) {
                    CandidateSkillPlan::create([
                        'user_id' => $analysis->user_id,
                        'resume_analysis_id' => $analysis->getKey(),
                        'skill_id' => $plan['skill_id'] ?? null,
                        'skill_name' => $plan['skill_name'],
                        'priority' => $plan['priority'],
                        'why' => $plan['why'],
                        'estimated_weeks' => $plan['estimated_weeks'],
                        'roadmap_days' => $plan['roadmap_days'],
                        'actions' => $plan['actions'],
                        'resources' => $plan['resources'],
                        'project' => $plan['project'],
                        'proof' => $plan['proof'],
                        'status' => CandidateSkillPlan::STATUS_NOT_STARTED,
                    ]);
                }

                $analysis->update([
                    'status' => ResumeAnalysis::STATUS_COMPLETED,
                    'extracted_profile' => $result['profile'],
                    'ai_analysis' => $result['analysis'],
                    'ai_plan' => [
                        'engine' => $result['is_ai_generated'] ? 'ai' : 'local',
                        'warning' => $result['warning'],
                    ],
                    'is_ai_generated' => $result['is_ai_generated'],
                    'model_used' => $result['model'],
                    'input_tokens' => $result['input_tokens'],
                    'output_tokens' => $result['output_tokens'],
                    'cost_usd' => $this->estimateCost($result['input_tokens'], $result['output_tokens']),
                    'completed_at' => now(),
                ]);
            });

            $this->audit($user, 'resume.analysis.completed', $analysis, [
                'source_type' => $resume['source_type'],
                'is_ai_generated' => $result['is_ai_generated'],
                'matches' => $matches->count(),
                'skills' => count($plans),
            ]);

            return $analysis->refresh();
        } catch (Throwable $exception) {
            $analysis->update([
                'status' => ResumeAnalysis::STATUS_FAILED,
                'failed_at' => now(),
                'error_message' => mb_substr($exception->getMessage(), 0, 1000),
            ]);

            $this->audit($user, 'resume.analysis.failed', $analysis, [
                'source_type' => $resume['source_type'],
            ]);

            throw $exception;
        }
    }

    /**
     * @param  array<int, string>  $skills
     */
    public function applySuggestions(User $user, ResumeAnalysis $analysis, array $skills, ?string $qualification = null): int
    {
        $ids = collect($skills)
            ->filter(fn ($skill) => is_string($skill) && trim($skill) !== '')
            ->map(fn (string $skill) => Skill::firstOrCreate(
                ['slug' => Str::slug(trim($skill))],
                ['name' => trim($skill)],
            )->getKey())
            ->unique()
            ->values()
            ->all();

        $user->skills()->syncWithoutDetaching($ids);

        if ($qualification !== null && trim($qualification) !== '') {
            $user->update(['qualification' => mb_substr(trim($qualification), 0, 255)]);
        }

        $this->refreshMatches($analysis);

        $this->audit($user, 'resume.analysis.applied', $analysis, [
            'skills_added' => count($ids),
            'qualification_updated' => filled($qualification),
        ]);

        return count($ids);
    }

    public function refreshMatches(ResumeAnalysis $analysis): void
    {
        $analysis->loadMissing('user');
        $user = $analysis->user;
        $user->unsetRelation('skills')->load(['skills', 'district']);
        $profile = $analysis->extracted_profile ?? [];
        $skills = collect($profile['skills'] ?? [])->pluck('name')->filter()->all();
        $matches = $this->matcher->rankedMatches(
            $user,
            $skills,
            $user->qualification ?? ($profile['qualification'] ?? null),
            8,
        );

        DB::transaction(function () use ($analysis, $matches): void {
            $analysis->jobMatches()->delete();

            foreach ($matches as $rank => $match) {
                ResumeJobMatch::create([
                    'resume_analysis_id' => $analysis->getKey(),
                    'job_id' => $match['job']->getKey(),
                    'score' => $match['score'],
                    'rank' => $rank,
                    'matched_skills' => $match['matched'],
                    'missing_skills' => $match['missing'],
                    'breakdown' => $match['breakdown'],
                    'reason' => implode(' ', $match['reasons']),
                ]);
            }
        });
    }

    public function deleteAnalysis(User $user, ResumeAnalysis $analysis): void
    {
        DB::transaction(function () use ($analysis): void {
            $analysis->jobMatches()->delete();
            $analysis->skillPlans()->delete();
            $analysis->forceDelete();
        });

        $this->audit($user, 'resume.analysis.deleted', $analysis);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    private function audit(User $user, string $action, ResumeAnalysis $analysis, array $metadata = []): void
    {
        AuditLog::create([
            'actor_id' => $user->getKey(),
            'action' => $action,
            'entity_type' => ResumeAnalysis::class,
            'entity_id' => $analysis->getKey(),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    private function estimateCost(int $inputTokens, int $outputTokens): float
    {
        $input = ($inputTokens / 1_000_000) * (float) config('ai.input_cost_per_million', 0);
        $output = ($outputTokens / 1_000_000) * (float) config('ai.output_cost_per_million', 0);

        return round($input + $output, 6);
    }
}
