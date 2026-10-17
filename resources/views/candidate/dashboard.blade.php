@extends('layouts.candidate')

@section('title', 'Candidate dashboard — GYRS')

@php
    $applications = $user->applications ?? collect();
    $completeness = (int) ($profileCompleteness ?? 0);
    $stageKeys = ['applied', 'under_review', 'shortlisted', 'interview_call'];
    $stageLabels = ['Applied', 'Under review', 'Shortlisted', 'Interview call'];
    $initials = collect(explode(' ', trim((string) $user->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
        ->implode('');
    if ($applications->isEmpty()) {
        $nextAction = ['label' => 'Explore open vacancies', 'href' => route('home') . '#job-explorer', 'note' => 'Start by finding roles that match your qualification and district.'];
    } elseif ($completeness < 100) {
        $nextAction = ['label' => 'Complete your profile', 'href' => '#profile', 'note' => 'A complete dossier improves your match score and visibility to employers.'];
    } else {
        $nextAction = ['label' => 'Track your applications', 'href' => '#applications', 'note' => 'Your dossier is complete — monitor each application stage below.'];
    }
@endphp

@section('content')
    <section class="section" style="padding-bottom:0;">
        <div class="container">
            <div class="identity-bar">
                <div class="identity-main">
                    <span class="avatar" aria-hidden="true">{{ $initials ?: 'GY' }}</span>
                    <div>
                        <div class="identity-name">
                            <h1>{{ $user->name }}</h1>
                            @if ($user->enrollment_id)
                                <span class="badge" style="font-variant-numeric:tabular-nums;">{{ $user->enrollment_id }}</span>
                            @endif
                            <x-status-badge :status="$user->status ?? 'pending'" />
                        </div>
                        <div class="identity-meta">
                            @if ($user->qualification)<span><x-icon name="award" :size="14" /> {{ $user->qualification }}</span>@endif
                            @if ($user->district)<span><x-icon name="map-pin" :size="14" /> {{ $user->district->name }}</span>@endif
                            @if ($user->email)<span><x-icon name="mail" :size="14" /> {{ $user->email }}</span>@endif
                            @if ($user->phone)<span><x-icon name="phone" :size="14" /> {{ $user->phone }}</span>@endif
                        </div>
                    </div>
                </div>

                <div class="identity-actions">
                    <x-button variant="outline" size="sm" data-modal-open="profile">Edit profile</x-button>
                    <x-button variant="ghost" size="sm" :href="route('candidate.applications.index')">All applications</x-button>
                </div>
            </div>

            <div class="progress-block">
                <div class="progress-head">
                    <span class="muted">Profile completeness</span>
                    <span class="strong">{{ $completeness }}%</span>
                </div>
                <div class="progress" role="progressbar" aria-valuenow="{{ $completeness }}" aria-valuemin="0" aria-valuemax="100" aria-label="Profile completeness">
                    <div class="progress-bar" style="width:{{ max(0, min(100, $completeness)) }}%;"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top:var(--space-6);">
        <div class="container">
            <div class="kpi-grid" style="margin-bottom:var(--space-6);">
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Applications</div>
                        <div class="kpi-value">{{ $kpis['applications'] ?? 0 }}</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="file" :size="20" /></span>
                </div>
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Verified</div>
                        <div class="kpi-value">{{ $kpis['verified'] ?? 0 }}</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="check-circle" :size="20" /></span>
                </div>
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Shortlisted</div>
                        <div class="kpi-value">{{ $kpis['shortlisted'] ?? 0 }}</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="award" :size="20" /></span>
                </div>
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Interview calls</div>
                        <div class="kpi-value">{{ $kpis['interview_call'] ?? 0 }}</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="calendar" :size="20" /></span>
                </div>
            </div>

            <div class="panel panel-accent between" style="margin-bottom:var(--space-6);">
                <div>
                    <span class="badge badge-info"><x-icon name="sparkles" :size="13" /> Next suggested action</span>
                    <p class="text-sm" style="margin:8px 0 0;">{{ $nextAction['note'] }}</p>
                </div>
                <x-button variant="outline" :href="$nextAction['href']">{{ $nextAction['label'] }}</x-button>
            </div>

            <nav class="tabs" aria-label="Dashboard sections">
                <a href="#applications" class="is-active"><x-icon name="file" :size="16" /> Applications &amp; tracker</a>
                <a href="#recommended"><x-icon name="sparkles" :size="16" /> Recommended</a>
                <a href="#melas"><x-icon name="calendar" :size="16" /> Rozgar melas</a>
            </nav>

            <div id="applications" style="scroll-margin-top:80px;">
                <div class="between" style="margin-bottom:var(--space-4);">
                    <div>
                        <h2>Application tracker</h2>
                        <p class="text-sm muted" style="margin:0;">Every application follows four live stages.</p>
                    </div>
                </div>

                @if ($applications->isEmpty())
                    <x-empty-state
                        icon="inbox"
                        title="No applications yet"
                        message="When you apply for a vacancy it appears here with a live four-stage tracker: Applied, Under review, Shortlisted and Interview call."
                    >
                        <x-slot:action>
                            <x-button variant="outline" :href="route('home') . '#job-explorer'">Explore open vacancies</x-button>
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
                                    <h3>{{ $application->job->title ?? 'Vacancy' }}</h3>
                                    <p class="text-sm muted" style="margin:0;">
                                        {{ $application->job->department ?? '' }}
                                        @if ($application->job && $application->job->district) &middot; {{ $application->job->district->name }} @endif
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
                                <p class="text-sm muted" style="margin:0;">
                                    This application was not progressed. You can continue applying for other vacancies.
                                </p>
                            @else
                                <div class="tracker" role="list">
                                    @foreach ($stageLabels as $index => $label)
                                        @php
                                            $isDone = $isPlaced || ($current !== false && $index < $current);
                                            $isCurrent = ! $isPlaced && $current !== false && $index === $current;
                                            $stateLabel = $isDone ? 'Completed' : ($isCurrent ? 'Current stage' : 'Pending');
                                        @endphp
                                        <div class="tracker-step {{ $isDone ? 'is-done' : '' }} {{ $isCurrent ? 'is-current' : '' }}" role="listitem">
                                            <span class="step-dot" aria-hidden="true">{{ $isDone ? '✓' : $index + 1 }}</span>
                                            <span class="step-label">{{ $label }}</span>
                                            <span class="step-time">{{ $stateLabel }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <p class="text-sm muted" style="margin:var(--space-3) 0 0;">
                                Applied
                                @if ($application->applied_at)
                                    on {{ \Illuminate\Support\Carbon::parse($application->applied_at)->format('d M Y') }}
                                @endif
                                @if ($application->status_changed_at)
                                    &middot; last updated {{ \Illuminate\Support\Carbon::parse($application->status_changed_at)->diffForHumans() }}
                                @endif
                            </p>
                        </article>
                    @endforeach
                @endif
            </div>

            <div id="recommended" style="margin-top:var(--space-12);scroll-margin-top:80px;">
                <div class="between" style="margin-bottom:var(--space-4);">
                    <div>
                        <h2>Recommended vacancies</h2>
                        <p class="text-sm muted" style="margin:0;">Roles matched to your qualification, skills and district.</p>
                    </div>
                    <x-button variant="ghost" size="sm" :href="route('home') . '#job-explorer'">View all</x-button>
                </div>

                @if ($recommendedJobs->isEmpty())
                    <x-empty-state
                        icon="search"
                        title="No recommendations yet"
                        message="Add more skills to your profile so we can match you with suitable vacancies."
                    />
                @else
                    <div class="jobs-grid">
                        @foreach ($recommendedJobs as $job)
                            <article class="job-card card is-interactive">
                                <div class="card-body">
                                    <div class="job-card-top">
                                        <h3 class="job-card-title"><a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a></h3>
                                        @if (! is_null($job->match_score))
                                            <span class="badge badge-info">{{ (int) $job->match_score }}% match</span>
                                        @endif
                                    </div>
                                    <p class="job-card-dept">{{ $job->department }}</p>
                                    <ul class="meta-list">
                                        <li><x-icon name="map-pin" :size="16" /> {{ $job->district->name ?? $job->location }}</li>
                                        @if ($job->employment_type)<li><x-icon name="briefcase" :size="16" /> {{ $job->employment_type }}</li>@endif
                                        @if ($job->salary_text)<li><x-icon name="award" :size="16" /> {{ $job->salary_text }}</li>@endif
                                        @if ($job->last_date)<li><x-icon name="calendar" :size="16" /> Apply by {{ \Illuminate\Support\Carbon::parse($job->last_date)->format('d M Y') }}</li>@endif
                                    </ul>
                                </div>
                                <div class="card-foot">
                                    <x-button variant="outline" size="sm" :href="route('jobs.show', $job)">View details</x-button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="melas" style="margin-top:var(--space-12);scroll-margin-top:80px;">
                <div class="between" style="margin-bottom:var(--space-4);">
                    <div>
                        <h2>Rozgar melas &amp; recruitment drives</h2>
                        <p class="text-sm muted" style="margin:0;">Government-sponsored interview drives organised by the Directorate of Employment &amp; Training.</p>
                    </div>
                </div>

                @if ($melas->isEmpty())
                    <x-empty-state
                        icon="calendar"
                        title="No melas scheduled"
                        message="Upcoming Rozgar Melas in your district will be listed here."
                    />
                @else
                    <div class="grid grid-2">
                        @foreach ($melas as $mela)
                            <article class="mela-card">
                                <div class="cluster">
                                    @if ($mela->district)<span class="badge"><x-icon name="map-pin" :size="13" /> {{ $mela->district->name }}</span>@endif
                                    @if ($mela->status)<x-status-badge :status="$mela->status" />@endif
                                </div>
                                <h3>{{ $mela->title }}</h3>
                                <p class="text-sm muted" style="margin:0;"><x-icon name="map-pin" :size="14" /> {{ $mela->venue }}</p>
                                @if ($mela->starts_at)
                                    <p class="text-sm" style="margin:0;">
                                        <x-icon name="calendar" :size="14" />
                                        {{ \Illuminate\Support\Carbon::parse($mela->starts_at)->format('d M Y') }}
                                        @if ($mela->ends_at) &ndash; {{ \Illuminate\Support\Carbon::parse($mela->ends_at)->format('d M Y') }} @endif
                                    </p>
                                @endif
                                @if ($mela->registration_url)
                                    <x-button variant="outline" size="sm" :href="$mela->registration_url" target="_blank" rel="noopener noreferrer" style="margin-top:auto;">
                                        <x-icon name="external" :size="15" /> Register for entry pass
                                    </x-button>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <x-modal name="profile" title="Edit profile">
        <form method="POST" action="{{ route('candidate.profile.update') }}" data-loading-form id="candidate-profile-form">
            @csrf
            @method('PATCH')

            <div class="form-grid">
                <x-input name="name" label="Full name" :value="$user->name" :required="true" />
                <x-input name="phone" label="Mobile number" type="tel" :value="$user->phone" />
            </div>

            <x-input name="email" label="Email address" type="email" :value="$user->email" />

            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label" for="profile-district">Home district</label>
                    <input class="form-control" id="profile-district" type="text" value="{{ $user->district->name ?? 'Not set' }}" disabled>
                    <input type="hidden" name="district_id" value="{{ $user->district_id }}">
                    <p class="form-hint">Contact the Directorate to change your registered district.</p>
                </div>
                <x-input name="qualification" label="Highest qualification" :value="$user->qualification" :required="true" />
            </div>

            <div class="form-field">
                <p class="form-label" id="profile-skills-label">Key skills</p>
                @php
                    $existingSkills = $user->relationLoaded('skills') ? $user->skills->pluck('name')->values()->all() : [];
                    $skillSlots = max(3, count($existingSkills));
                @endphp
                <div class="form-grid">
                    @for ($i = 0; $i < $skillSlots; $i++)
                        <x-input name="skills[]" :label="'Skill ' . ($i + 1)" aria-labelledby="profile-skills-label" :value="$existingSkills[$i] ?? null" />
                    @endfor
                </div>
            </div>
        </form>

        <x-slot:footer>
            <x-button variant="ghost" type="button" data-modal-close>Cancel</x-button>
            <x-button variant="outline" type="submit" form="candidate-profile-form">Save profile</x-button>
        </x-slot:footer>
    </x-modal>
@endsection
