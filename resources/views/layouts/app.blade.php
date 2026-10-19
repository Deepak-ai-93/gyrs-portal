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

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3><x-icon name="briefcase" :size="16" /> GYRS</h3>
                    <p class="text-sm">Gujarat Youth Rojgar Setu — verified public and private opportunities across all 33 districts.</p>
                </div>

                <div>
                    <h3>Quick links</h3>
                    <ul class="text-sm">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('candidate.login') }}">Candidate login</a></li>
                        <li><a href="{{ route('candidate.register') }}">Register new candidate</a></li>
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
