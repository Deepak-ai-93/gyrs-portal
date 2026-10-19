<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\ApplicationStoreRequest;
use App\Models\Application;
use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applications) {}

    public function store(ApplicationStoreRequest $request, Job $job): RedirectResponse
    {
        $this->authorize('create', Application::class);

        $this->applications->create($request->user(), $job, $request->validated());

        return redirect()->route('candidate.dashboard')
            ->with('status', 'Application submitted successfully.');
    }
}
