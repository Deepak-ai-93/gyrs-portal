<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\AnalyzeResumeRequest;
use App\Models\AuditLog;
use App\Models\CandidateSkillPlan;
use App\Models\ResumeAnalysis;
use App\Models\Skill;
use App\Services\Ai\CareerAssistantService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class CareerAssistantController extends Controller
{
    public function __construct(private readonly CareerAssistantService $assistant) {}

    public function index(Request $request): View
    {
        $user = $request->user()->load(['district', 'skills']);

        $latest = ResumeAnalysis::query()
            ->where('user_id', $user->getKey())
            ->with([
                'jobMatches.job.district',
                'jobMatches.job.skills',
                'skillPlans.skill',
            ])
            ->latest()
            ->first();

        return view('candidate.career.index', [
            'user' => $user,
            'latest' => $latest,
            'history' => ResumeAnalysis::query()
                ->where('user_id', $user->getKey())
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    public function store(AnalyzeResumeRequest $request): RedirectResponse
    {
        @set_time_limit(60);

        try {
            $analysis = $this->assistant->analyze(
                $request->user(),
                $request->file('resume'),
                $request->input('resume_text'),
                $request->input('target_role'),
            );
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Career Assistant analysis failed.', [
                'user_id' => $request->user()->getKey(),
                'exception' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'We could not analyse that résumé right now. Please try again or paste the text instead.');
        }

        return redirect()
            ->route('candidate.career.show', $analysis)
            ->with('status', 'Your résumé analysis is ready.');
    }

    public function show(Request $request, ResumeAnalysis $analysis): View
    {
        $this->authorize('view', $analysis);

        $analysis->load([
            'jobMatches.job.district',
            'jobMatches.job.skills',
            'skillPlans.skill',
        ]);

        $user = $request->user()->load(['district', 'skills']);

        return view('candidate.career.show', [
            'analysis' => $analysis,
            'user' => $user,
            'savedJobIds' => $user->interestedJobs()->pluck('jobs.id')->all(),
        ]);
    }

    public function destroy(Request $request, ResumeAnalysis $analysis): RedirectResponse
    {
        $this->authorize('delete', $analysis);

        $this->assistant->deleteAnalysis($request->user(), $analysis);

        return redirect()
            ->route('candidate.career.index')
            ->with('status', 'Your résumé analysis has been deleted.');
    }

    public function apply(Request $request, ResumeAnalysis $analysis): RedirectResponse
    {
        $this->authorize('update', $analysis);

        $data = $request->validate([
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:120'],
            'qualification' => ['nullable', 'string', 'max:255'],
        ]);

        $count = $this->assistant->applySuggestions(
            $request->user(),
            $analysis,
            $data['skills'] ?? [],
            $data['qualification'] ?? null,
        );

        return back()->with('status', $count > 0
            ? $count.' skill'.($count === 1 ? '' : 's').' added to your profile.'
            : 'Your profile has been updated.');
    }

    public function updatePlan(Request $request, CandidateSkillPlan $plan): RedirectResponse
    {
        abort_unless($plan->user_id === $request->user()->getKey(), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(CandidateSkillPlan::statuses())],
            'add_to_profile' => ['sometimes', 'boolean'],
        ]);

        $status = $data['status'];
        $timestamps = [];

        if ($status === CandidateSkillPlan::STATUS_IN_PROGRESS && $plan->started_at === null) {
            $timestamps['started_at'] = now();
        }

        if ($status === CandidateSkillPlan::STATUS_COMPLETED) {
            $timestamps['started_at'] = $plan->started_at ?? now();
            $timestamps['completed_at'] = now();
        }

        if ($status === CandidateSkillPlan::STATUS_NOT_STARTED) {
            $timestamps['started_at'] = null;
            $timestamps['completed_at'] = null;
        }

        $plan->update(['status' => $status] + $timestamps);

        if ($status === CandidateSkillPlan::STATUS_COMPLETED && $request->boolean('add_to_profile')) {
            $skill = $plan->skill ?: Skill::firstOrCreate(
                ['slug' => Str::slug($plan->skill_name)],
                ['name' => $plan->skill_name],
            );

            $request->user()->skills()->syncWithoutDetaching([$skill->getKey()]);
            $plan->update(['skill_id' => $skill->getKey()]);

            if ($plan->analysis !== null) {
                $this->assistant->refreshMatches($plan->analysis);
            }
        }

        AuditLog::create([
            'actor_id' => $request->user()->getKey(),
            'action' => 'resume.skill_plan.updated',
            'entity_type' => CandidateSkillPlan::class,
            'entity_id' => $plan->getKey(),
            'metadata' => ['status' => $status],
            'created_at' => now(),
        ]);

        return back()->with('status', 'Skill plan updated.');
    }
}
