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
    <title>@yield('title', 'GYRS Portal — Gujarat Youth Rojgar Setu')</title>
    <meta name="description" content="@yield('meta_description', 'GYRS — the official Government of Gujarat employment and placement portal by the Directorate of Employment and Training.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-app">
    <a class="skip-link" href="#main">Skip to main content</a>

    <div class="gov-bar">
        <div class="container">
            <span class="gov-meta"><x-icon name="building" :size="16" /> Government of Gujarat &middot; Directorate of Employment &amp; Training</span>
            <span class="gov-meta"><a href="{{ route('home') }}">Public job board</a></span>
        </div>
    </div>

    <header class="site-header">
        <div class="container">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-title"><x-icon name="briefcase" :size="18" /> GYRS</span>
                <span class="brand-sub">Gujarat Youth Rojgar Setu</span>
            </a>
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
                &copy; {{ date('Y') }} GYRS Portal &middot; Directorate of Employment &amp; Training, Government of Gujarat &middot; Toll-free 1800-233-5500
            </div>
        </div>
    </footer>
</body>
</html>
