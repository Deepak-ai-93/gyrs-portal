@extends('layouts.app')

@section('title', 'GYRS Portal admin sign in')

@section('content')
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-head">
                <span class="auth-mark"><x-icon name="shield" :size="26" /></span>
                <h1 class="auth-title">GYRS Portal admin sign in</h1>
                <p class="auth-sub">Gujarat Youth Rojgar Setu &middot; administrative gateway</p>
            </div>

            <x-alert variant="warning">
                Restricted access. This gateway is for authorised administrators only. All administrative
                actions are recorded in the immutable audit trail.
            </x-alert>

            <form method="POST" action="{{ route('admin.login') }}" id="admin-login-form" data-loading-form>
                @csrf

                <x-input
                    name="email"
                    label="Official email address"
                    type="email"
                    :required="true"
                    autocomplete="username"
                />

                <x-input
                    name="password"
                    label="Administrative password"
                    type="password"
                    :required="true"
                    autocomplete="current-password"
                />

                <x-button variant="outline" block type="submit">
                    <x-icon name="lock" :size="16" /> Secure sign in
                </x-button>
            </form>

            <x-demo-credentials
                role="Administrator"
                login-label="Admin email"
                fill-name="email"
                form="admin-login-form"
                :login="config('gyrs.demo.admin_email')"
                :password="config('gyrs.demo.admin_password')"
            />

            <p class="text-sm muted" style="margin-top:var(--space-4);">
                If you cannot access your administrator account, contact the portal systems
                team.
            </p>

            <div class="auth-foot">
                <a href="{{ route('home') }}">Return to the public job board</a>
            </div>
        </div>
    </div>
@endsection
