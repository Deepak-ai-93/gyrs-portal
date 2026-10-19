@extends('layouts.admin')

@section('title', 'Admin overview — GYRS Portal')

@php
    $students = (int) ($kpis['students'] ?? 0);
    $verified = (int) ($kpis['verified'] ?? 0);
    $activeJobs = (int) ($kpis['active_jobs'] ?? 0);
    $applicationsCount = (int) ($kpis['applications'] ?? 0);
    $pending = max(0, $students - $verified);
    $awaitingReview = $recentApplications->filter(fn ($application) => in_array($application->status, ['applied', 'under_review'], true))->count();

    $admin = auth()->user();
    $can = fn (string $permission) => (bool) $admin?->hasAdminPermission($permission);
    $canManageJobs = $can(\App\Support\AdminPermissions::JOBS_MANAGE);
    $canViewJobs = $can(\App\Support\AdminPermissions::JOBS_VIEW);
    $canViewCandidates = $can(\App\Support\AdminPermissions::CANDIDATES_VIEW);
    $canViewApplications = $can(\App\Support\AdminPermissions::APPLICATIONS_VIEW);
    $canExportCandidates = $can(\App\Support\AdminPermissions::EXPORT_CANDIDATES);
@endphp

@section('content')
    <section class="section" style="padding-bottom:0;">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Administration overview</h1>
                    <p class="lead">Statewide job feed, candidate verification and application audit across all 33 districts.</p>
                </div>
                <div class="cluster">
                    @if ($canManageJobs)
                        <x-button variant="outline" :href="route('admin.jobs.create')">
                            <x-icon name="plus" :size="16" /> Post new job
                        </x-button>
                    @endif
                    @if ($canExportCandidates)
                        <x-button variant="ghost" :href="route('admin.exports.candidates')">
                            <x-icon name="download" :size="16" /> Export roster CSV
                        </x-button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top:var(--space-6);">
        <div class="container">
            <div class="kpi-grid" style="margin-bottom:var(--space-6);">
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Candidate roster</div>
                        <div class="kpi-value">{{ $students }}</div>
                        <div class="kpi-note">Registered candidates</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="users" :size="20" /></span>
                </div>
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Verified candidates</div>
                        <div class="kpi-value">{{ $verified }}</div>
                        <div class="kpi-note">State verified</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="check-circle" :size="20" /></span>
                </div>
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Active job postings</div>
                        <div class="kpi-value">{{ $activeJobs }}</div>
                        <div class="kpi-note">Published vacancies</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="briefcase" :size="20" /></span>
                </div>
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Applications</div>
                        <div class="kpi-value">{{ $applicationsCount }}</div>
                        <div class="kpi-note">Audited submissions</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="file" :size="20" /></span>
                </div>
            </div>

            <div class="grid grid-2" style="margin-bottom:var(--space-6);">
                <div class="panel panel-accent">
                    <h2 style="font-size:1rem;">Operational alerts</h2>
                    @if ($pending > 0 || $awaitingReview > 0)
                        <ul class="audit-list">
                            @if ($pending > 0)
                                <li>
                                    <span class="audit-marker" style="background:var(--color-warning);"></span>
                                    <span><strong>{{ $pending }}</strong> candidate {{ Str::plural('record', $pending) }} awaiting verification. @if ($canViewCandidates)<a href="{{ route('admin.candidates.index') }}">Review roster</a>@endif</span>
                                </li>
                            @endif
                            @if ($awaitingReview > 0)
                                <li>
                                    <span class="audit-marker" style="background:var(--color-info);"></span>
                                    <span><strong>{{ $awaitingReview }}</strong> recent {{ Str::plural('application', $awaitingReview) }} awaiting review. @if ($canViewApplications)<a href="{{ route('admin.applications.index') }}">Open audit</a>@endif</span>
                                </li>
                            @endif
                        </ul>
                    @else
                        <p class="text-sm muted" style="margin:0;">No operational alerts. Verification and application queues are clear.</p>
                    @endif
                </div>

                <div class="panel">
                    <h2 style="font-size:1rem;">Quick actions</h2>
                    <div class="cluster">
                        @if ($canManageJobs)
                            <x-button variant="outline" size="sm" :href="route('admin.jobs.create')"><x-icon name="plus" :size="15" /> Post job</x-button>
                        @endif
                        @if ($canViewJobs)
                            <x-button variant="ghost" size="sm" :href="route('admin.jobs.index')"><x-icon name="briefcase" :size="15" /> Manage jobs</x-button>
                        @endif
                        @if ($canViewCandidates)
                            <x-button variant="ghost" size="sm" :href="route('admin.candidates.index')"><x-icon name="users" :size="15" /> Candidates</x-button>
                        @endif
                        @if ($canViewApplications)
                            <x-button variant="ghost" size="sm" :href="route('admin.applications.index')"><x-icon name="file" :size="15" /> Applications</x-button>
                        @endif
                        @if ($admin?->isSuperAdmin())
                            <x-button variant="ghost" size="sm" :href="route('admin.administrators.index')"><x-icon name="shield" :size="15" /> Administrators</x-button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-2">
                <x-card title="Recent applications" meta="Latest candidate submissions">
                    @if ($recentApplications->isEmpty())
                        <p class="text-sm muted" style="margin:0;">No applications recorded yet.</p>
                    @else
                        <div class="table-wrap">
                            <table class="table">
                                <caption class="sr-only">Recent applications</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Candidate</th>
                                        <th scope="col">Vacancy</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentApplications as $application)
                                        <tr>
                                            <td>{{ $application->candidate->name ?? '—' }}</td>
                                            <td>{{ Str::limit($application->job->title ?? '—', 36) }}</td>
                                            <td><x-status-badge :status="$application->status" /></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-card>

                <x-card title="Recent job postings" meta="Latest vacancies in the feed">
                    @if ($recentJobs->isEmpty())
                        <p class="text-sm muted" style="margin:0;">No jobs have been posted yet.</p>
                    @else
                        <div class="table-wrap">
                            <table class="table">
                                <caption class="sr-only">Recent job postings</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">District</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentJobs as $job)
                                        <tr>
                                            <td>
                                                @if ($canViewJobs)
                                                    <a href="{{ route('admin.jobs.show', $job) }}">{{ Str::limit($job->title, 36) }}</a>
                                                @else
                                                    {{ Str::limit($job->title, 36) }}
                                                @endif
                                            </td>
                                            <td>{{ $job->district->name ?? '—' }}</td>
                                            <td><x-status-badge :status="$job->status" /></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </section>
@endsection
