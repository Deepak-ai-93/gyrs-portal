<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\RozgarMela;
use App\Services\JobMatchService;
use App\Services\ProfileCompletenessService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ProfileCompletenessService $completeness,
        private readonly JobMatchService $matcher,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user()->load(['district', 'skills']);

        $recommendedJobs = $this->matcher->recommendedJobs($user);

        $savedJobs = $user->interestedJobs()
            ->with(['district', 'skills'])
            ->latest('saved_jobs.created_at')
            ->get();

        $melas = RozgarMela::query()->upcoming()->with('district')->limit(4)->get();
        $careerAnalysis = $user->resumeAnalyses()->first();

        return view('candidate.dashboard', [
            'user' => $user,
            'profileCompleteness' => $this->completeness->calculate($user),
            'savedJobs' => $savedJobs,
            'savedJobIds' => $savedJobs->pluck('id')->all(),
            'recommendedJobs' => $recommendedJobs,
            'suggestedSkills' => $this->matcher->suggestedSkills($user),
            'kpis' => [
                'interested' => $savedJobs->count(),
                'recommended' => $recommendedJobs->count(),
                'melas' => $melas->count(),
            ],
            'melas' => $melas,
            'careerAnalysis' => $careerAnalysis,
        ]);
    }
}
