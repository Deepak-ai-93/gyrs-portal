<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\JobSearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct(private readonly JobSearchService $search) {}

    public function index(Request $request): View
    {
        $filters = $this->search->filtersFromRequest($request->only(['keyword', 'district', 'type']));

        return view('public.home', [
            'jobs' => $this->search->search($filters),
            'districts' => $this->search->districts(),
            'employmentTypes' => $this->search->employmentTypes(),
            'filters' => $filters,
            'jobCount' => $this->search->publishedJobCount(),
        ]);
    }

    public function show(Job $job): View
    {
        abort_if(in_array($job->status, [Job::STATUS_DRAFT, Job::STATUS_ARCHIVED], true), 404);

        $job->load([
            'district',
            'skills',
            'eligibility',
            'responsibilities',
            'selectionSteps',
            'applicationSteps',
        ]);

        $relatedJobs = Job::query()
            ->open()
            ->with(['district', 'skills'])
            ->whereKeyNot($job->getKey())
            ->where(function ($query) use ($job) {
                $query->where('district_id', $job->district_id)
                    ->orWhere('employment_type', $job->employment_type);
            })
            ->limit(4)
            ->get();

        return view('public.jobs.show', [
            'job' => $job,
            'relatedJobs' => $relatedJobs,
        ]);
    }
}
