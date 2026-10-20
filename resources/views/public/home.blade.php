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
                        <svg class="hero-art-svg" viewBox="0 0 540 480" width="540" height="480" aria-hidden="true" focusable="false">
                            <defs>
                                <!-- Summit Ambient Glow -->
                                <radialGradient id="hero-summit-glow" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="var(--color-focus)" stop-opacity="0.22" />
                                    <stop offset="50%" stop-color="var(--color-heading)" stop-opacity="0.05" />
                                    <stop offset="100%" stop-color="var(--color-heading)" stop-opacity="0" />
                                </radialGradient>
                                <!-- Skill Upgrade Reactor Glow -->
                                <radialGradient id="hero-reactor-glow" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="var(--color-focus)" stop-opacity="0.18" />
                                    <stop offset="60%" stop-color="var(--color-heading)" stop-opacity="0.03" />
                                    <stop offset="100%" stop-color="var(--color-heading)" stop-opacity="0" />
                                </radialGradient>
                                <!-- Energy Ascent Pipeline Gradient -->
                                <linearGradient id="hero-ascent-line" x1="0%" y1="100%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="var(--color-muted)" stop-opacity="0.3" />
                                    <stop offset="45%" stop-color="var(--color-focus)" stop-opacity="0.9" />
                                    <stop offset="100%" stop-color="var(--color-heading)" stop-opacity="0.9" />
                                </linearGradient>
                                <filter id="hero-card-shadow" x="-15%" y="-15%" width="130%" height="135%">
                                    <feDropShadow dx="0" dy="6" stdDeviation="10" flood-color="#4f504f" flood-opacity="0.09" />
                                </filter>
                            </defs>

                            <!-- Ambient Halos -->
                            <circle cx="415" cy="100" r="160" fill="url(#hero-summit-glow)" />
                            <circle cx="260" cy="235" r="130" fill="url(#hero-reactor-glow)" />

                            <!-- Background Isometric Grid Perspective -->
                            <g class="hero-grid-matrix" stroke="var(--color-border)" stroke-width="1" opacity="0.6">
                                <path d="M40 400 L260 270 L480 400" fill="none" />
                                <path d="M70 360 L260 250 L450 360" fill="none" stroke-dasharray="3 4" />
                                <path d="M100 320 L260 230 L420 320" fill="none" />
                                <path d="M130 280 L260 210 L390 280" fill="none" stroke-dasharray="3 4" />
                                <line x1="260" y1="210" x2="260" y2="420" stroke="var(--color-border-strong)" stroke-width="1.2" />
                                <line x1="140" y1="280" x2="60" y2="420" />
                                <line x1="380" y1="280" x2="460" y2="420" />
                            </g>

                            <!-- ── STAGE 01: Profile & Assessment Pedestal ── -->
                            <g class="hero-step-01">
                                <!-- Base Isometric Pedestal -->
                                <polygon points="50,375 120,340 190,375 120,410" fill="var(--color-surface)" stroke="var(--color-border-strong)" stroke-width="1.5" />
                                <polygon points="50,375 120,410 120,422 50,387" fill="var(--color-subtle)" stroke="var(--color-border)" />
                                <polygon points="120,410 190,375 190,387 120,422" fill="var(--color-border)" stroke="var(--color-border-strong)" />
                                <!-- Center Hub Dot -->
                                <circle cx="120" cy="375" r="6" fill="var(--color-heading)" />
                                <circle cx="120" cy="375" r="12" fill="none" stroke="var(--color-border-strong)" stroke-width="1.5" />
                                <text x="120" y="440" text-anchor="middle" font-size="10" font-weight="700" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif" letter-spacing="0.05em">LEVEL 01 · PROFILE</text>
                            </g>

                            <!-- ── STAGE 02: Skill Upgrade Reactor Pedestal ── -->
                            <g class="hero-step-02">
                                <!-- Elevated Isometric Pedestal -->
                                <polygon points="190,240 260,205 330,240 260,275" fill="var(--color-surface)" stroke="var(--color-border-strong)" stroke-width="1.5" />
                                <polygon points="190,240 260,275 260,290 190,255" fill="var(--color-subtle)" stroke="var(--color-border)" />
                                <polygon points="260,275 330,240 330,255 260,290" fill="var(--color-border)" stroke="var(--color-border-strong)" />

                                <!-- Gyro Orbit Ring around Skill Hub -->
                                <ellipse cx="260" cy="240" rx="44" ry="22" fill="none" stroke="var(--color-focus)" stroke-width="1.5" stroke-dasharray="6 4" class="hero-orbit-spin" />
                                <circle cx="260" cy="218" r="3.5" fill="var(--color-heading)" />

                                <!-- Center Reactor Core -->
                                <circle cx="260" cy="238" r="8" fill="var(--color-heading)" />
                                <circle cx="260" cy="238" r="16" fill="none" stroke="var(--color-focus)" stroke-width="1.2" opacity="0.6" />

                                <!-- Ascending Skill Chevrons -->
                                <path d="M255 198 L260 192 L265 198" fill="none" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M255 188 L260 182 L265 188" fill="none" stroke="var(--color-focus)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                                <text x="260" y="308" text-anchor="middle" font-size="10" font-weight="700" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif" letter-spacing="0.05em">LEVEL 02 · SKILL UPGRADE</text>
                            </g>

                            <!-- ── STAGE 03: The Summit Peak (Goal Achieved!) ── -->
                            <g class="hero-step-03">
                                <!-- Summit Pillar Pedestal -->
                                <polygon points="350,110 420,75 490,110 420,145" fill="var(--color-surface)" stroke="var(--color-heading)" stroke-width="2" />
                                <polygon points="350,110 420,145 420,162 350,127" fill="var(--color-subtle)" stroke="var(--color-border-strong)" />
                                <polygon points="420,145 490,110 490,127 420,162" fill="var(--color-border-strong)" stroke="var(--color-heading)" />

                                <!-- Summit Goal Banner & Trophy Crest -->
                                <line x1="420" y1="75" x2="420" y2="28" stroke="var(--color-heading)" stroke-width="2.5" stroke-linecap="round" />
                                <path d="M420 30 L456 42 L420 54 Z" fill="var(--color-heading)" />
                                <circle cx="420" cy="28" r="4" fill="var(--color-focus)" />

                                <!-- Celebratory Starburst Rays on Goal Summit -->
                                <g class="hero-goal-sparks">
                                    <line x1="420" y1="14" x2="420" y2="4" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                                    <line x1="432" y1="18" x2="440" y2="10" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                                    <line x1="436" y1="28" x2="446" y2="28" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                                    <line x1="408" y1="18" x2="400" y2="10" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                                    <line x1="404" y1="28" x2="394" y2="28" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                                </g>

                                <!-- Pulsing Goal Beacon Ring -->
                                <circle cx="420" cy="75" r="14" fill="none" stroke="var(--color-focus)" stroke-width="1.8" class="hero-summit-pulse" />

                                <text x="420" y="178" text-anchor="middle" font-size="10.5" font-weight="800" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif" letter-spacing="0.06em">LEVEL 03 · GOAL ACHIEVED</text>
                            </g>

                            <!-- ── Continuous Ascending Energy Trajectory ── -->
                            <path class="hero-ascent-track" d="M120 375 C160 350, 200 280, 260 238 C320 196, 360 130, 420 75" fill="none" stroke="var(--color-border-strong)" stroke-width="2" stroke-dasharray="4 4" />
                            <path class="hero-ascent-pulse" d="M120 375 C160 350, 200 280, 260 238 C320 196, 360 130, 420 75" fill="none" stroke="url(#hero-ascent-line)" stroke-width="3.5" stroke-linecap="round" />

                            <!-- Milestone Waypoint Nodes -->
                            <circle cx="120" cy="375" r="4.5" fill="var(--color-heading)" />
                            <circle cx="260" cy="238" r="5" fill="var(--color-heading)" />
                            <circle cx="420" cy="75" r="5.5" fill="var(--color-heading)" />

                            <!-- ── Floating Milestone Card 1: Skill Upgrade Engine ── -->
                            <g class="hero-float-card hero-float-card-1" transform="translate(18, 145)" filter="url(#hero-card-shadow)">
                                <rect width="186" height="66" rx="12" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="1" />
                                <circle cx="26" cy="26" r="13" fill="var(--color-subtle)" />
                                <text x="26" y="30" text-anchor="middle" font-size="13">⚡</text>
                                <text x="48" y="22" font-size="11.5" font-weight="700" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif">Skill Upgrade +42%</text>
                                <text x="48" y="37" font-size="9.5" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif">Technical &amp; Core Certifications</text>
                                <!-- Mini Readiness Progress Bar -->
                                <rect x="48" y="46" width="124" height="4" rx="2" fill="var(--color-border)" />
                                <rect x="48" y="46" width="108" height="4" rx="2" fill="var(--color-heading)" class="hero-progress-fill" />
                            </g>

                            <!-- ── Floating Milestone Card 2: Summit Goal Achieved ── -->
                            <g class="hero-float-card hero-float-card-2" transform="translate(295, 12)" filter="url(#hero-card-shadow)">
                                <rect width="210" height="64" rx="12" fill="var(--color-surface)" stroke="var(--color-heading)" stroke-width="1.5" />
                                <circle cx="28" cy="26" r="14" fill="var(--color-subtle)" />
                                <text x="28" y="31" text-anchor="middle" font-size="14">🏆</text>
                                <text x="50" y="22" font-size="12" font-weight="800" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif">Goal Achieved!</text>
                                <text x="50" y="37" font-size="9.5" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif">Verified Appointment Letter</text>
                                <g transform="translate(50, 45)">
                                    <rect width="68" height="15" rx="7.5" fill="var(--color-success-soft)" />
                                    <text x="34" y="11" text-anchor="middle" font-size="8.5" font-weight="700" fill="var(--color-success)" font-family="Inter, system-ui, sans-serif">✓ OFFER READY</text>
                                </g>
                            </g>

                            <!-- ── Floating Milestone Card 3: AI Smart Recommendation ── -->
                            <g class="hero-float-card hero-float-card-3" transform="translate(280, 350)" filter="url(#hero-card-shadow)">
                                <rect width="195" height="54" rx="12" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="1" />
                                <circle cx="26" cy="27" r="13" fill="var(--color-subtle)" />
                                <text x="26" y="31" text-anchor="middle" font-size="13">🎯</text>
                                <text x="48" y="22" font-size="11" font-weight="700" fill="var(--color-heading)" font-family="Inter, system-ui, sans-serif">Smart Match: 98%</text>
                                <text x="48" y="38" font-size="9.5" fill="var(--color-muted)" font-family="Inter, system-ui, sans-serif">33 Districts · 1,450+ Roles</text>
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

    <section class="section" id="how-it-works" style="background:var(--color-surface);border-top:1px solid var(--color-border);border-bottom:1px solid var(--color-border);">
        <div class="container">
            <div class="page-head" style="justify-content:center;text-align:center;">
                <div>
                    <h2>How Gujarat Rojgar Setu works</h2>
                    <p class="lead">A clear four-step path from registration to a verified placement.</p>
                </div>
            </div>

            <div class="steps-track" data-motion-steps>
                <span class="steps-line" aria-hidden="true"><span class="steps-line-fill" data-motion-steps-fill></span></span>
                <div class="steps-grid">
                    <div class="step-card" data-motion-step>
                        <span class="step-num">01</span>
                        <h3>Create your free profile</h3>
                        <p>Register with your qualification, skills and district. Complete your candidate dossier in minutes.</p>
                    </div>
                    <div class="step-card" data-motion-step>
                        <span class="step-num">02</span>
                        <h3>Explore &amp; read details</h3>
                        <p>Filter verified openings across all 33 districts and open any job to read the full official notification.</p>
                    </div>
                    <div class="step-card" data-motion-step>
                        <span class="step-num">03</span>
                        <h3>Save &amp; shortlist interests</h3>
                        <p>Mark any vacancy as interested, review AI-matched recommendations and build a personal shortlist of roles.</p>
                    </div>
                    <div class="step-card" data-motion-step>
                        <span class="step-num">04</span>
                        <h3>Attend &amp; get placed</h3>
                        <p>Attend interview calls or state Rozgar Melas and receive your official verified appointment.</p>
                    </div>
                </div>
            </div>

            <div class="panel panel-accent between" style="margin-top:var(--space-8);">
                <div>
                    <h2 style="font-size:1.0625rem;margin-bottom:4px;">Ready to shortlist jobs you are interested in?</h2>
                    <p class="text-sm muted" style="margin:0;">Sign in to your candidate dashboard to view interested jobs, AI recommendations and upcoming Rozgar melas.</p>
                </div>
                <div class="cluster">
                    <x-button variant="outline" :href="route('candidate.login')">Candidate sign in</x-button>
                    <x-button variant="ghost" :href="route('candidate.register')">Register free</x-button>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="departments">
        <div class="container">
            <div class="page-head" style="justify-content:center;text-align:center;">
                <div>
                    <h2>Top hiring departments &amp; organizations</h2>
                    <p class="lead">Partner government boards, state public sector undertakings and major industrial projects recruiting through GYRS.</p>
                </div>
            </div>

            <div class="dept-grid" data-motion-reveal data-motion-stagger>
                @foreach ([
                    ['Roads & Buildings Department (R&B)', 'State department &middot; Gandhinagar', 'Roads & Buildings'],
                    ['GIFT City Authority', 'Gandhinagar International Tech City', 'GIFT City'],
                    ['GETCO — Gujarat Energy Transmission', 'State power utility &middot; Vadodara', 'GETCO'],
                    ['Health & Family Welfare Department', 'State healthcare department &middot; Surat &amp; Rajkot', 'Health'],
                    ['Dholera Special Investment Region', 'Mega industrial and greenfield hub', 'Dholera'],
                    ['Gujarat Informatics Limited (GIL)', 'Department of Science &amp; Technology', 'Gujarat Informatics'],
                ] as [$deptName, $deptMeta, $deptQuery])
                    <div class="dept-card card is-interactive">
                        <span class="badge" style="align-self:flex-start;"><x-icon name="building" :size="14" /> Department</span>
                        <h3>{{ $deptName }}</h3>
                        <span class="dept-meta">{!! $deptMeta !!}</span>
                        <a class="dept-action-link" href="{{ route('home', ['keyword' => $deptQuery]) }}#job-explorer">
                            <span>View openings</span>
                            <x-icon name="arrow-right" :size="14" />
                        </a>
                    </div>
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
