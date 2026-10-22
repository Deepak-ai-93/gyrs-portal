<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <!--
      ============================================================
       GYRS Portal — source-only developer signature (noindex)
       Designed & developed by Deepak Bagada.
       HTML comments are never rendered and are not indexed by
       search engines; this credit lives only in the page source.
      ============================================================
    -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GYRS — Gujarat Youth Rojgar Setu')</title>
    <meta name="description" content="@yield('meta_description', 'GYRS is the official public employment portal. Explore verified public and private vacancies across all 33 districts.')">
    @include('partials.dev-hmr-guard')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (request()->routeIs('home'))
        <script>
            (function () {
                try {
                    var el = document.documentElement;
                    el.classList.add('is-preloading');

                    var released = false;
                    window.__gyrsPreloaderRelease = function () {
                        if (released) {
                            return;
                        }
                        released = true;
                        el.classList.remove('is-preloading');
                    };

                    /* Safety net: never let the overlay trap the page if the
                       motion script fails to load or throw. */
                    window.setTimeout(window.__gyrsPreloaderRelease, 4000);
                } catch (e) {}
            })();
        </script>
    @endif
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script defer src="{{ asset('js/public-motion.js') }}"></script>
</head>
<body class="layout-public has-tabbar">
    @if (request()->routeIs('home'))
        <div class="preloader" id="gyrs-preloader" aria-hidden="true">
            <div class="preloader-stage">
                <div class="preloader-mark">
                    <svg class="preloader-art" viewBox="0 0 200 200" width="200" height="200" aria-hidden="true" focusable="false">
                        <defs>
                            <radialGradient id="pl-radar-glow" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="var(--color-heading)" stop-opacity="0.08" />
                                <stop offset="60%" stop-color="var(--color-heading)" stop-opacity="0.02" />
                                <stop offset="100%" stop-color="var(--color-heading)" stop-opacity="0" />
                            </radialGradient>
                            <linearGradient id="pl-arrow-grad-l" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="var(--color-heading)" />
                                <stop offset="100%" stop-color="var(--color-text)" />
                            </linearGradient>
                            <linearGradient id="pl-arrow-grad-r" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="var(--color-accent-hover)" />
                                <stop offset="100%" stop-color="var(--color-muted)" />
                            </linearGradient>
                        </defs>

                        <!-- Ambient Radar Field -->
                        <circle class="pl-ambient" cx="100" cy="100" r="92" fill="url(#pl-radar-glow)" />

                        <!-- Outer Dial & Graduations -->
                        <circle class="pl-outer-frame" cx="100" cy="100" r="88" fill="none" stroke="var(--color-border)" stroke-width="1.5" />
                        <circle class="pl-dial-track" cx="100" cy="100" r="80" fill="none" stroke="var(--color-border-strong)" stroke-width="2" stroke-dasharray="2 10" stroke-linecap="round" />

                        <!-- Cardinal Calibration Markers -->
                        <g class="pl-cardinals">
                            <line class="pl-cardinal pl-card-n" x1="100" y1="6" x2="100" y2="18" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-cardinal pl-card-s" x1="100" y1="182" x2="100" y2="194" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-cardinal pl-card-w" x1="6" y1="100" x2="18" y2="100" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-cardinal pl-card-e" x1="182" y1="100" x2="194" y2="100" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <circle class="pl-pip" cx="156" cy="44" r="2" fill="var(--color-muted)" />
                            <circle class="pl-pip" cx="156" cy="156" r="2" fill="var(--color-muted)" />
                            <circle class="pl-pip" cx="44" cy="156" r="2" fill="var(--color-muted)" />
                            <circle class="pl-pip" cx="44" cy="44" r="2" fill="var(--color-muted)" />
                        </g>

                        <!-- Orbital Tracking Gyro -->
                        <g class="pl-orbit-group">
                            <circle class="pl-orbit-ring" cx="100" cy="100" r="62" fill="none" stroke="var(--color-border-strong)" stroke-width="1.5" stroke-dasharray="44 18 14 18" stroke-linecap="round" />
                            <circle class="pl-satellite" cx="100" cy="38" r="3.5" fill="var(--color-heading)" />
                            <circle class="pl-satellite-pulse" cx="100" cy="38" r="7" fill="none" stroke="var(--color-heading)" stroke-width="1" opacity="0.4" />
                        </g>

                        <!-- Reticle Crosshairs & Inner Target -->
                        <circle class="pl-inner-ring" cx="100" cy="100" r="42" fill="none" stroke="var(--color-border)" stroke-width="1.5" stroke-dasharray="8 8" />
                        <line class="pl-crosshair" x1="78" y1="100" x2="122" y2="100" stroke="var(--color-border-strong)" stroke-width="1.2" stroke-linecap="round" />
                        <line class="pl-crosshair" x1="100" y1="78" x2="100" y2="122" stroke="var(--color-border-strong)" stroke-width="1.2" stroke-linecap="round" />

                        <!-- Resonance Shockwave Ripples -->
                        <circle class="pl-ripple pl-ripple-1" cx="100" cy="100" r="10" stroke="var(--color-heading)" stroke-width="2" fill="none" opacity="0" />
                        <circle class="pl-ripple pl-ripple-2" cx="100" cy="100" r="10" stroke="var(--color-accent-hover)" stroke-width="1.5" fill="none" opacity="0" />

                        <!-- Starburst Flare Sparks on Alignment Lock -->
                        <g class="pl-sparks" opacity="0">
                            <line class="pl-spark" x1="100" y1="84" x2="100" y2="66" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-spark" x1="111" y1="89" x2="124" y2="76" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-spark" x1="116" y1="100" x2="134" y2="100" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-spark" x1="111" y1="111" x2="124" y2="124" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-spark" x1="100" y1="116" x2="100" y2="134" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-spark" x1="89" y1="111" x2="76" y2="124" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-spark" x1="84" y1="100" x2="66" y2="100" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                            <line class="pl-spark" x1="89" y1="89" x2="76" y2="76" stroke="var(--color-heading)" stroke-width="2" stroke-linecap="round" />
                        </g>

                        <!-- Precision Pathfinder Needle / Arrow Group -->
                        <g class="pl-arrow-system">
                            <!-- Aerodynamic Pathfinder Needle -->
                            <path class="pl-arrow-blade-left" d="M100 34 L100 100 L90 88 Z" fill="url(#pl-arrow-grad-l)" />
                            <path class="pl-arrow-blade-right" d="M100 34 L110 88 L100 100 Z" fill="url(#pl-arrow-grad-r)" />
                            <path class="pl-arrow-tail-left" d="M100 100 L100 126 L94 118 Z" fill="var(--color-muted)" />
                            <path class="pl-arrow-tail-right" d="M100 100 L106 118 L100 126 Z" fill="var(--color-border-strong)" />
                            <!-- Central Nexus Hub -->
                            <circle class="pl-hub-ring" cx="100" cy="100" r="9" fill="var(--color-surface)" stroke="var(--color-heading)" stroke-width="2.5" />
                            <circle class="pl-bull" cx="100" cy="100" r="4.5" fill="var(--color-heading)" />
                        </g>
                    </svg>
                </div>

                <!-- Floating Orbit Chips -->
                <div class="preloader-chips" aria-hidden="true">
                    <span class="pl-chip pl-chip-1"><span class="pl-chip-dot"></span>Opportunities</span>
                    <span class="pl-chip pl-chip-2"><span class="pl-chip-dot"></span>Skill Match</span>
                    <span class="pl-chip pl-chip-3"><span class="pl-chip-dot"></span>33 Districts</span>
                    <span class="pl-chip pl-chip-4"><span class="pl-chip-dot"></span>Rozgar Melas</span>
                    <span class="pl-chip pl-chip-5"><span class="pl-chip-dot"></span>Career Setu</span>
                </div>

                <!-- Telemetry & Live Progress HUD -->
                <div class="preloader-telemetry" aria-hidden="true">
                    <div class="pl-status-pill">
                        <span class="pl-pulse-beacon"></span>
                        <span class="pl-status-text" id="pl-status-text">CALIBRATING OPPORTUNITIES</span>
                    </div>
                    <div class="pl-meter-row">
                        <div class="pl-meter-track">
                            <div class="pl-meter-bar" id="pl-meter-bar"></div>
                        </div>
                        <span class="pl-counter" id="pl-counter">0%</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <a class="skip-link" href="#main">Skip to main content</a>

    <header class="site-header">
        <div class="container">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-title"><x-icon name="briefcase" :size="18" /> GYRS GUJARAT</span>
                <span class="brand-sub">Gujarat Youth Rojgar Setu</span>
            </a>

            <button
                type="button"
                class="menu-toggle"
                data-menu-toggle
                aria-expanded="false"
                aria-controls="primary-navigation"
            >
                <x-icon name="menu" :size="18" /> Menu
            </button>

            <div class="nav-collapse" id="primary-navigation" data-mobile-menu>
                <nav class="main-nav" aria-label="Primary">
                    <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif><x-icon name="home" :size="16" /> Home</a>
                    <a href="{{ route('home') }}#job-explorer">Explore jobs</a>
                    <a href="{{ route('home') }}#how-it-works">How it works</a>
                    @auth
                        @if (auth()->user()->isCandidate())
                            <a href="{{ route('candidate.dashboard') }}" @if (request()->routeIs('candidate.dashboard')) aria-current="page" @endif><x-icon name="user" :size="16" /> My dashboard</a>
                        @endif
                    @endauth
                </nav>

                <div class="header-actions">
                    @auth
                        @if (auth()->user()->isCandidate())
                            <span class="identity-chip"><x-icon name="user" :size="15" /> <span class="name">{{ auth()->user()->name }}</span></span>
                            <form method="POST" action="{{ route('candidate.logout') }}">
                                @csrf
                                <x-button variant="ghost" size="sm" type="submit"><x-icon name="logout" :size="16" /> Sign out</x-button>
                            </form>
                        @else
                            <a class="btn btn-ghost btn-sm" href="{{ route('admin.dashboard') }}">Admin panel</a>
                        @endif
                    @else
                        <x-button variant="outline" size="sm" :href="route('candidate.login')">Candidate sign in</x-button>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main id="main" class="page-main" tabindex="-1">
        <div class="container">
            @include('partials.flash')
            @include('partials.errors')
        </div>

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3><x-icon name="briefcase" :size="16" /> GYRS GUJARAT</h3>
                    <p class="text-sm">
                        Gujarat Youth Rojgar Setu (GYRS) connects job seekers across all 33 districts with verified
                        public and private employment opportunities.
                    </p>
                </div>

                <div>
                    <h3>Quick navigation</h3>
                    <ul class="text-sm">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('home') }}#job-explorer">Explore jobs</a></li>
                        <li><a href="{{ route('home') }}#how-it-works">How it works</a></li>
                        <li><a href="{{ route('home') }}#departments">Departments</a></li>
                        <li><a href="{{ route('home') }}#faqs">FAQs</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Candidate access</h3>
                    <ul class="text-sm">
                        <li><a href="{{ route('candidate.login') }}">Candidate login</a></li>
                        <li><a href="{{ route('candidate.register') }}">Register new candidate</a></li>
                        <li><a href="{{ route('candidate.dashboard') }}">Candidate dashboard</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Support</h3>
                    <ul class="text-sm">
                        <li><a href="{{ route('home') }}#faqs">Help &amp; FAQs</a></li>
                        <li><a href="{{ route('home') }}#job-explorer">Browse vacancies</a></li>
                        <li><a href="{{ route('admin.login') }}"><x-icon name="lock" :size="14" /> Portal admin login</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; {{ date('Y') }} GYRS Portal. All rights reserved.
            </div>
        </div>
    </footer>

    @include('partials.mobile-tabbar')

    @stack('scripts')
</body>
</html>
