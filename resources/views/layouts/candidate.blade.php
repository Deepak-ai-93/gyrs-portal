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
    <title>@yield('title', 'Candidate workspace — GYRS')</title>
    <meta name="description" content="@yield('meta_description', 'GYRS candidate workspace: save interested jobs, manage your profile and discover AI-matched vacancies.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-candidate has-tabbar">
    <a class="skip-link" href="#main">Skip to main content</a>

    @php($user = auth()->user())

    <header class="site-header">
        <div class="container">
            <a class="brand" href="{{ route('candidate.dashboard') }}">
                <span class="brand-title"><x-icon name="user" :size="18" /> Candidate workspace</span>
                <span class="brand-sub">Gujarat Youth Rojgar Setu (GYRS)</span>
            </a>

            <button type="button" class="menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="candidate-navigation">
                <x-icon name="menu" :size="18" /> Menu
            </button>

            <div class="nav-collapse" id="candidate-navigation" data-mobile-menu>
                <nav class="main-nav" aria-label="Candidate">
                    <a href="{{ route('candidate.dashboard') }}" @if (request()->routeIs('candidate.dashboard')) aria-current="page" @endif><x-icon name="home" :size="16" /> Dashboard</a>
                    <a href="{{ route('candidate.interested.index') }}" @if (request()->routeIs('candidate.interested.index')) aria-current="page" @endif><x-icon name="sparkles" :size="16" /> Interested jobs</a>
                    <a href="{{ route('home') }}#job-explorer"><x-icon name="search" :size="16" /> Explore jobs</a>
                </nav>

                <div class="header-actions">
                    @if ($user)
                        <span class="identity-chip">
                            <x-icon name="user" :size="15" />
                            <span class="name">{{ $user->name }}</span>
                        </span>
                    @endif
                    <form method="POST" action="{{ route('candidate.logout') }}">
                        @csrf
                        <x-button variant="ghost" size="sm" type="submit"><x-icon name="logout" :size="16" /> Sign out</x-button>
                    </form>
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
                    <h3><x-icon name="user" :size="16" /> Candidate workspace</h3>
                    <p class="text-sm">Save jobs you are interested in, keep your profile current and discover AI-matched vacancies.</p>
                </div>

                <div>
                    <h3>Quick links</h3>
                    <ul class="text-sm">
                        <li><a href="{{ route('candidate.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('candidate.interested.index') }}">Interested jobs</a></li>
                        <li><a href="{{ route('home') }}#job-explorer">Explore jobs</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Account</h3>
                    <ul class="text-sm">
                        <li><a href="{{ route('home') }}">Public job board</a></li>
                        <li><a href="{{ route('admin.login') }}"><x-icon name="lock" :size="14" /> Portal admin login</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; {{ date('Y') }} GYRS Portal. All rights reserved.
            </div>
        </div>
    </footer>

    @include('partials.mobile-tabbar', ['context' => 'candidate'])
</body>
</html>
