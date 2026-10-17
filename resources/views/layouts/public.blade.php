<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GYRS — Gujarat Youth Rojgar Setu')</title>
    <meta name="description" content="@yield('meta_description', 'GYRS is the official Government of Gujarat public employment portal. Explore verified government and private vacancies across all 33 districts.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-public">
    <a class="skip-link" href="#main">Skip to main content</a>

    <div class="gov-bar">
        <div class="container">
            <span class="gov-meta"><x-icon name="building" :size="16" /> Government of Gujarat &middot; Directorate of Employment &amp; Training (DET)</span>
            <span class="gov-meta">
                <a href="#accessibility">Accessibility</a>
                <span aria-hidden="true">|</span>
                <span>Toll-free 1800-233-5500</span>
            </span>
        </div>
    </div>

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

    <footer class="site-footer" id="accessibility">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3><x-icon name="briefcase" :size="16" /> GYRS GUJARAT</h3>
                    <p class="text-sm">
                        Gujarat Youth Rojgar Setu is an official platform of the Directorate of Employment &amp; Training, connecting
                        young job seekers across all 33 districts with verified public and private employment opportunities.
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
                    <h3>Directorate contact</h3>
                    <p class="text-sm" style="margin-bottom:8px;">
                        Block No. 14, Dr. Jivraj Mehta Bhavan,<br>
                        Gandhinagar, Gujarat 382010
                    </p>
                    <p class="text-sm" style="margin-bottom:8px;">Toll-free helpline: 1800-233-5500</p>
                    <p class="text-sm" style="margin-bottom:0;">support.gyrs@gujarat.gov.in</p>
                    <p class="text-sm" style="margin-top:12px;"><a href="{{ route('admin.login') }}"><x-icon name="lock" :size="14" /> GYRS Portal admin login</a></p>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; {{ date('Y') }} GYRS Portal. An initiative of the Directorate of Employment &amp; Training, Government of Gujarat.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
