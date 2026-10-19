<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\SavedJob;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InterestedJobController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $jobs = $user->interestedJobs()
            ->with(['district', 'skills'])
            ->latest('saved_jobs.created_at')
            ->paginate(9);

        return view('candidate.interested.index', [
            'jobs' => $jobs,
            'savedJobIds' => $user->savedJobs()->pluck('job_id')->all(),
        ]);
    }

    public function toggle(Request $request, Job $job): RedirectResponse
    {
        $user = $request->user();

        $existing = SavedJob::query()
            ->where('user_id', $user->getKey())
            ->where('job_id', $job->getKey())
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed “'.$job->title.'” from your interested jobs.';
        } else {
            SavedJob::create([
                'user_id' => $user->getKey(),
                'job_id' => $job->getKey(),
            ]);
            $message = 'Saved “'.$job->title.'” to your interested jobs.';
        }

        return back()->with('status', $message);
    }
}
