@extends('layouts.candidate')

@section('title', 'My applications — GYRS')

@php
    $stageKeys = ['applied', 'under_review', 'shortlisted', 'interview_call'];
    $stageLabels = ['Applied', 'Under review', 'Shortlisted', 'Interview call'];
@endphp

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">My applications</h1>
                    <p class="lead">Every vacancy you have applied for, with its live four-stage status.</p>
                </div>
                <x-button variant="outline" :href="route('home') . '#job-explorer'">
                    <x-icon name="search" :size="16" /> Explore more vacancies
                </x-button>
            </div>

            @if ($applications->isEmpty())
                <x-empty-state
                    icon="inbox"
                    title="You have not applied for any vacancies yet"
                    message="Browse verified government and private vacancies and apply once per role. Track each application through four live stages."
                >
                    <x-slot:action>
                        <x-button variant="outline" :href="route('home') . '#job-explorer'">Find your first vacancy</x-button>
                    </x-slot:action>
                </x-empty-state>
            @else
                @foreach ($applications as $application)
                    @php
                        $status = $application->status;
                        $current = array_search($status, $stageKeys, true);
                        $isRejected = $status === 'rejected';
                        $isPlaced = $status === 'placed';
                    @endphp

                    <article class="application-item">
                        <div class="application-head">
                            <div>
                                <h2 style="font-size:1.125rem;margin:0 0 2px;">
                                    @if ($application->job)
                                        <a href="{{ route('jobs.show', $application->job) }}">{{ $application->job->title }}</a>
                                    @else
                                        Vacancy
                                    @endif
                                </h2>
                                <p class="text-sm muted" style="margin:0;">
                                    {{ $application->job->department ?? '' }}
                                    @if ($application->job && $application->job->district) &middot; {{ $application->job->district->name }} @endif
                                    @if ($application->job && $application->job->salary_text) &middot; {{ $application->job->salary_text }} @endif
                                </p>
                            </div>
                            <div class="cluster">
                                <x-status-badge :status="$status" />
                                @if ($application->job)
                                    <x-button variant="ghost" size="sm" :href="route('jobs.show', $application->job)">View vacancy</x-button>
                                @endif
                            </div>
                        </div>

                        @if ($isRejected)
                            <p class="text-sm muted" style="margin:0;">This application was not progressed. You may continue applying for other roles.</p>
                        @else
                            <div class="tracker" role="list">
                                @foreach ($stageLabels as $index => $label)
                                    @php
                                        $isDone = $isPlaced || ($current !== false && $index < $current);
                                        $isCurrent = ! $isPlaced && $current !== false && $index === $current;
                                    @endphp
                                    <div class="tracker-step {{ $isDone ? 'is-done' : '' }} {{ $isCurrent ? 'is-current' : '' }}" role="listitem">
                                        <span class="step-dot" aria-hidden="true">{{ $isDone ? '✓' : $index + 1 }}</span>
                                        <span class="step-label">{{ $label }}</span>
                                        <span class="step-time">{{ $isDone ? 'Completed' : ($isCurrent ? 'Current stage' : 'Pending') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <p class="text-sm muted" style="margin:var(--space-3) 0 0;">
                            Applied
                            @if ($application->applied_at) on {{ \Illuminate\Support\Carbon::parse($application->applied_at)->format('d M Y') }} @endif
                            @if ($application->status_changed_at) &middot; last updated {{ \Illuminate\Support\Carbon::parse($application->status_changed_at)->diffForHumans() }} @endif
                        </p>
                    </article>
                @endforeach

                <x-pagination :paginator="$applications" />
            @endif
        </div>
    </section>
@endsection
