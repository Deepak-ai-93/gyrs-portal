<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\ResumeAnalysis;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $kpis = [
            'students' => User::query()->where('role', User::ROLE_CANDIDATE)->count(),
            'verified' => User::query()->where('role', User::ROLE_CANDIDATE)->where('status', User::STATUS_VERIFIED)->count(),
            'active_jobs' => Job::query()->open()->count(),
            'applications' => Application::query()->count(),
        ];

        $month = now()->format('Y-m');
        $aiSpend = DB::table('ai_monthly_spend')->where('month', $month)->first();
        $aiUsage = [
            'requests' => ResumeAnalysis::query()
                ->where('is_ai_generated', true)
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
            'input_tokens' => ResumeAnalysis::query()
                ->where('is_ai_generated', true)
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('input_tokens'),
            'output_tokens' => ResumeAnalysis::query()
                ->where('is_ai_generated', true)
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('output_tokens'),
            'spent_usd' => (float) ($aiSpend->spent_usd ?? 0),
            'reserved_usd' => (float) ($aiSpend->reserved_usd ?? 0),
            'budget_usd' => (float) config('ai.monthly_budget_usd', 20),
        ];

        return view('admin.dashboard', [
            'kpis' => $kpis,
            'aiUsage' => $aiUsage,
            'recentApplications' => Application::query()
                ->with(['candidate', 'job'])
                ->latest('applied_at')
                ->limit(5)
                ->get(),
            'recentJobs' => Job::query()
                ->with('district')
                ->latest('created_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
