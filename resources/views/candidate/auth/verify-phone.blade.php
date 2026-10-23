@extends('layouts.candidate')

@section('title', 'Verify your mobile number — GYRS')

@section('content')
    <section class="section">
        <div class="container" style="max-width: 640px;">
            <x-card title="Verify your mobile number" meta="One-time SMS code">
                @if ($phone)
                    <p class="muted">
                        @if ($hasActiveCode)
                            Enter the {{ config('otp.code_length', 6) }}-digit code sent to
                            <strong>{{ $maskedPhone }}</strong>. Verifying your number enables mobile OTP sign-in and
                            helps the employment office reach you about applications.
                        @else
                            We will send a {{ config('otp.code_length', 6) }}-digit code to
                            <strong>{{ $maskedPhone }}</strong>. Verifying your number enables mobile OTP sign-in and
                            helps the employment office reach you about applications.
                        @endif
                    </p>

                    @if ($hasActiveCode)
                        <form method="POST" action="{{ route('candidate.phone.confirm') }}" class="auth-form" data-loading-form>
                            @csrf
                            <input type="hidden" name="phone" value="{{ $phone }}">

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
                                :hint="'The code expires in '.config('otp.ttl_minutes', 10).' minutes and can be used once.'"
                            />

                            <x-button variant="solid" size="lg" block type="submit">
                                Verify mobile number
                                <x-icon name="check-circle" :size="16" />
                            </x-button>
                        </form>

                        <form method="POST" action="{{ route('candidate.phone.send') }}" style="margin-top: var(--space-3);">
                            @csrf
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
                    @else
                        <form method="POST" action="{{ route('candidate.phone.send') }}" class="auth-form" data-loading-form>
                            @csrf
                            <x-button variant="solid" size="lg" block type="submit">
                                Send verification code
                                <x-icon name="phone" :size="16" />
                            </x-button>
                        </form>
                    @endif
                @else
                    <x-alert variant="warning">
                        Add a valid 10-digit mobile number to your profile before verifying it.
                    </x-alert>
                @endif

                <p class="auth-switch" style="margin-top: var(--space-5);">
                    <a href="{{ route('candidate.dashboard') }}">Skip for now and go to dashboard</a>
                </p>
            </x-card>
        </div>
    </section>
@endsection
