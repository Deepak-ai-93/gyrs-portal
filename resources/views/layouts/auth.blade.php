<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Candidate sign in — GYRS')</title>
    <meta name="description" content="@yield('meta_description', 'Sign in or create your free GYRS candidate account to apply for verified opportunities across Gujarat.')">
    @include('partials.dev-hmr-guard')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-auth has-tabbar">
    <a class="skip-link" href="#auth-panel">Skip to the sign-in form</a>

    <main class="auth-shell">
        <aside class="auth-visual" aria-hidden="true">
            @include('partials.auth-visual')
        </aside>

        <section class="auth-panel" id="auth-panel" tabindex="-1">
            <div class="auth-panel-bar">
                <a class="auth-brand" href="{{ route('home') }}">
                    <span class="auth-brand-mark"><x-icon name="briefcase" :size="18" /></span>
                    <span class="auth-brand-text">
                        <span class="auth-brand-name">GYRS</span>
                        <span class="auth-brand-sub">Gujarat Youth Rojgar Setu</span>
                    </span>
                </a>

                <a class="auth-back" href="{{ route('home') }}">
                    <x-icon name="arrow-left" :size="15" />
                    <span>Job board</span>
                </a>
            </div>

            <div class="auth-panel-main">
                <div class="auth-panel-inner @yield('panel_class')">
                    @include('partials.flash')
                    @include('partials.errors')

                    @yield('auth-content')
                </div>
            </div>
        </section>
    </main>

    @include('partials.mobile-tabbar')
</body>
</html>
