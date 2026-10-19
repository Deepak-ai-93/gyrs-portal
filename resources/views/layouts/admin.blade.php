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
    <title>@yield('title', 'Admin panel — GYRS Portal')</title>
    <meta name="description" content="@yield('meta_description', 'GYRS Portal administration for job vacancies, candidate verification and application audit.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-admin">
    <a class="skip-link" href="#main">Skip to main content</a>

    @php($user = auth()->user())

    <header class="site-header">
        <div class="container">
            <a class="brand" href="{{ route('admin.dashboard') }}">
                <span class="brand-title"><x-icon name="shield" :size="18" /> GYRS PORTAL</span>
                <span class="brand-sub">Administration</span>
            </a>

            <button type="button" class="menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="admin-navigation">
                <x-icon name="menu" :size="18" /> Menu
            </button>

            <div class="nav-collapse" id="admin-navigation" data-mobile-menu>
                <nav class="main-nav" aria-label="Administration">
                    <a href="{{ route('admin.dashboard') }}" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif><x-icon name="home" :size="16" /> Overview</a>
                    <a href="{{ route('admin.jobs.index') }}" @if (request()->routeIs('admin.jobs.*')) aria-current="page" @endif><x-icon name="briefcase" :size="16" /> Jobs</a>
                    <a href="{{ route('admin.candidates.index') }}" @if (request()->routeIs('admin.candidates.*')) aria-current="page" @endif><x-icon name="users" :size="16" /> Candidates</a>
                    <a href="{{ route('admin.applications.index') }}" @if (request()->routeIs('admin.applications.*')) aria-current="page" @endif><x-icon name="file" :size="16" /> Applications</a>
                </nav>

                <div class="header-actions">
                    @if ($user)
                        <span class="identity-chip">
                            <x-icon name="shield" :size="15" />
                            <span class="name">{{ $user->name }}</span>
                        </span>
                    @endif
                    <form method="POST" action="{{ route('admin.logout') }}">
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
                    <h3><x-icon name="shield" :size="16" /> GYRS Portal</h3>
                    <p class="text-sm">Administration for job vacancies, candidate verification and application audit.</p>
                </div>

                <div>
                    <h3>Quick links</h3>
                    <ul class="text-sm">
                        <li><a href="{{ route('admin.dashboard') }}">Overview</a></li>
                        <li><a href="{{ route('admin.jobs.index') }}">Jobs</a></li>
                        <li><a href="{{ route('admin.candidates.index') }}">Candidates</a></li>
                        <li><a href="{{ route('admin.applications.index') }}">Applications</a></li>
                        <li><a href="{{ route('home') }}"><x-icon name="external" :size="14" /> Public job board</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; {{ date('Y') }} GYRS Portal. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
