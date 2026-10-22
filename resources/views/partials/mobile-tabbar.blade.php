@php
    $context = $context ?? 'public';
    $user = auth()->user();
@endphp

<nav class="mobile-tabbar" aria-label="Quick navigation">
    @if ($context === 'candidate')
        <a
            href="{{ route('candidate.dashboard') }}"
            class="mobile-tabbar-item @if (request()->routeIs('candidate.dashboard')) is-active @endif"
            @if (request()->routeIs('candidate.dashboard')) aria-current="page" @endif
        >
            <span class="mobile-tabbar-icon"><x-icon name="home" :size="20" /></span>
            <span class="mobile-tabbar-label">Dashboard</span>
        </a>

        <a
            href="{{ route('candidate.interested.index') }}"
            class="mobile-tabbar-item @if (request()->routeIs('candidate.interested.index')) is-active @endif"
            @if (request()->routeIs('candidate.interested.index')) aria-current="page" @endif
        >
            <span class="mobile-tabbar-icon"><x-icon name="sparkles" :size="20" /></span>
            <span class="mobile-tabbar-label">Interested</span>
        </a>

        <a href="{{ route('home') }}#job-explorer" class="mobile-tabbar-item">
            <span class="mobile-tabbar-icon"><x-icon name="search" :size="20" /></span>
            <span class="mobile-tabbar-label">Explore</span>
        </a>

        <form method="POST" action="{{ route('candidate.logout') }}" class="mobile-tabbar-form">
            @csrf
            <button type="submit" class="mobile-tabbar-item">
                <span class="mobile-tabbar-icon"><x-icon name="logout" :size="20" /></span>
                <span class="mobile-tabbar-label">Sign out</span>
            </button>
        </form>
    @else
        <a
            href="{{ route('home') }}"
            class="mobile-tabbar-item @if (request()->routeIs('home')) is-active @endif"
            @if (request()->routeIs('home')) aria-current="page" @endif
        >
            <span class="mobile-tabbar-icon"><x-icon name="home" :size="20" /></span>
            <span class="mobile-tabbar-label">Home</span>
        </a>

        <a href="{{ route('home') }}#job-explorer" class="mobile-tabbar-item">
            <span class="mobile-tabbar-icon"><x-icon name="briefcase" :size="20" /></span>
            <span class="mobile-tabbar-label">Jobs</span>
        </a>

        @auth
            @if ($user->isAdmin())
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="mobile-tabbar-item @if (request()->routeIs('admin.dashboard')) is-active @endif"
                    @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif
                >
                    <span class="mobile-tabbar-icon"><x-icon name="shield" :size="20" /></span>
                    <span class="mobile-tabbar-label">Admin</span>
                </a>
            @else
                <a
                    href="{{ route('candidate.dashboard') }}"
                    class="mobile-tabbar-item @if (request()->routeIs('candidate.dashboard')) is-active @endif"
                    @if (request()->routeIs('candidate.dashboard')) aria-current="page" @endif
                >
                    <span class="mobile-tabbar-icon"><x-icon name="user" :size="20" /></span>
                    <span class="mobile-tabbar-label">Workspace</span>
                </a>
            @endif
        @else
            <a
                href="{{ route('candidate.login') }}"
                class="mobile-tabbar-item @if (request()->routeIs('candidate.login')) is-active @endif"
                @if (request()->routeIs('candidate.login')) aria-current="page" @endif
            >
                <span class="mobile-tabbar-icon"><x-icon name="user" :size="20" /></span>
                <span class="mobile-tabbar-label">Sign in</span>
            </a>

            <a
                href="{{ route('candidate.register') }}"
                class="mobile-tabbar-item @if (request()->routeIs('candidate.register')) is-active @endif"
                @if (request()->routeIs('candidate.register')) aria-current="page" @endif
            >
                <span class="mobile-tabbar-icon"><x-icon name="plus" :size="20" /></span>
                <span class="mobile-tabbar-label">Register</span>
            </a>
        @endauth
    @endif
</nav>
