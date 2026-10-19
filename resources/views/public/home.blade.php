@extends('layouts.public')

@section('title', 'GYRS — Gujarat Youth Rojgar Setu | Government Job Portal')
@section('meta_description', 'Explore verified Government of Gujarat and private vacancies across all 33 districts. Free candidate registration and application tracking.')

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
        <div class="container">
            <div class="hero-inner">
                <span class="hero-badge"><x-icon name="shield" :size="16" /> Directorate of Employment &amp; Training &middot; Government of Gujarat</span>

                <h1 class="hero-title">
                    Find your next
                    <span class="rotator hero-rotator" data-rotator data-rotator-interval="2600">
                        <span class="rotator-item is-active" data-rotator-item>role</span>
                        <span class="rotator-item" data-rotator-item>career</span>
                        <span class="rotator-item" data-rotator-item>opportunity</span>
                        <span class="rotator-item" data-rotator-item>future</span>
                    </span>
                    in Gujarat
                </h1>

                <p class="hero-sub">
                    Search verified state government positions, public department vacancies and leading private sector roles
                    across all 33 districts — free for every candidate.
                </p>

                <p class="hero-story">
                    <span class="hero-story-icon" aria-hidden="true"><x-icon name="sparkles" :size="15" /></span>
                    <span class="rotator hero-story-rotator" data-rotator data-rotator-interval="4600" aria-live="polite">
                        <span class="rotator-item is-active" data-rotator-item>From the first search to the final offer letter — one platform for every district.</span>
                        <span class="rotator-item" data-rotator-item>Register free, save the jobs you like and let smart matching surface the right roles.</span>
                        <span class="rotator-item" data-rotator-item>Government posts, department vacancies and private roles — verified and in one place.</span>
                        <span class="rotator-item" data-rotator-item>33 districts. Thousands of aspirations. One Gujarat.</span>
                    </span>
                </p>

                <form class="search-panel" method="GET" action="{{ route('home') }}" role="search" data-loading-form>
                    <div class="search-seg">
                        <x-icon name="search" />
                        <label class="sr-only" for="search-keyword">Keyword</label>
                        <input class="form-control" type="search" id="search-keyword" name="keyword" value="{{ $keyword }}" placeholder="Skills, job title or department">
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
                    @foreach (['Software Engineer', 'Civil Engineer', 'Staff Nurse', 'Data Entry', 'Solar Technician', 'Apprenticeship'] as $term)
                        <a class="chip" href="{{ route('home', ['keyword' => $term]) }}">{{ $term }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="metric-strip">
        <div class="container">
            <div class="grid grid-4">
                <div class="metric">
                    <div class="metric-num">{{ number_format((int) $jobCount) }}</div>
                    <div class="metric-label">Open vacancies on GYRS</div>
                </div>
                <div class="metric">
                    <div class="metric-num">33</div>
                    <div class="metric-label">Districts covered statewide</div>
                </div>
                <div class="metric">
                    <div class="metric-num">100%</div>
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
                    <h2>Explore open jobs in Gujarat</h2>
                    <p class="lead">Verified state government posts, public department vacancies and private industrial roles.</p>
                </div>
                <p class="result-count">Showing <strong>{{ $jobs->total() }}</strong> matching {{ Str::plural('opportunity', $jobs->total()) }}</p>
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
                        <div class="jobs-grid">
                            @foreach ($jobs as $job)
                                <x-job-card :job="$job" :saved="in_array($job->getKey(), $savedJobIds ?? [], true)" />
                            @endforeach
                        </div>

                        <x-pagination :paginator="$jobs" />
                    @endif
                </div>

                <aside class="filter-panel" aria-label="Filter vacancies">
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
                            <li>Official Gujarat state verified postings</li>
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

            <div class="steps-grid">
                <div class="step-card">
                    <span class="step-num">01</span>
                    <h3>Create your free profile</h3>
                    <p>Register with your qualification, skills and district. Complete your candidate dossier in minutes.</p>
                </div>
                <div class="step-card">
                    <span class="step-num">02</span>
                    <h3>Explore &amp; read details</h3>
                    <p>Filter verified openings across all 33 districts and open any job to read the full official notification.</p>
                </div>
                <div class="step-card">
                    <span class="step-num">03</span>
                    <h3>Save &amp; shortlist interests</h3>
                    <p>Mark any vacancy as interested, review AI-matched recommendations and build a personal shortlist of roles.</p>
                </div>
                <div class="step-card">
                    <span class="step-num">04</span>
                    <h3>Attend &amp; get placed</h3>
                    <p>Attend interview calls or state Rozgar Melas and receive your official verified appointment.</p>
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

            <div class="dept-grid">
                @foreach ([
                    ['Roads & Buildings Department (R&B)', 'Government of Gujarat &middot; Gandhinagar'],
                    ['GIFT City Authority', 'Gandhinagar International Tech City'],
                    ['GETCO — Gujarat Energy Transmission', 'State power utility &middot; Vadodara'],
                    ['Health & Family Welfare Department', 'State healthcare directorate &middot; Surat &amp; Rajkot'],
                    ['Dholera Special Investment Region', 'Mega industrial and greenfield hub'],
                    ['Gujarat Informatics Limited (GIL)', 'Department of Science &amp; Technology'],
                ] as [$deptName, $deptMeta])
                    <div class="dept-card">
                        <span class="badge" style="align-self:flex-start;"><x-icon name="building" :size="14" /> Department</span>
                        <h3>{{ $deptName }}</h3>
                        <span class="dept-meta">{!! $deptMeta !!}</span>
                        <a class="text-sm" style="margin-top:auto;padding-top:8px;" href="{{ route('home') }}#job-explorer">View jobs</a>
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

            <div class="faq-list">
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
                        No. GYRS is an initiative of the Directorate of Employment &amp; Training, Government of Gujarat. Candidate
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
