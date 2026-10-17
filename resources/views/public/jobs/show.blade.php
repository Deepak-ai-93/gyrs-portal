@extends('layouts.public')

@section('title', $job->title . ' — GYRS')
@section('meta_description', Str::limit(strip_tags($job->description ?? ''), 150))

@php
    $user = auth()->user();
    $isCandidate = $user && method_exists($user, 'isCandidate') && $user->isCandidate();
    $lastDate = $job->last_date ? \Illuminate\Support\Carbon::parse($job->last_date) : null;
    $isExpired = ($lastDate && $lastDate->isPast()) || (method_exists($job, 'isExpired') && $job->isExpired());
    $isClosed = in_array($job->status, ['closed', 'archived'], true) || $isExpired;

    $eligibility = $job->eligibility ?? collect();
    $responsibilities = $job->responsibilities ?? collect();
    $selectionSteps = $job->selectionSteps ?? collect();
    $applicationSteps = $job->applicationSteps ?? collect();

    $pdfUrl = null;
    if ($job->notification_pdf_path) {
        $pdfUrl = Str::startsWith($job->notification_pdf_path, ['http://', 'https://'])
            ? $job->notification_pdf_path
            : asset('storage/' . ltrim($job->notification_pdf_path, '/'));
    }
@endphp

@section('content')
    <div class="breadcrumb-bar">
        <div class="container">
            <ol class="breadcrumbs" style="margin:0;">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('home') }}#job-explorer">Explore jobs</a></li>
                <li aria-current="page">{{ Str::limit($job->title, 48) }}</li>
            </ol>
        </div>
    </div>

    <section class="section" style="padding-top:var(--space-8);">
        <div class="container">
            <div class="split">
                <div class="split-main">
                    <a class="btn btn-ghost btn-sm" href="{{ route('home') }}#job-explorer" style="margin-bottom:var(--space-4);">
                        <x-icon name="arrow-left" :size="16" /> Back to all jobs
                    </a>

                    <div class="page-head" style="margin-bottom:var(--space-4);">
                        <div>
                            <h1 style="font-size:clamp(1.75rem,3vw,2.5rem);">{{ $job->title }}</h1>
                            <p class="lead">{{ $job->department }}@if ($job->district) &middot; {{ $job->district->name }}@endif</p>
                        </div>
                        <div class="cluster">
                            <x-status-badge :status="$job->status" />
                            @if (! is_null($job->match_score))
                                <span class="badge badge-info">{{ (int) $job->match_score }}% match</span>
                            @endif
                        </div>
                    </div>

                    @if ($isClosed)
                        <div class="apply-state">
                            <x-icon name="alert" :size="18" />
                            This vacancy is closed. Applications are no longer being accepted.
                        </div>
                    @endif

                    <div class="panel" style="margin-bottom:var(--space-6);">
                        <ul class="data-list">
                            <li>
                                <span class="data-label">Location</span>
                                <span class="data-value">{{ $job->district->name ?? $job->location ?? 'Gujarat' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Employment type</span>
                                <span class="data-value">{{ $job->employment_type ?? '—' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Salary</span>
                                <span class="data-value">{{ $job->salary_text ?? '—' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Qualification</span>
                                <span class="data-value">{{ $job->qualification ?? '—' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Advertisement no.</span>
                                <span class="data-value">{{ $job->advertisement_number ?? '—' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Total posts</span>
                                <span class="data-value">{{ $job->total_posts ?? '—' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Posted on</span>
                                <span class="data-value">{{ $job->posted_at ? \Illuminate\Support\Carbon::parse($job->posted_at)->format('d M Y') : '—' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Last date to apply</span>
                                <span class="data-value">
                                    {{ $lastDate ? $lastDate->format('d M Y') : '—' }}
                                    @if ($isExpired && ! in_array($job->status, ['closed', 'archived'], true))
                                        (expired)
                                    @endif
                                </span>
                            </li>
                        </ul>

                        @if ($job->relationLoaded('skills') && $job->skills->isNotEmpty())
                            <div class="badge-row" style="margin-top:var(--space-4);">
                                @foreach ($job->skills as $skill)
                                    <span class="badge">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="detail-section">
                        <h2>Overview</h2>
                        <p style="white-space:pre-line;">{{ $job->description }}</p>
                    </div>

                    @if ($eligibility->isNotEmpty())
                        <div class="detail-section">
                            <h2>Eligibility</h2>
                            <ul class="doc-list">
                                @foreach ($eligibility as $item)
                                    <li>{{ $item->body }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="detail-section">
                        <h2>Vacancy details</h2>
                        <ul class="data-list">
                            <li>
                                <span class="data-label">Total posts</span>
                                <span class="data-value">{{ $job->total_posts ?? '—' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Age limit</span>
                                <span class="data-value">{{ $job->age_limit ?? '—' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Application fee</span>
                                <span class="data-value">{{ $job->fees ?? '—' }}</span>
                            </li>
                            <li>
                                <span class="data-label">Vacancy breakup</span>
                                <span class="data-value">{{ $job->vacancy_breakup ?? '—' }}</span>
                            </li>
                        </ul>
                    </div>

                    @if ($responsibilities->isNotEmpty())
                        <div class="detail-section">
                            <h2>Roles &amp; responsibilities</h2>
                            <ul class="doc-list">
                                @foreach ($responsibilities as $item)
                                    <li>{{ $item->body }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($selectionSteps->isNotEmpty())
                        <div class="detail-section">
                            <h2>Selection process</h2>
                            <ol class="doc-list">
                                @foreach ($selectionSteps as $item)
                                    <li>{{ $item->body }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    @if ($applicationSteps->isNotEmpty())
                        <div class="detail-section">
                            <h2>How to apply</h2>
                            <ol class="doc-list">
                                @foreach ($applicationSteps as $item)
                                    <li>{{ $item->body }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    <div class="detail-section">
                        <h2>Official notification</h2>
                        @if ($pdfUrl)
                            <x-button variant="outline" :href="$pdfUrl" target="_blank" rel="noopener noreferrer">
                                <x-icon name="download" :size="16" /> Download official notification (PDF)
                            </x-button>
                        @else
                            <p class="muted" style="margin:0;">No notification document has been published for this vacancy yet.</p>
                        @endif
                    </div>
                </div>

                <aside aria-label="Application and related links">
                    <div class="side-card">
                        <h2>Apply for this vacancy</h2>

                        @if ($isClosed)
                            <p class="text-sm muted">Applications are closed for this vacancy.</p>
                            <x-button variant="ghost" block disabled>Applications closed</x-button>
                        @elseif ($isCandidate)
                            <form method="POST" action="{{ route('jobs.applications.store', $job) }}" data-loading-form>
                                @csrf

                                <x-textarea name="notes" label="Message to the recruitment cell" :rows="3" hint="Optional — mention anything the reviewer should know." />

                                <x-button
                                    variant="outline"
                                    block
                                    type="submit"
                                    data-confirm="Submit your application for {{ $job->title }}?"
                                >
                                    Apply now
                                </x-button>
                            </form>
                            <p class="text-sm muted" style="margin:var(--space-3) 0 0;">
                                Your application is logged into your candidate dossier and tracked through four live stages.
                            </p>
                        @elseif ($user)
                            <p class="text-sm muted" style="margin-bottom:var(--space-3);">
                                You are signed in as an administrator. Applications are submitted from candidate accounts.
                            </p>
                        @else
                            <x-button variant="outline" block :href="route('candidate.login')">Sign in to apply</x-button>
                            <p class="text-sm muted" style="margin:var(--space-3) 0 0;">
                                You will be taken to candidate sign in, then returned to complete your application.
                            </p>
                        @endif

                        @if ($job->apply_url)
                            <div style="margin-top:var(--space-4);padding-top:var(--space-4);border-top:1px solid var(--color-border);">
                                <p class="text-sm muted" style="margin-bottom:8px;">Official government application portal:</p>
                                <x-button variant="ghost" block :href="$job->apply_url" target="_blank" rel="noopener noreferrer">
                                    <x-icon name="external" :size="16" /> Apply on official portal
                                </x-button>
                            </div>
                        @endif
                    </div>

                    <div class="side-card">
                        <h2>Important links</h2>
                        <div class="stack" style="margin-top:0;">
                            @if ($job->official_website_url)
                                <a class="text-sm" href="{{ $job->official_website_url }}" target="_blank" rel="noopener noreferrer">
                                    <x-icon name="external" :size="15" /> Official website
                                </a>
                            @endif
                            @if ($pdfUrl)
                                <a class="text-sm" href="{{ $pdfUrl }}" target="_blank" rel="noopener noreferrer">
                                    <x-icon name="file" :size="15" /> Notification PDF
                                </a>
                            @endif
                            <button type="button" class="btn btn-link" style="text-align:left;" onclick="window.print()">
                                <x-icon name="download" :size="15" /> Print this page
                            </button>
                        </div>
                    </div>

                    <div class="side-card">
                        <h2>Similar jobs</h2>
                        @if ($relatedJobs->isEmpty())
                            <p class="text-sm muted" style="margin:0;">No similar vacancies right now.</p>
                        @else
                            <div class="related-list">
                                @foreach ($relatedJobs as $related)
                                    <a class="related-item" href="{{ route('jobs.show', $related) }}">
                                        <span class="related-title">{{ $related->title }}</span>
                                        <span class="text-sm muted">{{ $related->department }}@if ($related->district) &middot; {{ $related->district->name }}@endif</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="panel">
                        <h2 style="font-size:.9375rem;"><x-icon name="shield" :size="15" /> Trust &amp; compliance</h2>
                        <ul class="dot-list text-sm" style="margin:0;">
                            <li>Verified Government of Gujarat posting</li>
                            <li>Free candidate registration</li>
                            <li>Zero intermediaries or agents</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
