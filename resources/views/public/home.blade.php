@extends('layouts.public')

@section('title', 'GYRS — Gujarat Youth Rojgar Setu | Job Portal')
@section('meta_description', 'Explore verified public and private vacancies across all 33 districts. Free candidate registration and application tracking.')

@php
    $keyword = $filters['keyword'] ?? '';
    $activeType = $filters['type'] ?? '';
    $activeDistrict = (string) ($filters['district'] ?? '');
    $typeLabel = fn ($type) => is_object($type) ? ($type->name ?? $type->label ?? $type->value ?? '') : (string) $type;
    $typeValue = fn ($type) => is_object($type) ? (string) ($type->value ?? $type->slug ?? $type->name ?? '') : (string) $type;
    $districtValue = fn ($district) => is_object($district) ? (string) ($district->slug ?? $district->id ?? '') : (string) $district;
    $districtLabel = fn ($district) => is_object($district) ? (string) ($district->name ?? $district->title ?? '') : (string) $district;

    $authUser = auth()->user();
    $savedJobIds = $authUser && $authUser->isCandidate()
        ? $authUser->savedJobs()->pluck('job_id')->all()
        : [];
@endphp

@section('content')
    <div class="ticker" aria-label="State recruitment alerts">
        <div class="container">
            <span class="ticker-label"><x-icon name="sparkles" :size="15" /> State live alerts</span>
            <div class="ticker-viewport">
                <div class="ticker-track">
                    <span>GPSC Class 1 &amp; 2 recruitment open — 320+ executive and administrative posts.</span>
                    <span>Gujarat mega Rozgar Mela in Ahmedabad — 5,000+ on-the-spot offer letters.</span>
                    <span>Mukhyamantri Apprentice Yojana — monthly stipend up to ₹9,000 for technical graduates.</span>
                    <span>Open any job, read the full notification, then click Apply now at the end.</span>
                    <span aria-hidden="true">GPSC Class 1 &amp; 2 recruitment open — 320+ executive and administrative posts.</span>
                    <span aria-hidden="true">Gujarat mega Rozgar Mela in Ahmedabad — 5,000+ on-the-spot offer letters.</span>
                    <span aria-hidden="true">Mukhyamantri Apprentice Yojana — monthly stipend up to ₹9,000 for technical graduates.</span>
                    <span aria-hidden="true">Open any job, read the full notification, then click Apply now at the end.</span>
                </div>
            </div>
        </div>
    </div>

    <section class="hero">
        <span class="hero-glow hero-glow-a" aria-hidden="true"></span>
        <span class="hero-glow hero-glow-b" aria-hidden="true"></span>

        <div class="container">
            <div class="hero-inner">
                <div class="hero-content" data-motion-hero>
                    <div class="hero-badge">
                        <span class="hero-badge-pulse" aria-hidden="true"></span>
                        <span>Statewide Public &amp; Private Vacancies</span>
                    </div>

                    <h1 class="hero-title">
                        <span class="hero-line"><span class="hero-line-inner" data-motion-line>Find your next</span></span>
                        <span class="hero-line hero-line-keyword">
                            <span class="rotator hero-rotator" data-rotator data-rotator-interval="2600" data-motion-line>
                                <span class="rotator-item is-active" data-rotator-item>career</span>
                                <span class="rotator-item" data-rotator-item>opportunity</span>
                                <span class="rotator-item" data-rotator-item>role</span>
                                <span class="rotator-item" data-rotator-item>future</span>
                            </span>
                        </span>
                        <span class="hero-line"><span class="hero-line-inner" data-motion-line>in Gujarat</span></span>
                    </h1>

                    <p class="hero-sub">
                        Explore verified state government positions, public department vacancies and leading private sector roles
                        across all 33 districts — free for every candidate.
                    </p>

                    <form class="search-panel" method="GET" action="{{ route('home') }}" role="search" data-loading-form>
                        <div class="search-seg">
                            <x-icon name="search" />
                            <label class="sr-only" for="search-keyword">Keyword</label>
                            <input class="form-control" type="search" id="search-keyword" name="keyword" value="{{ $keyword }}" placeholder="Skills, job title or department" autocomplete="off">
                        </div>

                        <div class="search-seg">
                            <x-icon name="briefcase" />
                            <label class="sr-only" for="search-type">Employment type</label>
                            <select class="form-select" id="search-type" name="type">
                                <option value="">All employment types</option>
                                @foreach ($employmentTypes as $type)
                                    <option value="{{ $typeValue($type) }}" @selected($activeType === $typeValue($type))>{{ $typeLabel($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="search-seg">
                            <x-icon name="map-pin" />
                            <label class="sr-only" for="search-district">District</label>
                            <select class="form-select" id="search-district" name="district">
                                <option value="">All 33 districts</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $districtValue($district) }}" @selected($activeDistrict === $districtValue($district))>{{ $districtLabel($district) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-outline"><x-icon name="search" :size="18" /> Search jobs</button>
                    </form>

                    <div class="chip-row">
                        <span class="text-sm muted" style="align-self:center;">Popular searches:</span>
                        @foreach (['Software Engineer', 'Civil Engineer', 'Staff Nurse', 'Data Entry', 'Apprenticeship'] as $term)
                            <a class="chip" href="{{ route('home', ['keyword' => $term]) }}">{{ $term }}</a>
                        @endforeach
                    </div>

                    <div class="hero-trust">
                        <span class="hero-trust-item"><x-icon name="check" :size="14" /> 33 districts statewide</span>
                        <span class="hero-trust-item"><x-icon name="shield" :size="14" /> 100% free registration</span>
                        <span class="hero-trust-item"><x-icon name="sparkles" :size="14" /> Verified official postings</span>
                    </div>
                </div>

                <div class="hero-visual" data-motion-visual aria-hidden="true">
                    <div class="hero-art-stage">
                        <svg class="hero-art-svg hero-opportunity-art" viewBox="0 0 560 460" width="560" height="460" aria-hidden="true" focusable="false">
                            <defs>
                                <radialGradient id="hero-map-wash" cx="52%" cy="44%" r="58%">
                                    <stop offset="0" stop-color="var(--color-focus)" stop-opacity=".15" />
                                    <stop offset="1" stop-color="var(--color-focus)" stop-opacity="0" />
                                </radialGradient>
                                <linearGradient id="hero-route" x1="0" y1="1" x2="1" y2="0">
                                    <stop offset="0" stop-color="var(--color-muted)" stop-opacity=".35" />
                                    <stop offset="1" stop-color="var(--color-heading)" stop-opacity=".8" />
                                </linearGradient>
                                <filter id="hero-art-shadow" x="-20%" y="-20%" width="140%" height="150%">
                                    <feDropShadow dx="0" dy="12" stdDeviation="14" flood-color="var(--color-heading)" flood-opacity=".13" />
                                </filter>
                                <pattern id="hero-map-dots" width="18" height="18" patternUnits="userSpaceOnUse">
                                    <circle cx="2" cy="2" r="1" fill="var(--color-border-strong)" opacity=".55" />
                                </pattern>
                            </defs>

                            <circle cx="300" cy="220" r="220" fill="url(#hero-map-wash)" />
                            <circle cx="300" cy="220" r="190" fill="url(#hero-map-dots)" opacity=".48" />

                            <!-- A winding route links districts to the opportunity card. -->
                            <path d="M55 352C108 336 116 293 153 282S195 304 218 267 219 215 251 204" fill="none" stroke="var(--color-border-strong)" stroke-width="2" stroke-dasharray="4 7" />
                            <path class="hero-art-route" d="M55 352C108 336 116 293 153 282S195 304 218 267 219 215 251 204" fill="none" stroke="url(#hero-route)" stroke-width="3" stroke-linecap="round" stroke-dasharray="12 9" />

                            <!-- Abstract district network keeps the location story illustrative, not geographic. -->
                            <g class="hero-art-map" fill="none" stroke="var(--color-border-strong)" stroke-width="1.2" opacity=".7">
                                <path d="M76 98c24-16 41-12 57-25l31 8 15 24 32 5 14 28-16 20 8 26-27 17-4 30-28 9-18 25-31-9-22 15-28-14-21 5-14-25-22-10 8-28-15-22 15-23-3-29 24-17z" />
                                <path d="m96 102 12 39-20 36 32 26-8 43m27-174-9 40 30 22-18 32 27 31m40-105-37 20 19 30-20 22m-82-3 39-9 23 21 29-7m-81 47 43-12 18-23 31-4" />
                            </g>
                            <g class="hero-art-map-districts" fill="var(--color-surface)" stroke="var(--color-heading)" stroke-width="2">
                                <circle cx="98" cy="164" r="5" /><circle cx="152" cy="120" r="5" /><circle cx="190" cy="194" r="5" />
                                <circle cx="124" cy="231" r="5" /><circle cx="211" cy="145" r="5" />
                            </g>
                            <g class="hero-art-map-pin" transform="translate(184 101)">
                                <path d="M16 0C7 0 0 7 0 16c0 12 16 29 16 29s16-17 16-29C32 7 25 0 16 0Z" fill="var(--color-heading)" />
                                <circle cx="16" cy="16" r="5" fill="var(--color-surface)" />
                            </g>

                            <!-- Opportunity finder panel -->
                            <g class="hero-art-panel" filter="url(#hero-art-shadow)">
                                <rect x="222" y="92" width="300" height="266" rx="20" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="1.3" />
                                <path d="M242 92h260a20 20 0 0 1 20 20v34H222v-34a20 20 0 0 1 20-20Z" fill="var(--color-surface-raised)" />
                                <circle cx="244" cy="119" r="4" fill="var(--color-border-strong)" />
                                <circle cx="259" cy="119" r="4" fill="var(--color-border-strong)" />
                                <circle cx="274" cy="119" r="4" fill="var(--color-border-strong)" />
                                <path d="M239 168h92" stroke="var(--color-border)" stroke-width="7" stroke-linecap="round" />
                                <path d="M239 184h148" stroke="var(--color-border-strong)" stroke-width="3" stroke-linecap="round" />
                                <text x="239" y="211" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif" font-size="13" font-weight="700">Explore opportunities</text>
                                <text x="239" y="230" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif" font-size="10">Across Gujarat, in one place</text>
                                <rect x="239" y="249" width="266" height="48" rx="10" fill="var(--color-surface-raised)" stroke="var(--color-border)" />
                                <circle cx="260" cy="273" r="10" fill="var(--color-subtle)" />
                                <path d="M256 273h8m-4-4v8" stroke="var(--color-heading)" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M279 267h104" stroke="var(--color-border-strong)" stroke-width="4" stroke-linecap="round" />
                                <path d="M279 279h72" stroke="var(--color-border)" stroke-width="3" stroke-linecap="round" />
                                <rect x="239" y="307" width="266" height="34" rx="9" fill="var(--color-subtle)" />
                                <circle cx="257" cy="324" r="6" fill="none" stroke="var(--color-heading)" stroke-width="1.5" />
                                <path d="M257 320v4l3 2" fill="none" stroke="var(--color-heading)" stroke-width="1.3" stroke-linecap="round" />
                                <path d="M271 324h81" stroke="var(--color-border-strong)" stroke-width="3" stroke-linecap="round" />
                            </g>

                            <!-- Floating location and access cues -->
                            <g class="hero-art-district-tag" filter="url(#hero-art-shadow)">
                                <rect x="28" y="276" width="142" height="48" rx="14" fill="var(--color-surface)" stroke="var(--color-border)" />
                                <circle cx="52" cy="300" r="12" fill="var(--color-subtle)" />
                                <path d="M52 307s6-5 6-10a6 6 0 1 0-12 0c0 5 6 10 6 10Zm0-8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" fill="none" stroke="var(--color-heading)" stroke-width="1.4" />
                                <text x="72" y="298" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif" font-size="10" font-weight="700">33 districts</text>
                                <text x="72" y="312" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif" font-size="8.5">Search close to home</text>
                            </g>
                            <g class="hero-art-free-tag" filter="url(#hero-art-shadow)">
                                <rect x="342" y="378" width="168" height="48" rx="14" fill="var(--color-surface)" stroke="var(--color-border)" />
                                <circle cx="367" cy="402" r="12" fill="var(--color-success-soft)" />
                                <path d="m362 402 3 3 6-7" fill="none" stroke="var(--color-success)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                <text x="387" y="400" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif" font-size="10" font-weight="700">Free to get started</text>
                                <text x="387" y="414" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif" font-size="8.5">Make your next move</text>
                            </g>

                            <g class="hero-art-spark" fill="var(--color-focus)">
                                <path d="m477 52 4 12 12 4-12 4-4 12-4-12-12-4 12-4z" />
                                <circle cx="204" cy="382" r="3" />
                                <circle cx="520" cy="228" r="3" />
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        @if ($districts->isNotEmpty())
            <div class="hero-districts" aria-label="Districts covered on GYRS">
                <div class="hero-districts-track">
                    @foreach ($districts as $district)
                        @php $districtCount = (int) ($districtJobCounts[$district->id] ?? 0); @endphp
                        <a href="{{ route('home', ['district' => $districtValue($district)]) }}#job-explorer"
                           class="hero-district @if($activeDistrict === $districtValue($district)) is-active @endif"
                           data-district-jobs="{{ $districtCount }}">
                            {{ $districtLabel($district) }}<span class="sr-only"> — {{ $districtCount }} {{ $districtCount === 1 ? 'open job' : 'open jobs' }}</span>
                        </a>
                    @endforeach
                    @foreach ($districts as $district)
                        @php $districtCount = (int) ($districtJobCounts[$district->id] ?? 0); @endphp
                        <a href="{{ route('home', ['district' => $districtValue($district)]) }}#job-explorer"
                           class="hero-district @if($activeDistrict === $districtValue($district)) is-active @endif"
                           data-district-jobs="{{ $districtCount }}"
                           aria-hidden="true"
                           tabindex="-1">
                            {{ $districtLabel($district) }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <div class="metric-strip">
        <div class="container">
            <div class="grid grid-4" data-motion-reveal data-motion-stagger>
                <div class="metric">
                    <div class="metric-num" data-count-to="{{ (int) $jobCount }}">{{ number_format((int) $jobCount) }}</div>
                    <div class="metric-label">Open vacancies on GYRS</div>
                </div>
                <div class="metric">
                    <div class="metric-num" data-count-to="33">33</div>
                    <div class="metric-label">Districts covered statewide</div>
                </div>
                <div class="metric">
                    <div class="metric-num" data-count-to="100" data-count-suffix="%">100%</div>
                    <div class="metric-label">Free candidate registration</div>
                </div>
                <div class="metric">
                    <div class="metric-num">AI</div>
                    <div class="metric-label">Matched recommendations</div>
                </div>
            </div>
        </div>
    </div>

    <section class="section" id="job-explorer">
        <div class="container">
            <div class="page-head">
                <div>
                    <h2 style="text-wrap:balance;">Explore open jobs in Gujarat</h2>
                    <p class="lead" style="text-wrap:balance;">Verified state government posts, public department vacancies and private industrial roles.</p>
                </div>
                <p class="result-count">Showing <strong>{{ $jobs->total() }}</strong> matching {{ Str::plural('opportunity', $jobs->total()) }}</p>
            </div>

            @php
                $activeFilterCount = ($keyword ? 1 : 0) + ($activeDistrict ? 1 : 0) + ($activeType ? 1 : 0);
            @endphp

            @if ($activeFilterCount > 0)
                <div class="active-filters-bar" aria-label="Current active search filters">
                    <span class="active-filters-label"><x-icon name="filter" :size="14" /> Active filters:</span>
                    <div class="active-filters-list">
                        @if ($keyword)
                            <a href="{{ route('home', array_filter(['district' => $activeDistrict, 'type' => $activeType])) }}#job-explorer" class="active-filter-chip">
                                <span>Keyword: <strong>"{{ $keyword }}"</strong></span>
                                <span class="active-filter-remove" aria-hidden="true">&times;</span>
                                <span class="sr-only">Remove keyword filter</span>
                            </a>
                        @endif
                        @if ($activeDistrict)
                            @php
                                $matchedDistrict = $districts->first(fn($d) => $districtValue($d) === $activeDistrict);
                            @endphp
                            <a href="{{ route('home', array_filter(['keyword' => $keyword, 'type' => $activeType])) }}#job-explorer" class="active-filter-chip">
                                <span>District: <strong>{{ $matchedDistrict ? $districtLabel($matchedDistrict) : $activeDistrict }}</strong></span>
                                <span class="active-filter-remove" aria-hidden="true">&times;</span>
                                <span class="sr-only">Remove district filter</span>
                            </a>
                        @endif
                        @if ($activeType)
                            @php
                                $matchedType = collect($employmentTypes)->first(fn($t) => $typeValue($t) === $activeType);
                            @endphp
                            <a href="{{ route('home', array_filter(['keyword' => $keyword, 'district' => $activeDistrict])) }}#job-explorer" class="active-filter-chip">
                                <span>Type: <strong>{{ $matchedType ? $typeLabel($matchedType) : $activeType }}</strong></span>
                                <span class="active-filter-remove" aria-hidden="true">&times;</span>
                                <span class="sr-only">Remove employment type filter</span>
                            </a>
                        @endif
                        <a href="{{ route('home') }}#job-explorer" class="active-filter-clear">Clear all</a>
                    </div>
                </div>
            @endif

            <div class="mobile-filter-bar">
                <button type="button" class="btn btn-outline btn-sm mobile-filter-toggle" data-filter-toggle aria-expanded="false" aria-controls="sidebar-filters">
                    <x-icon name="filter" :size="15" />
                    <span>Filter opportunities</span>
                    @if ($activeFilterCount > 0)
                        <span class="badge badge-accent" style="margin-left:4px;">{{ $activeFilterCount }}</span>
                    @endif
                </button>
                @if ($activeFilterCount > 0)
                    <a href="{{ route('home') }}#job-explorer" class="btn btn-ghost btn-sm">Clear filters</a>
                @endif
            </div>

            <div class="split">
                <div class="split-main">
                    @if ($jobs->isEmpty())
                        <x-empty-state
                            icon="search"
                            title="No vacancies match your search"
                            message="Try a different keyword, employment type or district — or clear your filters to browse every open vacancy."
                        >
                            <x-slot:action>
                                <x-button variant="outline" :href="route('home')">Clear filters</x-button>
                            </x-slot:action>
                        </x-empty-state>
                    @else
                        <div class="jobs-grid" data-motion-reveal data-motion-stagger>
                            @foreach ($jobs as $job)
                                <x-job-card :job="$job" :saved="in_array($job->getKey(), $savedJobIds ?? [], true)" />
                            @endforeach
                        </div>

                        <x-pagination :paginator="$jobs" />
                    @endif
                </div>

                <aside class="filter-panel" id="sidebar-filters" aria-label="Filter vacancies">
                    <div class="between" style="margin-bottom:var(--space-3);">
                        <h2 style="margin:0;"><x-icon name="filter" :size="16" /> Filters</h2>
                        <a class="filter-reset" href="{{ route('home') }}">Clear all</a>
                    </div>

                    <form method="GET" action="{{ route('home') }}" data-loading-form>
                        @if ($keyword)
                            <input type="hidden" name="keyword" value="{{ $keyword }}">
                        @endif
                        <div class="filter-group">
                            <label class="form-label" for="sidebar-district">District</label>
                            <select class="form-select" id="sidebar-district" name="district">
                                <option value="">All districts</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $districtValue($district) }}" @selected($activeDistrict === $districtValue($district))>{{ $districtLabel($district) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="form-label" for="sidebar-type">Employment type</label>
                            <select class="form-select" id="sidebar-type" name="type">
                                <option value="">All types</option>
                                @foreach ($employmentTypes as $type)
                                    <option value="{{ $typeValue($type) }}" @selected($activeType === $typeValue($type))>{{ $typeLabel($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-button variant="outline" block type="submit">Apply filters</x-button>
                    </form>

                    <div class="panel" style="margin-top:var(--space-4);">
                        <h2 style="font-size:.9375rem;"><x-icon name="shield" :size="15" /> Trust &amp; compliance</h2>
                        <ul class="dot-list text-sm" style="margin:0;">
                            <li>100% free candidate registration</li>
                            <li>Zero intermediaries or agents</li>
                            <li>Verified official postings</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="section how-section" id="how-it-works">
        <span class="how-glow" aria-hidden="true"></span>
        <div class="container">
            <div class="how-head">
                <span class="how-eyebrow"><x-icon name="sparkles" :size="14" /> Your route to a role</span>
                <h2>How Gujarat Rojgar Setu works</h2>
                <p class="lead">A clear four-step path from registration to a verified placement — no agents, no fees, no guesswork.</p>
            </div>

            <div class="steps-track" data-motion-steps>
                <span class="steps-line" aria-hidden="true"><span class="steps-line-fill" data-motion-steps-fill></span></span>
                <div class="steps-grid">
                    <div class="step-card" data-motion-step data-step="01">
                        <span class="step-num">01</span>
                        <div class="step-top">
                            <span class="step-icon"><x-icon name="user" :size="20" /></span>
                            <span class="step-kicker">Profile</span>
                        </div>
                        <h3>Create your free profile</h3>
                        <p>Register with your qualification, skills and district. Complete your candidate dossier in minutes.</p>
                        <span class="step-outcome"><x-icon name="check-circle" :size="14" /> Profile ready in minutes</span>
                    </div>
                    <div class="step-card" data-motion-step data-step="02">
                        <span class="step-num">02</span>
                        <div class="step-top">
                            <span class="step-icon"><x-icon name="search" :size="20" /></span>
                            <span class="step-kicker">Explore</span>
                        </div>
                        <h3>Explore &amp; read details</h3>
                        <p>Filter verified openings across all 33 districts and open any job to read the full official notification.</p>
                        <span class="step-outcome"><x-icon name="check-circle" :size="14" /> Full notification access</span>
                    </div>
                    <div class="step-card" data-motion-step data-step="03">
                        <span class="step-num">03</span>
                        <div class="step-top">
                            <span class="step-icon"><x-icon name="sparkles" :size="20" /></span>
                            <span class="step-kicker">Shortlist</span>
                        </div>
                        <h3>Save &amp; shortlist interests</h3>
                        <p>Mark any vacancy as interested, review AI-matched recommendations and build a personal shortlist of roles.</p>
                        <span class="step-outcome"><x-icon name="check-circle" :size="14" /> Personal shortlist built</span>
                    </div>
                    <div class="step-card" data-motion-step data-step="04">
                        <span class="step-num">04</span>
                        <div class="step-top">
                            <span class="step-icon"><x-icon name="award" :size="20" /></span>
                            <span class="step-kicker">Placed</span>
                        </div>
                        <h3>Attend &amp; get placed</h3>
                        <p>Attend interview calls or state Rozgar Melas and receive your official verified appointment.</p>
                        <span class="step-outcome"><x-icon name="check-circle" :size="14" /> Verified appointment</span>
                    </div>
                </div>
            </div>

            <div class="how-cta">
                <span class="how-cta-rings" aria-hidden="true"></span>
                <div class="how-cta-copy">
                    <span class="how-cta-eyebrow"><x-icon name="sparkles" :size="14" /> Ready when you are</span>
                    <h2>Turn your shortlist into a placement.</h2>
                    <p>Sign in to your candidate dashboard to view interested jobs, AI recommendations and upcoming Rozgar Melas — or create your free profile in minutes.</p>
                </div>
                <div class="how-cta-actions">
                    <div class="cluster">
                        <x-button variant="outline" :href="route('candidate.login')">Candidate sign in</x-button>
                        <x-button variant="ghost" :href="route('candidate.register')">Register free</x-button>
                    </div>
                    <span class="how-cta-note"><x-icon name="shield" :size="14" /> Free · Verified postings · No intermediaries</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section dept-section" id="departments">
        <div class="container">
            <div class="how-head dept-head">
                <span class="how-eyebrow"><x-icon name="building" :size="14" /> Verified hiring partners</span>
                <h2>Top hiring departments &amp; organizations</h2>
                <p class="lead">Partner government boards, state public sector undertakings and major industrial projects recruiting through GYRS.</p>
            </div>

            <div class="dept-grid" data-motion-reveal data-motion-stagger>
                @foreach ([
                    ['Roads & Buildings Department (R&B)', 'State department', 'Gandhinagar', 'Roads & Buildings', 'R&B'],
                    ['GIFT City Authority', 'Authority', 'Gandhinagar International Tech City', 'GIFT City', 'GIFT'],
                    ['GETCO — Gujarat Energy Transmission', 'State power utility', 'Vadodara', 'GETCO', 'GETCO'],
                    ['Health & Family Welfare Department', 'State department', 'Surat & Rajkot', 'Health', 'HFW'],
                    ['Dholera Special Investment Region', 'Industrial region', 'Dholera', 'Dholera', 'DSIR'],
                    ['Gujarat Informatics Limited (GIL)', 'State undertaking', 'Department of Science & Technology', 'Gujarat Informatics', 'GIL'],
                ] as [$deptName, $deptType, $deptLocation, $deptQuery, $deptMark])
                    <a class="dept-card card is-interactive" href="{{ route('home', ['keyword' => $deptQuery]) }}#job-explorer">
                        <div class="dept-top">
                            <span class="dept-mark" aria-hidden="true">{{ $deptMark }}</span>
                            <span class="dept-type">{{ $deptType }}</span>
                        </div>
                        <h3>{{ $deptName }}</h3>
                        <span class="dept-meta"><x-icon name="map-pin" :size="14" /> {{ $deptLocation }}</span>
                        <span class="dept-action-link">
                            <span>View openings</span>
                            <span class="dept-arrow"><x-icon name="arrow-right" :size="14" /></span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" id="faqs" style="background:var(--color-surface);border-top:1px solid var(--color-border);">
        <div class="container" style="max-width:860px;">
            <div class="page-head" style="justify-content:center;text-align:center;">
                <div>
                    <h2>Frequently asked questions</h2>
                    <p class="lead">Answers to the most common candidate questions.</p>
                </div>
            </div>

            <div class="faq-list" data-motion-reveal data-motion-stagger>
                <details open>
                    <summary>How do I apply for official state vacancies through GYRS?</summary>
                    <div class="faq-answer">
                        Open any job card to read its full details — eligibility, vacancy breakup, the official notification and step-by-step
                        instructions. The outline <strong>Apply now</strong> action records the application in your candidate dossier, and any
                        external official portal link is clearly labelled.
                    </div>
                </details>
                <details>
                    <summary>How do I keep track of the jobs I am interested in?</summary>
                    <div class="faq-answer">
                        Sign in and choose <strong>Mark interested</strong> on any vacancy. Your candidate dashboard keeps a simple directory
                        of interested jobs, alongside AI-matched recommendations and suggested skills to learn next.
                    </div>
                </details>
                <details>
                    <summary>Are there any fees for registration or applications?</summary>
                    <div class="faq-answer">
                        No. GYRS is a free employment initiative. Candidate
                        registration, job filtering and application tracking are completely free of charge.
                    </div>
                </details>
                <details>
                    <summary>What is the Mukhyamantri Apprentice Yojana stipend?</summary>
                    <div class="faq-answer">
                        Under the scheme, technical diploma holders and graduates receive a monthly stipend of up to ₹9,000 during
                        on-the-job industrial apprenticeship programmes across Gujarat.
                    </div>
                </details>
            </div>
        </div>
    </section>
@endsection
