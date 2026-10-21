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

            @if (session('error'))
                <x-alert variant="danger">{{ session('error') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('candidate.login') }}" id="candidate-login-form" data-loading-form>
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

            <div style="display:flex; align-items:center; gap:var(--space-3); margin:var(--space-4) 0;">
                <hr style="flex:1; border:none; border-top:1px solid var(--border);">
                <span class="text-sm muted">or</span>
                <hr style="flex:1; border:none; border-top:1px solid var(--border);">
            </div>

            <a href="{{ route('auth.google.redirect', ['role' => 'candidate']) }}" class="btn btn-outline" style="display:flex; align-items:center; justify-content:center; gap:var(--space-2); width:100%; padding:var(--space-3); text-decoration:none; border-radius:var(--radius); border:1px solid var(--border);">
                <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59a14.5 14.5 0 0 1 0-9.18l-7.98-6.19a24.01 24.01 0 0 0 0 21.56l7.98-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                Sign in with Google
            </a>

            <details class="panel" style="margin-top:var(--space-4);">
                <summary class="strong" style="cursor:pointer;">Prefer mobile OTP access?</summary>
                <p class="text-sm muted" style="margin:var(--space-3) 0 0;">
                    Mobile OTP sign-in is available to candidates whose number is verified by the employment office. OTP codes are sent
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
