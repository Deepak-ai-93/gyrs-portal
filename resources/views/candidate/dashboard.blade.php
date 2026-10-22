@extends('layouts.candidate')

@section('title', 'Candidate dashboard — GYRS')

@php
    $completeness = (int) ($profileCompleteness ?? 0);
    $initials = collect(explode(' ', trim((string) $user->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
        ->implode('');

    if ($savedJobs->isEmpty() && $recommendedJobs->isEmpty()) {
        $nextAction = ['label' => 'Explore open vacancies', 'href' => route('home') . '#job-explorer', 'note' => 'Start by browsing verified vacancies that match your qualification and district.'];
    } elseif ($savedJobs->isEmpty()) {
        $nextAction = ['label' => 'Save jobs you like', 'href' => '#recommended', 'note' => 'You have AI recommendations ready — mark the ones you are interested in to shortlist them.'];
    } elseif ($completeness < 100) {
        $nextAction = ['label' => 'Complete your profile', 'href' => '#profile', 'note' => 'A complete profile improves your match score and the quality of AI suggestions.'];
    } else {
        $nextAction = ['label' => 'Review your interest list', 'href' => '#interested', 'note' => 'Your profile is complete — keep your interested jobs list up to date.'];
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
                    <x-button variant="ghost" size="sm" :href="route('candidate.interested.index')">My interested jobs</x-button>
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
                        <div class="kpi-label">Interested jobs</div>
                        <div class="kpi-value">{{ $kpis['interested'] ?? 0 }}</div>
                        <div class="kpi-note">Saved to your list</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="sparkles" :size="20" /></span>
                </div>
                <div class="kpi">
                    <div>
                        <div class="kpi-label">AI matches</div>
                        <div class="kpi-value">{{ $kpis['recommended'] ?? 0 }}</div>
                        <div class="kpi-note">Recommended for you</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="briefcase" :size="20" /></span>
                </div>
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Rozgar melas</div>
                        <div class="kpi-value">{{ $kpis['melas'] ?? 0 }}</div>
                        <div class="kpi-note">Upcoming drives</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="calendar" :size="20" /></span>
                </div>
                <div class="kpi">
                    <div>
                        <div class="kpi-label">Profile strength</div>
                        <div class="kpi-value">{{ $completeness }}%</div>
                        <div class="kpi-note">Complete your dossier</div>
                    </div>
                    <span class="kpi-icon"><x-icon name="user" :size="20" /></span>
                </div>
            </div>

            <div class="panel panel-accent between" style="margin-bottom:var(--space-6);">
                <div>
                    <span class="badge badge-info"><x-icon name="sparkles" :size="13" /> Next suggested action</span>
                    <p class="text-sm" style="margin:8px 0 0;">{{ $nextAction['note'] }}</p>
                </div>
                <x-button variant="outline" :href="$nextAction['href']">{{ $nextAction['label'] }}</x-button>
            </div>

            <section class="panel between" aria-labelledby="career-assistant-heading" style="margin-bottom:var(--space-6);">
                <div>
                    <span class="badge badge-info"><x-icon name="sparkles" :size="13" /> Career Assistant</span>
                    <h2 id="career-assistant-heading" style="margin:var(--space-2) 0;">
                        @if (! $careerAnalysis)
                            Get a résumé snapshot and skill plan
                        @elseif ($careerAnalysis->isCompleted())
                            Your latest résumé analysis is ready
                        @elseif ($careerAnalysis->status === \App\Models\ResumeAnalysis::STATUS_PROCESSING)
                            Your résumé analysis is processing
                        @else
                            Analyse your résumé again
                        @endif
                    </h2>
                    <p class="text-sm muted" style="margin:0;">
                        @if ($careerAnalysis?->isCompleted())
                            {{ $careerAnalysis->completed_at?->diffForHumans() ?? 'Recently completed' }} · {{ $careerAnalysis->is_ai_generated ? 'AI-assisted' : 'Local analysis' }}
                        @else
                            See open-job matches and a practical 30/60/90-day skill plan.
                        @endif
                    </p>
                </div>
                <x-button variant="outline" :href="$careerAnalysis?->isCompleted() ? route('candidate.career.show', $careerAnalysis) : route('candidate.career.index')">
                    {{ $careerAnalysis?->isCompleted() ? 'View analysis' : 'Open Career Assistant' }}
                </x-button>
            </section>

            <nav class="tabs" aria-label="Dashboard sections">
                <a href="#interested" class="is-active"><x-icon name="sparkles" :size="16" /> Interested jobs</a>
                <a href="#recommended"><x-icon name="briefcase" :size="16" /> AI recommended</a>
                <a href="#skills"><x-icon name="award" :size="16" /> Skill upgradation</a>
                <a href="#melas"><x-icon name="calendar" :size="16" /> Rozgar melas</a>
            </nav>

            <div id="interested" style="scroll-margin-top:80px;">
                <div class="between" style="margin-bottom:var(--space-4);">
                    <div>
                        <h2>Jobs you are interested in</h2>
                        <p class="text-sm muted" style="margin:0;">A simple directory of roles you have saved. Remove any that no longer interest you.</p>
                    </div>
                </div>

                @if ($savedJobs->isEmpty())
                    <x-empty-state
                        icon="sparkles"
                        title="No interested jobs yet"
                        message="Mark any vacancy as interested while browsing and it will appear here for quick access."
                    >
                        <x-slot:action>
                            <x-button variant="outline" :href="route('home') . '#job-explorer'">Explore open vacancies</x-button>
                        </x-slot:action>
                    </x-empty-state>
                @else
                    <div class="jobs-grid">
                        @foreach ($savedJobs as $job)
                            <x-job-card :job="$job" :saved="true" />
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="recommended" style="margin-top:var(--space-12);scroll-margin-top:80px;">
                <div class="between" style="margin-bottom:var(--space-4);">
                    <div>
                        <h2><x-icon name="sparkles" :size="18" /> AI recommended for you</h2>
                        <p class="text-sm muted" style="margin:0;">Roles matched to your qualification, skills and district by our recommendation engine.</p>
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
                            <x-job-card :job="$job" :saved="in_array($job->getKey(), $savedJobIds, true)" />
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="skills" style="margin-top:var(--space-12);scroll-margin-top:80px;">
                <div class="between" style="margin-bottom:var(--space-4);">
                    <div>
                        <h2><x-icon name="award" :size="18" /> Skill upgradation</h2>
                        <p class="text-sm muted" style="margin:0;">In-demand skills appearing across matched vacancies that are not yet on your profile.</p>
                    </div>
                </div>

                @if ($suggestedSkills->isEmpty())
                    <x-empty-state
                        icon="check-circle"
                        title="Your skills are in demand"
                        message="Your profile already covers the skills requested by the vacancies we matched to you. Keep it updated as you learn more."
                    />
                @else
                    <div class="skill-grid">
                        @foreach ($suggestedSkills as $skill)
                            <article class="skill-card">
                                <div class="cluster">
                                    <span class="badge badge-info"><x-icon name="sparkles" :size="13" /> In demand</span>
                                    <span class="badge">{{ $skill['jobs'] }} {{ Str::plural('role', $skill['jobs']) }}</span>
                                </div>
                                <h3>{{ $skill['name'] }}</h3>
                                <p class="text-sm muted" style="margin:0;">
                                    Requested by {{ $skill['jobs'] }} open {{ Str::plural('vacancy', $skill['jobs']) }}. Upskill in this area to raise your match score.
                                </p>
                                <x-button variant="outline" size="sm" :href="route('home', ['keyword' => $skill['name']]) . '#job-explorer'">
                                    View roles needing this
                                </x-button>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="melas" style="margin-top:var(--space-12);scroll-margin-top:80px;">
                <div class="between" style="margin-bottom:var(--space-4);">
                    <div>
                        <h2>Rozgar melas &amp; recruitment drives</h2>
                        <p class="text-sm muted" style="margin:0;">Interview drives organised at district employment offices.</p>
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
                    <p class="form-hint">Contact support to change your registered district.</p>
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
