@extends('layouts.auth')

@php
    $otpPhone = session('otp_phone');
    $isOtp = ($method ?? 'password') === 'otp';
@endphp

@section('title', $isOtp ? 'Mobile OTP sign in — GYRS' : 'Candidate sign in — GYRS')

@section('auth-content')
    @if ($isOtp)
        <div class="auth-panel-head">
            <p class="auth-kicker"><span class="auth-kicker-dot"></span> Candidate gateway</p>
            <h1 class="auth-title-lg">Sign in with a code</h1>
            <p class="auth-lede">We will text a one-time code to the mobile number registered with your GYRS profile. No password needed.</p>
        </div>

        @include('partials.auth-tabs', ['active' => 'login'])

        @if ($otpPhone)
            <form method="POST" action="{{ route('candidate.otp.verify') }}" class="auth-form" data-loading-form>
                @csrf

                <input type="hidden" name="phone" value="{{ old('phone', $otpPhone) }}">

                <x-input
                    name="code"
                    label="One-time code"
                    type="text"
                    :required="true"
                    inputmode="numeric"
                    maxlength="6"
                    autocomplete="one-time-code"
                    autocapitalize="none"
                    spellcheck="false"
                    :hint="'Sent by SMS to '.App\Support\PhoneNumber::mask($otpPhone).'. It expires in '.config('otp.ttl_minutes', 10).' minutes.'"
                />

                <label class="checkbox-field text-sm">
                    <input type="checkbox" name="remember" value="1">
                    <span>Keep me signed in on this device</span>
                </label>

                <x-button variant="solid" size="lg" block type="submit">
                    Verify and sign in
                    <x-icon name="arrow-right" :size="16" />
                </x-button>
            </form>

            <form method="POST" action="{{ route('candidate.otp.request') }}" style="margin-top: var(--space-3);">
                @csrf
                <input type="hidden" name="phone" value="{{ old('phone', $otpPhone) }}">
                <x-button
                    variant="ghost"
                    size="sm"
                    block
                    type="submit"
                    data-otp-resend
                    data-otp-seconds="{{ config('otp.resend_seconds', 60) }}"
                >
                    Resend code
                </x-button>
            </form>

            <p class="auth-switch">
                <a href="{{ route('candidate.login', ['method' => 'otp', 'reset' => 1]) }}">Use a different mobile number</a>
            </p>
        @else
            <form method="POST" action="{{ route('candidate.otp.request') }}" class="auth-form" data-loading-form>
                @csrf

                <x-input
                    name="phone"
                    label="Mobile number"
                    type="tel"
                    :required="true"
                    inputmode="numeric"
                    maxlength="10"
                    autocomplete="tel"
                    hint="Enter the 10-digit mobile number linked to your GYRS profile."
                />

                <x-button variant="solid" size="lg" block type="submit">
                    Send one-time code
                    <x-icon name="phone" :size="16" />
                </x-button>
            </form>
        @endif

        <div class="auth-divider"><span>or</span></div>

        <p class="auth-switch">
            Prefer a password?
            <a href="{{ route('candidate.login') }}">Use password sign-in</a>
        </p>

        <details class="auth-note">
            <summary>
                <span>About mobile OTP sign-in</span>
                <x-icon name="chevron-down" :size="16" />
            </summary>
            <p>
                Codes are sent only to your registered mobile number and are never shown on screen. Each code is valid for
                {{ config('otp.ttl_minutes', 10) }} minutes and can be used once. If your number is not registered yet,
                create or update your candidate profile first.
            </p>
        </details>

        <p class="auth-switch">
            New to GYRS?
            <a href="{{ route('candidate.register') }}">Create a free candidate account</a>
        </p>
    @else
        <div class="auth-panel-head">
            <p class="auth-kicker"><span class="auth-kicker-dot"></span> Candidate gateway</p>
            <h1 class="auth-title-lg">Welcome back</h1>
            <p class="auth-lede">Sign in to continue your application journey, revisit saved roles and track every response.</p>
        </div>

        @include('partials.auth-tabs', ['active' => 'login'])

        <form method="POST" action="{{ route('candidate.login') }}" id="candidate-login-form" class="auth-form" data-loading-form>
            @csrf

            <x-input
                name="identifier"
                label="Mobile number or email"
                type="text"
                :required="true"
                autocomplete="username"
                autocapitalize="none"
                spellcheck="false"
                hint="Use the 10-digit mobile number or email address linked to your profile."
            />

            <x-input
                name="password"
                label="Password"
                type="password"
                :required="true"
                :toggle="true"
                autocomplete="current-password"
            />

            <label class="checkbox-field text-sm">
                <input type="checkbox" name="remember" value="1">
                <span>Keep me signed in on this device</span>
            </label>

            <x-button variant="solid" size="lg" block type="submit">
                Sign in
                <x-icon name="arrow-right" :size="16" />
            </x-button>
        </form>

        <p class="auth-switch">
            <a href="{{ route('candidate.login', ['method' => 'otp']) }}">Sign in with a mobile OTP instead</a>
        </p>

        <div class="auth-divider"><span>or</span></div>

        <a href="{{ route('auth.google.redirect', ['role' => 'candidate']) }}" class="btn btn-google">
            <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59a14.5 14.5 0 0 1 0-9.18l-7.98-6.19a24.01 24.01 0 0 0 0 21.56l7.98-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
            <span>Continue with Google</span>
        </a>

        <details class="auth-note">
            <summary>
                <span>About mobile OTP sign-in</span>
                <x-icon name="chevron-down" :size="16" />
            </summary>
            <p>
                OTP sign-in is available once your mobile number is verified. Codes are sent only to your registered
                number and are never shown on screen. You can verify your number from the candidate dashboard after
                signing in with your password.
            </p>
        </details>

        <p class="auth-switch">
            New to GYRS?
            <a href="{{ route('candidate.register') }}">Create a free candidate account</a>
        </p>
    @endif
@endsection
