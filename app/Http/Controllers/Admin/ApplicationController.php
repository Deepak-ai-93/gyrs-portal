<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApplicationStatusRequest;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applications) {}

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->input('status'),
            'keyword' => $request->input('keyword'),
        ];

        $applications = Application::query()
            ->with(['candidate', 'job'])
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['keyword'], function ($query, string $term) {
                $like = '%'.str_replace('%', '\%', trim($term)).'%';
                $query->where(function ($query) use ($like) {
                    $query->whereHas('candidate', fn ($query) => $query->where('name', 'like', $like)->orWhere('email', 'like', $like))
                        ->orWhereHas('job', fn ($query) => $query->where('title', 'like', $like));
                });
            })
            ->latest('applied_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.applications.index', [
            'applications' => $applications,
            'filters' => $filters,
            'statuses' => Application::statuses(),
        ]);
    }

    public function updateStatus(ApplicationStatusRequest $request, Application $application): RedirectResponse
    {
        $this->applications->updateStatus(
            $application,
            $request->string('status')->value(),
            $request->user(),
            $request->input('notes'),
        );

        return redirect()->back()->with('status', 'Application status updated.');
    }
}
