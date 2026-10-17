@extends('layouts.app')

@section('title', 'Candidate sign in — GYRS')

@section('content')
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-head">
                <span class="auth-mark"><x-icon name="user" :size="26" /></span>
                <h1 class="auth-title">Candidate sign in</h1>
                <p class="auth-sub">Gujarat Youth Rojgar Setu (GYRS) &middot; Candidate gateway</p>
            </div>

            <x-alert variant="info">
                Sign in with the mobile number or email you registered, together with your password. If you do not have an
                account yet, you can <a href="{{ route('candidate.register') }}">register free</a> in a few minutes.
            </x-alert>

            <form method="POST" action="{{ route('candidate.login') }}" data-loading-form>
                @csrf

                <x-input
                    name="identifier"
                    label="Mobile number or email"
                    type="text"
                    :required="true"
                    autocomplete="username"
                    hint="Enter the 10-digit mobile number or email address linked to your profile."
                />

                <x-input
                    name="password"
                    label="Password"
                    type="password"
                    :required="true"
                    autocomplete="current-password"
                />

                <label class="checkbox-field text-sm">
                    <input type="checkbox" name="remember" value="1">
                    <span>Keep me signed in on this device</span>
                </label>

                <x-button variant="outline" block type="submit">Sign in to dashboard</x-button>
            </form>

            <details class="panel" style="margin-top:var(--space-4);">
                <summary class="strong" style="cursor:pointer;">Prefer mobile OTP access?</summary>
                <p class="text-sm muted" style="margin:var(--space-3) 0 0;">
                    Mobile OTP sign-in is available to candidates whose number is verified by the Directorate. OTP codes are sent
                    only to your registered mobile number — they are never displayed on screen. Until your number is verified,
                    please use password sign-in.
                </p>
            </details>

            <div class="auth-foot">
                New to GYRS? <a href="{{ route('candidate.register') }}">Create a free candidate account</a>
            </div>
        </div>
    </div>
@endsection
