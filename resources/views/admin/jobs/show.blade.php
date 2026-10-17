@extends('layouts.admin')

@section('title', $job->title . ' — GYRS Portal admin')

@php
    $eligibility = $job->eligibility ?? collect();
    $responsibilities = $job->responsibilities ?? collect();
    $selectionSteps = $job->selectionSteps ?? collect();
    $applicationSteps = $job->applicationSteps ?? collect();
    $lastDate = $job->last_date ? \Illuminate\Support\Carbon::parse($job->last_date) : null;
    $isExpired = $lastDate && $lastDate->isPast();
@endphp

@section('content')
    <section class="section">
        <div class="container">
            <ol class="breadcrumbs">
                <li><a href="{{ route('admin.jobs.index') }}">Job postings</a></li>
                <li aria-current="page">{{ Str::limit($job->title, 48) }}</li>
            </ol>

            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">{{ $job->title }}</h1>
                    <p class="lead">{{ $job->department }}@if ($job->district) &middot; {{ $job->district->name }}@endif</p>
                </div>
                <div class="cluster">
                    <x-status-badge :status="$job->status" />
                    @if ($isExpired)
                        <span class="badge badge-danger">Deadline passed</span>
                    @endif
                    <x-button variant="outline" size="sm" :href="route('admin.jobs.edit', $job)"><x-icon name="edit" :size="15" /> Edit</x-button>
                    <x-button variant="ghost" size="sm" :href="route('jobs.show', $job)" target="_blank" rel="noopener noreferrer"><x-icon name="external" :size="15" /> Public view</x-button>
                    <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" data-confirm="Delete this job posting? This cannot be undone.">
                        @csrf
                        @method('DELETE')
                        <x-button variant="danger" size="sm" type="submit"><x-icon name="trash" :size="15" /> Delete</x-button>
                    </form>
                </div>
            </div>

            <div class="grid grid-2" style="margin-bottom:var(--space-6);">
                <x-card title="Vacancy summary">
                    <ul class="data-list">
                        <li><span class="data-label">Employment type</span><span class="data-value">{{ $job->employment_type ?? '—' }}</span></li>
                        <li><span class="data-label">Salary</span><span class="data-value">{{ $job->salary_text ?? '—' }}</span></li>
                        <li><span class="data-label">Qualification</span><span class="data-value">{{ $job->qualification ?? '—' }}</span></li>
                        <li><span class="data-label">Total posts</span><span class="data-value">{{ $job->total_posts ?? '—' }}</span></li>
                        <li><span class="data-label">Match score</span><span class="data-value">{{ $job->match_score ?? '—' }}</span></li>
                        <li><span class="data-label">Advertisement no.</span><span class="data-value">{{ $job->advertisement_number ?? '—' }}</span></li>
                        <li><span class="data-label">Posted on</span><span class="data-value">{{ $job->posted_at ? \Illuminate\Support\Carbon::parse($job->posted_at)->format('d M Y') : '—' }}</span></li>
                        <li><span class="data-label">Last date</span><span class="data-value">{{ $lastDate ? $lastDate->format('d M Y') : '—' }}</span></li>
                    </ul>
                </x-card>

                <x-card title="Links &amp; publication">
                    <ul class="data-list">
                        <li><span class="data-label">Status</span><span class="data-value"><x-status-badge :status="$job->status" /></span></li>
                        <li><span class="data-label">Age limit</span><span class="data-value">{{ $job->age_limit ?? '—' }}</span></li>
                        <li><span class="data-label">Fees</span><span class="data-value">{{ $job->fees ?? '—' }}</span></li>
                        <li><span class="data-label">Vacancy breakup</span><span class="data-value">{{ $job->vacancy_breakup ?? '—' }}</span></li>
                        <li><span class="data-label">Official website</span><span class="data-value">{{ $job->official_website_url ?? '—' }}</span></li>
                        <li><span class="data-label">Apply URL</span><span class="data-value">{{ $job->apply_url ?? '—' }}</span></li>
                        <li><span class="data-label">Notification</span><span class="data-value">{{ $job->notification_pdf_path ? basename($job->notification_pdf_path) : 'Not uploaded' }}</span></li>
                    </ul>
                </x-card>
            </div>

            @if ($job->relationLoaded('skills') && $job->skills->isNotEmpty())
                <x-card title="Skills" :meta="$job->skills->count() . ' skills tagged'">
                    <div class="badge-row">
                        @foreach ($job->skills as $skill)
                            <span class="badge">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                </x-card>
            @endif

            <div class="card" style="margin-top:var(--space-4);">
                <div class="card-body">
                    <div class="detail-section" style="padding-top:0;">
                        <h2>Job description</h2>
                        <p style="white-space:pre-line;margin:0;">{{ $job->description }}</p>
                    </div>

                    @if ($eligibility->isNotEmpty())
                        <div class="detail-section">
                            <h2>Eligibility criteria</h2>
                            <ul class="doc-list">@foreach ($eligibility as $item)<li>{{ $item->body }}</li>@endforeach</ul>
                        </div>
                    @endif

                    @if ($responsibilities->isNotEmpty())
                        <div class="detail-section">
                            <h2>Roles &amp; responsibilities</h2>
                            <ul class="doc-list">@foreach ($responsibilities as $item)<li>{{ $item->body }}</li>@endforeach</ul>
                        </div>
                    @endif

                    @if ($selectionSteps->isNotEmpty())
                        <div class="detail-section">
                            <h2>Selection process</h2>
                            <ol class="doc-list">@foreach ($selectionSteps as $item)<li>{{ $item->body }}</li>@endforeach</ol>
                        </div>
                    @endif

                    @if ($applicationSteps->isNotEmpty())
                        <div class="detail-section">
                            <h2>How to apply</h2>
                            <ol class="doc-list">@foreach ($applicationSteps as $item)<li>{{ $item->body }}</li>@endforeach</ol>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
