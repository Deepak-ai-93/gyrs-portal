<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CandidateStatusRequest;
use App\Models\District;
use App\Models\User;
use App\Services\ApplicationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function __construct(private readonly ApplicationService $applications) {}

    public function index(Request $request): View
    {
        $filters = [
            'keyword' => $request->input('keyword'),
            'district' => $request->input('district'),
            'status' => $request->input('status'),
        ];

        $candidates = User::query()
            ->where('role', User::ROLE_CANDIDATE)
            ->with(['district', 'skills'])
            ->when($filters['keyword'], function ($query, string $term) {
                $like = '%'.str_replace('%', '\%', trim($term)).'%';
                $query->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('enrollment_id', 'like', $like);
                });
            })
            ->when($filters['district'], fn ($query, $district) => $query->where('district_id', $district))
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.candidates.index', [
            'candidates' => $candidates,
            'districts' => District::query()->orderBy('sort_order')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(User $candidate): View
    {
        abort_unless($candidate->isCandidate(), 404);

        $candidate->load(['district', 'skills', 'applications.job']);

        return view('admin.candidates.show', [
            'candidate' => $candidate,
        ]);
    }

    public function updateStatus(CandidateStatusRequest $request, User $candidate): RedirectResponse
    {
        abort_unless($candidate->isCandidate(), 404);

        $status = $request->string('status')->value();

        $candidate->update([
            'status' => $status,
            'approved_at' => $status === User::STATUS_VERIFIED ? ($candidate->approved_at ?? now()) : $candidate->approved_at,
        ]);

        $this->applications->audit($request->user(), 'candidate.status_changed', $candidate, [
            'status' => $status,
            'notes' => $request->input('notes'),
        ]);

        return redirect()->back()->with('status', 'Candidate status updated.');
    }
}
