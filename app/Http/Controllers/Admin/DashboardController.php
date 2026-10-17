<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Contracts\View\View;

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

        return view('admin.dashboard', [
            'kpis' => $kpis,
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
