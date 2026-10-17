<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\ApplicationStoreRequest;
use App\Models\Application;
use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applications) {}

    public function index(Request $request): View
    {
        $applications = $request->user()
            ->applications()
            ->with(['job.district'])
            ->latest('applied_at')
            ->paginate(10);

        return view('candidate.applications.index', [
            'applications' => $applications,
        ]);
    }

    public function store(ApplicationStoreRequest $request, Job $job): RedirectResponse
    {
        $this->authorize('create', Application::class);

        $this->applications->create($request->user(), $job, $request->validated());

        return redirect()->route('candidate.applications.index')
            ->with('status', 'Application submitted successfully.');
    }
}
