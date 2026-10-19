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
<body class="layout-public">
    @if (request()->routeIs('home'))
        <div class="preloader" id="gyrs-preloader" aria-hidden="true">
            <div class="preloader-stage">
                <div class="preloader-mark">
                    <svg class="preloader-art" viewBox="0 0 140 140" width="140" height="140" aria-hidden="true" focusable="false">
                        <g fill="none" stroke-linecap="round">
                            <circle class="pl-ring" cx="70" cy="70" r="54" stroke-width="2" />
                            <circle class="pl-ring" cx="70" cy="70" r="38" stroke-width="2" />
                            <circle class="pl-ring" cx="70" cy="70" r="22" stroke-width="2" />
                            <circle class="pl-ripple" cx="70" cy="70" r="8" stroke-width="2" opacity="0" />
                            <circle class="pl-bull" cx="70" cy="70" r="7" />
                        </g>
                        <g class="pl-arrow" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none">
                            <line x1="72" y1="72" x2="114" y2="114" />
                            <path d="M111 112 L104 116 M111 112 L116 104" />
                            <path class="pl-tip" d="M70 70 L85.5 74.5 L74.5 85.5 Z" />
                        </g>
                    </svg>
                </div>
                <div class="preloader-words" aria-hidden="true">
                    <span class="pl-word pl-word-1">roles</span>
                    <span class="pl-word pl-word-2">opportunity</span>
                    <span class="pl-word pl-word-3">jobs</span>
                    <span class="pl-word pl-word-4">skills</span>
                    <span class="pl-word pl-word-5">upgrade</span>
                    <span class="pl-word pl-word-6">stay informed</span>
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

    @stack('scripts')
</body>
</html>
