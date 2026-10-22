@extends('layouts.candidate')

@section('title', 'Your résumé analysis — GYRS')

@php
    $profile = $analysis->extracted_profile ?? [];
    $ai = $analysis->ai_analysis ?? [];
    $skills = collect($profile['skills'] ?? []);
    $existing = $user->skills->pluck('name')->map(fn ($name) => mb_strtolower((string) $name));
    $suggestedSkills = $skills->filter(fn ($skill) => is_array($skill) && filled($skill['name'] ?? null));
    $topMatch = $analysis->jobMatches->first();
    $planCount = $analysis->skillPlans->count();
    $highPriority = $analysis->skillPlans->where('priority', 'high')->count();
@endphp

@section('content')
    <section class="section" style="padding-bottom:0;">
        <div class="container">
            <div class="page-head">
                <div>
                    <span class="badge badge-info"><x-icon name="sparkles" :size="13" /> Career Assistant</span>
                    <h1 style="margin-top:var(--space-3);">Your résumé analysis</h1>
                    <p class="lead">
                        {{ $analysis->sourceLabel() }} · {{ $analysis->completed_at?->format('d M Y, H:i') }}
                        @if ($analysis->target_role)
                            · target: {{ $analysis->target_role }}
                        @endif
                    </p>
                </div>
                <div class="cluster">
                    <x-button variant="ghost" size="sm" :href="route('candidate.career.index')">
                        <x-icon name="arrow-left" :size="16" /> Career Assistant
                    </x-button>
                    <x-button variant="outline" size="sm" data-modal-open="apply-profile">
                        <x-icon name="edit" :size="16" /> Update profile
                    </x-button>
                </div>
            </div>

            <div class="career-result-meta">
                @if ($analysis->is_ai_generated)
                    <span class="badge badge-info"><x-icon name="sparkles" :size="13" /> AI-assisted</span>
                @else
                    <span class="badge"><x-icon name="info" :size="13" /> Local analysis</span>
                @endif
                <span class="text-sm muted">AI guidance only — verify eligibility in the official job notification.</span>
            </div>
            @if (filled(data_get($analysis->ai_plan, 'warning')))
                <div class="alert alert-info" role="status" style="margin-top:var(--space-4);">
                    {{ data_get($analysis->ai_plan, 'warning') }}
                </div>
            @endif
        </div>
    </section>

    <section class="section" style="padding-top:var(--space-6);">
        <div class="container">
            <div class="career-result-layout">
                <div class="career-result-main">
                    <div class="panel" id="snapshot">
                        <div class="between" style="margin-bottom:var(--space-4);">
                            <div>
                                <h2 style="margin-bottom:var(--space-1);">Résumé snapshot</h2>
                                <p class="text-sm muted" style="margin:0;">What we understood from your résumé. Check the details before applying to jobs.</p>
                            </div>
                        </div>

                        @if (! empty($profile['summary']))
                            <p class="career-summary">{{ $profile['summary'] }}</p>
                        @endif

                        <div class="career-snapshot-grid">
                            @if (! empty($profile['education']))
                                <section class="career-snapshot-block">
                                    <h3><x-icon name="award" :size="16" /> Education</h3>
                                    <ul>
                                        @foreach ($profile['education'] as $education)
                                            <li>
                                                <strong>{{ $education['degree'] ?? 'Qualification' }}</strong>
                                                @if (! empty($education['institution']))<span>{{ $education['institution'] }}</span>@endif
                                                @if (! empty($education['year']) || ! empty($education['grade']))
                                                    <span class="text-sm muted">{{ collect([$education['year'] ?? null, $education['grade'] ?? null])->filter()->join(' · ') }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </section>
                            @endif

                            @if (! empty($profile['experience']))
                                <section class="career-snapshot-block">
                                    <h3><x-icon name="briefcase" :size="16" /> Experience</h3>
                                    <ul>
                                        @foreach ($profile['experience'] as $experience)
                                            <li>
                                                <strong>{{ $experience['title'] ?? 'Experience' }}</strong>
                                                @if (! empty($experience['organization']))<span>{{ $experience['organization'] }}</span>@endif
                                                @if (! empty($experience['period']))<span class="text-sm muted">{{ $experience['period'] }}</span>@endif
                                                @if (! empty($experience['highlights']))
                                                    <ul>
                                                        @foreach ($experience['highlights'] as $highlight)
                                                            <li>{{ $highlight }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </section>
                            @endif

                            <section class="career-snapshot-block">
                                <h3><x-icon name="check-circle" :size="16" /> Skills detected</h3>
                                @if ($suggestedSkills->isEmpty())
                                    <p class="text-sm muted">No skills were detected. Add them manually from the profile editor.</p>
                                @else
                                    <div class="badge-row">
                                        @foreach ($suggestedSkills as $skill)
                                            <span class="badge {{ $existing->contains(mb_strtolower((string) $skill['name'])) ? 'badge-success' : '' }}">
                                                {{ $skill['name'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </section>

                            @if (! empty($profile['certifications']))
                                <section class="career-snapshot-block">
                                    <h3><x-icon name="award" :size="16" /> Certifications</h3>
                                    <ul>
                                        @foreach ($profile['certifications'] as $certification)
                                            <li>{{ $certification }}</li>
                                        @endforeach
                                    </ul>
                                </section>
                            @endif
                        </div>

                        <div class="career-insight-grid">
                            @if (! empty($ai['strengths']))
                                <div class="career-insight career-insight-success">
                                    <h3><x-icon name="check-circle" :size="16" /> Strengths</h3>
                                    <ul>
                                        @foreach ($ai['strengths'] as $strength)
                                            <li>{{ $strength }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (! empty($ai['gaps']))
                                <div class="career-insight career-insight-warning">
                                    <h3><x-icon name="alert" :size="16" /> Gaps to work on</h3>
                                    <ul>
                                        @foreach ($ai['gaps'] as $gap)
                                            <li>{{ $gap }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (! empty($ai['ats_notes']))
                                <div class="career-insight career-insight-info">
                                    <h3><x-icon name="file" :size="16" /> Résumé tips</h3>
                                    <ul>
                                        @foreach ($ai['ats_notes'] as $note)
                                            <li>{{ $note }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="panel" id="jobs" style="margin-top:var(--space-8);">
                        <div class="between" style="margin-bottom:var(--space-4);">
                            <div>
                                <h2 style="margin-bottom:var(--space-1);">Best-fit jobs</h2>
                                <p class="text-sm muted" style="margin:0;">Open vacancies ranked by your skills, qualification, district and posting date.</p>
                            </div>
                            <x-button variant="ghost" size="sm" :href="route('home') . '#job-explorer'">Explore all</x-button>
                        </div>

                        @if ($analysis->jobMatches->isEmpty())
                            <x-empty-state icon="search" title="No open matches yet" message="Add a few skills to your profile or widen your target role, then analyse again." />
                        @else
                            <div class="jobs-grid">
                                @foreach ($analysis->jobMatches as $match)
                                    <article class="job-card card is-interactive">
                                        <div class="card-body">
                                            <div class="job-card-top">
                                                <h3 class="job-card-title"><a href="{{ route('jobs.show', $match->job) }}">{{ $match->job->title }}</a></h3>
                                                <span class="badge badge-info">{{ $match->score }}% match</span>
                                            </div>
                                            <p class="job-card-dept">{{ $match->job->department }}</p>

                                            <ul class="meta-list">
                                                <li><x-icon name="map-pin" :size="16" /> {{ $match->job->district->name ?? $match->job->location }}</li>
                                                @if ($match->job->employment_type)
                                                    <li><x-icon name="briefcase" :size="16" /> {{ $match->job->employment_type }}</li>
                                                @endif
                                                @if ($match->job->last_date)
                                                    <li><x-icon name="calendar" :size="16" /> Apply by {{ $match->job->last_date->format('d M Y') }}</li>
                                                @endif
                                            </ul>

                                            @if ($match->reason)
                                                <p class="text-sm career-match-reason">{{ $match->reason }}</p>
                                            @endif

                                            @if (! empty($match->matched_skills))
                                                <div class="badge-row">
                                                    <span class="text-sm muted">You have:</span>
                                                    @foreach (array_slice($match->matched_skills, 0, 4) as $skill)
                                                        <span class="badge badge-success">{{ $skill }}</span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if (! empty($match->missing_skills))
                                                <div class="badge-row">
                                                    <span class="text-sm muted">To build:</span>
                                                    @foreach (array_slice($match->missing_skills, 0, 4) as $skill)
                                                        <span class="badge badge-warning">{{ $skill }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-foot">
                                            <x-button variant="outline" size="sm" :href="route('jobs.show', $match->job)">View details</x-button>
                                            <x-interested-button :job="$match->job" :saved="in_array($match->job->getKey(), $savedJobIds, true)" />
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="panel" id="plan" style="margin-top:var(--space-8);">
                        <div class="between" style="margin-bottom:var(--space-4);">
                            <div>
                                <h2 style="margin-bottom:var(--space-1);">Skill upgradation plan</h2>
                                <p class="text-sm muted" style="margin:0;">A focused route from where you are to the skills your matched roles ask for.</p>
                            </div>
                            <span class="badge"><x-icon name="calendar" :size="13" /> 30 / 60 / 90 days</span>
                        </div>

                        @if ($analysis->skillPlans->isEmpty())
                            <x-empty-state icon="check-circle" title="Your skills already cover the matched roles" message="Keep your profile current and add projects or certifications that show your work." />
                        @else
                            <div class="career-plan-grid">
                                @foreach ($analysis->skillPlans as $plan)
                                    <article class="career-plan-card">
                                        <div class="between">
                                            <span class="badge {{ $plan->priority === 'high' ? 'badge-danger' : ($plan->priority === 'medium' ? 'badge-warning' : 'badge-info') }}">
                                                {{ ucfirst($plan->priority) }} priority
                                            </span>
                                            <span class="badge {{ $plan->status === 'completed' ? 'badge-success' : ($plan->status === 'in_progress' ? 'badge-info' : '') }}">
                                                {{ $plan->statusLabel() }}
                                            </span>
                                        </div>

                                        <h3>{{ $plan->skill_name }}</h3>
                                        <span class="badge badge-info">{{ $plan->roadmap_days }}-day focus</span>
                                        @if ($plan->why)
                                            <p class="text-sm muted">{{ $plan->why }}</p>
                                        @endif

                                        <p class="career-plan-time"><x-icon name="calendar" :size="14" /> About {{ $plan->estimated_weeks }} {{ Str::plural('week', $plan->estimated_weeks) }}</p>

                                        @if (! empty($plan->actions))
                                            <ol class="career-plan-actions">
                                                @foreach ($plan->actions as $action)
                                                    <li>{{ $action }}</li>
                                                @endforeach
                                            </ol>
                                        @endif

                                        @if (! empty($plan->resources))
                                            <div class="career-plan-resources">
                                                <span class="text-sm muted">Start learning</span>
                                                @foreach ($plan->resources as $resource)
                                                    @if (! empty($resource['url']))
                                                        <a href="{{ $resource['url'] }}" target="_blank" rel="noopener noreferrer">
                                                            <x-icon name="external" :size="14" /> {{ $resource['title'] ?? $resource['provider'] ?? 'Resource' }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($plan->project)
                                            <div class="career-plan-note">
                                                <strong>Practice project</strong>
                                                <p>{{ $plan->project }}</p>
                                            </div>
                                        @endif

                                        @if ($plan->proof)
                                            <div class="career-plan-note">
                                                <strong>Proof of skill</strong>
                                                <p>{{ $plan->proof }}</p>
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('candidate.career.plan.update', $plan) }}" class="career-plan-form">
                                            @csrf
                                            @method('PATCH')

                                            @if ($plan->status === \App\Models\CandidateSkillPlan::STATUS_NOT_STARTED)
                                                <input type="hidden" name="status" value="{{ \App\Models\CandidateSkillPlan::STATUS_IN_PROGRESS }}">
                                                <x-button variant="outline" size="sm" type="submit">Start learning</x-button>
                                            @elseif ($plan->status === \App\Models\CandidateSkillPlan::STATUS_IN_PROGRESS)
                                                <input type="hidden" name="status" value="{{ \App\Models\CandidateSkillPlan::STATUS_COMPLETED }}">
                                                <label class="checkbox-field text-sm">
                                                    <input type="checkbox" name="add_to_profile" value="1">
                                                    <span>Add {{ $plan->skill_name }} to my profile</span>
                                                </label>
                                                <x-button variant="primary" size="sm" type="submit">Mark complete</x-button>
                                            @else
                                                <span class="badge badge-success"><x-icon name="check" :size="13" /> Completed</span>
                                            @endif
                                        </form>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <aside class="career-result-aside">
                    <div class="panel">
                        <h3>At a glance</h3>
                        <div class="career-stat-list">
                            <div>
                                <span class="career-stat-value">{{ $topMatch?->score ?? 0 }}%</span>
                                <span class="text-sm muted">Best match</span>
                            </div>
                            <div>
                                <span class="career-stat-value">{{ $planCount }}</span>
                                <span class="text-sm muted">Skills to build</span>
                            </div>
                            <div>
                                <span class="career-stat-value">{{ $highPriority }}</span>
                                <span class="text-sm muted">High priority</span>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <h3>Next steps</h3>
                        <div class="career-side-actions">
                            <x-button variant="primary" block data-modal-open="apply-profile">
                                <x-icon name="edit" :size="16" /> Review profile suggestions
                            </x-button>
                            <x-button variant="outline" block :href="route('candidate.career.index') . '#analyse'">
                                <x-icon name="refresh" :size="16" /> Analyse a newer résumé
                            </x-button>
                            <form method="POST" action="{{ route('candidate.career.destroy', $analysis) }}" data-confirm="Delete this analysis and its skill plan? This cannot be undone.">
                                @csrf
                                @method('DELETE')
                                <x-button variant="ghost" block type="submit">
                                    <x-icon name="trash" :size="16" /> Delete this analysis
                                </x-button>
                            </form>
                        </div>
                    </div>

                    <div class="panel career-disclaimer">
                        <h3><x-icon name="info" :size="16" /> A note on AI</h3>
                        <p class="text-sm">Suggestions are generated from your résumé and the vacancies currently listed. They are guidance, not an eligibility decision. Always read the official notification before applying.</p>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <x-modal name="apply-profile" title="Update your profile">
        <form method="POST" action="{{ route('candidate.career.apply', $analysis) }}" id="career-apply-form">
            @csrf

            <p class="text-sm muted">Select the skills you want to add. Existing skills are marked and will not be removed.</p>

            @if ($suggestedSkills->isEmpty())
                <p class="text-sm">No skills were detected in this résumé. You can add them from the profile editor.</p>
            @else
                <div class="career-skill-picker">
                    @foreach ($suggestedSkills as $skill)
                        @php($alreadyAdded = $existing->contains(mb_strtolower((string) $skill['name'])))
                        <label class="checkbox-field">
                            <input type="checkbox" name="skills[]" value="{{ $skill['name'] }}" @checked(! $alreadyAdded) @disabled($alreadyAdded)>
                            <span>
                                <strong>{{ $skill['name'] }}</strong>
                                @if ($alreadyAdded)
                                    <span class="badge badge-success">On profile</span>
                                @elseif (! empty($skill['evidence']))
                                    <span class="text-sm muted">{{ $skill['evidence'] }}</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif

            @if (! empty($profile['qualification']))
                <div class="form-field" style="margin-top:var(--space-4);">
                    <label class="form-label" for="career-qualification">Highest qualification</label>
                    <input class="form-control" id="career-qualification" name="qualification" value="{{ old('qualification', $profile['qualification']) }}" maxlength="255">
                    <p class="form-hint">Update this only if the résumé shows a higher or corrected qualification.</p>
                </div>
            @endif
        </form>

        <x-slot:footer>
            <x-button variant="ghost" type="button" data-modal-close>Cancel</x-button>
            <x-button variant="primary" type="submit" form="career-apply-form">Add selected to profile</x-button>
        </x-slot:footer>
    </x-modal>
@endsection
