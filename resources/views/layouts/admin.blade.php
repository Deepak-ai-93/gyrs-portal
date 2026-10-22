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
    @include('partials.dev-hmr-guard')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-admin">
    <a class="skip-link" href="#main">Skip to main content</a>

    @php
        $user = auth()->user();
        $can = fn (string $permission) => (bool) $user?->hasAdminPermission($permission);
        $canViewJobs = $can(\App\Support\AdminPermissions::JOBS_VIEW);
        $canViewCandidates = $can(\App\Support\AdminPermissions::CANDIDATES_VIEW);
        $canViewApplications = $can(\App\Support\AdminPermissions::APPLICATIONS_VIEW);
        $canExportCandidates = $can(\App\Support\AdminPermissions::EXPORT_CANDIDATES);
    @endphp

    <header class="site-header">
        <div class="container">
            <a class="brand" href="{{ route('admin.dashboard') }}">
                <span class="brand-title"><x-icon name="shield" :size="18" /> GYRS PORTAL</span>
                <span class="brand-sub">Administration</span>
            </a>

            <button type="button" class="menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="admin-sidebar">
                <x-icon name="menu" :size="18" /> Menu
            </button>

            <div class="header-actions">
                @if ($user)
                    <span class="identity-chip">
                        <x-icon name="shield" :size="15" />
                        <span class="name">{{ $user->name }}</span>
                        <span class="chip-role">{{ $user->adminRoleLabel() }}</span>
                    </span>
                @endif
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <x-button variant="ghost" size="sm" type="submit"><x-icon name="logout" :size="16" /> Sign out</x-button>
                </form>
            </div>
        </div>
    </header>

    <div class="admin-shell">
        <aside class="admin-sidebar" id="admin-sidebar" data-mobile-menu>
            <nav class="side-nav" aria-label="Administration">
                <p class="side-nav-label">Overview</p>
                <a href="{{ route('admin.dashboard') }}" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif><x-icon name="home" :size="16" /> Dashboard</a>

                @if ($canViewJobs || $canViewCandidates || $canViewApplications)
                    <p class="side-nav-label">Manage</p>
                    @if ($canViewJobs)
                        <a href="{{ route('admin.jobs.index') }}" @if (request()->routeIs('admin.jobs.*')) aria-current="page" @endif><x-icon name="briefcase" :size="16" /> Job postings</a>
                    @endif
                    @if ($canViewCandidates)
                        <a href="{{ route('admin.candidates.index') }}" @if (request()->routeIs('admin.candidates.*')) aria-current="page" @endif><x-icon name="users" :size="16" /> Candidates</a>
                    @endif
                    @if ($canViewApplications)
                        <a href="{{ route('admin.applications.index') }}" @if (request()->routeIs('admin.applications.*')) aria-current="page" @endif><x-icon name="file" :size="16" /> Applications</a>
                    @endif
                @endif

                @if ($canExportCandidates)
                    <p class="side-nav-label">Data</p>
                    <a href="{{ route('admin.exports.candidates') }}"><x-icon name="download" :size="16" /> Export candidates</a>
                @endif

                @if ($user?->isSuperAdmin())
                    <p class="side-nav-label">Access control</p>
                    <a href="{{ route('admin.administrators.index') }}" @if (request()->routeIs('admin.administrators.*')) aria-current="page" @endif><x-icon name="shield" :size="16" /> Administrators</a>
                @endif

                <p class="side-nav-label">Site</p>
                <a href="{{ route('home') }}"><x-icon name="external" :size="16" /> Public job board</a>
            </nav>
        </aside>

        <main id="main" class="page-main admin-main" tabindex="-1">
            <div class="container">
                @include('partials.flash')
                @include('partials.errors')
            </div>

            @yield('content')
        </main>
    </div>
</body>
</html>
