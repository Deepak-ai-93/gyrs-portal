@extends('layouts.auth')

@section('title', 'Candidate registration — GYRS')
@section('panel_class', 'is-wide')

@php
    $districtOptions = isset($districts) ? $districts : [];
    $qualifications = [
        'B.Tech / B.E. (Computer / IT)' => 'B.Tech / B.E. (Computer / IT)',
        'B.Tech / B.E. (Civil)' => 'B.Tech / B.E. (Civil)',
        'B.Tech / B.E. (Mechanical / Electrical)' => 'B.Tech / B.E. (Mechanical / Electrical)',
        'MCA / M.Sc (Computer)' => 'MCA / M.Sc (Computer)',
        'ITI / Diploma (Technical)' => 'ITI / Diploma (Technical)',
        'B.Com / BBA / Accounting' => 'B.Com / BBA / Accounting',
        'B.A. / B.Sc / General Graduate' => 'B.A. / B.Sc / General Graduate',
        '12th Pass' => '12th Pass',
    ];
@endphp

@section('auth-content')
    <div class="auth-panel-head">
        <p class="auth-kicker"><span class="auth-kicker-dot"></span> Free registration</p>
        <h1 class="auth-title-lg">Create your candidate account</h1>
        <p class="auth-lede">Four short sections — personal details, contact information, education and account security. It takes about three minutes.</p>
    </div>

    @include('partials.auth-tabs', ['active' => 'register'])

    <form method="POST" action="{{ route('candidate.register') }}" class="auth-form" data-loading-form>
        @csrf

        <fieldset class="form-section">
            <legend>Personal details</legend>

            <x-input name="name" label="Full name (as per Aadhaar)" :required="true" autocomplete="name" />
            <p class="text-sm muted" style="margin-top:var(--space-2);">Your GYRS enrollment ID is generated automatically after registration.</p>
        </fieldset>

        <fieldset class="form-section">
            <legend>Contact details</legend>

            <div class="form-grid">
                <x-input name="phone" label="Mobile number" type="tel" :required="true" autocomplete="tel" inputmode="numeric" maxlength="10" hint="10-digit number, used for OTP access." />
                <x-input name="email" label="Email address" type="email" autocomplete="email" autocapitalize="none" spellcheck="false" hint="Optional, but recommended for alerts." />
            </div>

            <x-select
                name="district_id"
                label="Home district"
                :options="$districtOptions"
                placeholder="Select your district"
                :required="true"
            />
        </fieldset>

        <fieldset class="form-section">
            <legend>Education &amp; skills</legend>

            <div class="form-grid">
                <x-select
                    name="qualification"
                    label="Highest qualification"
                    :options="$qualifications"
                    placeholder="Select qualification"
                    :required="true"
                />
                <x-input name="cgpa" label="CPI / CGPA" hint="Optional — average marks or grade point." />
            </div>

            <p class="form-label" id="skills-label">Key skills</p>
            <div class="form-grid form-grid-3">
                <x-input name="skills[]" label="Skill 1" aria-labelledby="skills-label" hint="For example Python, SQL or AutoCAD." />
                <x-input name="skills[]" label="Skill 2" aria-labelledby="skills-label" />
                <x-input name="skills[]" label="Skill 3" aria-labelledby="skills-label" />
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Account security</legend>

            <div class="form-grid">
                <x-input name="password" label="Password" type="password" :required="true" :toggle="true" autocomplete="new-password" hint="At least 8 characters." />
                <x-input name="password_confirmation" label="Confirm password" type="password" :required="true" :toggle="true" autocomplete="new-password" />
            </div>

            <label class="checkbox-field text-sm">
                <input type="checkbox" name="terms" value="1" required>
                <span>I confirm the information provided is accurate and agree to the GYRS terms of use.</span>
            </label>
        </fieldset>

        <x-button variant="solid" size="lg" block type="submit">
            Create my account
            <x-icon name="arrow-right" :size="16" />
        </x-button>
    </form>

    <p class="auth-switch">
        Already registered?
        <a href="{{ route('candidate.login') }}">Sign in instead</a>
    </p>
@endsection
