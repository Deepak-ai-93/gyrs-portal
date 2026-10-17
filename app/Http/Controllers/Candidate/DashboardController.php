<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Application;
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
        $user = $request->user()->load(['district', 'skills', 'applications.job.district']);

        $kpis = [
            'applications' => $user->applications->count(),
            'verified' => $user->applications->where('status', Application::STATUS_UNDER_REVIEW)->count(),
            'shortlisted' => $user->applications->where('status', Application::STATUS_SHORTLISTED)->count(),
            'interview_call' => $user->applications->where('status', Application::STATUS_INTERVIEW_CALL)->count(),
        ];

        return view('candidate.dashboard', [
            'user' => $user,
            'profileCompleteness' => $this->completeness->calculate($user),
            'recommendedJobs' => $this->matcher->recommendedJobs($user),
            'kpis' => $kpis,
            'melas' => RozgarMela::query()->upcoming()->with('district')->limit(4)->get(),
        ]);
    }
}
