<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Candidate workspace — GYRS')</title>
    <meta name="description" content="@yield('meta_description', 'GYRS candidate workspace: track applications, manage your profile and discover recommended vacancies.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-candidate">
    <a class="skip-link" href="#main">Skip to main content</a>

    @php($user = auth()->user())

    <div class="gov-bar">
        <div class="container">
            <span class="gov-meta"><x-icon name="building" :size="16" /> Government of Gujarat &middot; Directorate of Employment &amp; Training</span>
            <span class="gov-meta">Toll-free 1800-233-5500</span>
        </div>
    </div>

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
                    <a href="{{ route('candidate.applications.index') }}" @if (request()->routeIs('candidate.applications.index')) aria-current="page" @endif><x-icon name="file" :size="16" /> My applications</a>
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

    <footer class="site-footer" style="margin-top:0;">
        <div class="container">
            <div class="footer-bottom" style="border-top:0;margin-top:0;">
                &copy; {{ date('Y') }} GYRS Portal &middot; Directorate of Employment &amp; Training, Government of Gujarat
            </div>
        </div>
    </footer>
</body>
</html>
