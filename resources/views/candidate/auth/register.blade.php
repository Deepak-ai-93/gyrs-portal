@extends('layouts.app')

@section('title', 'Candidate registration — GYRS')

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

@section('content')
    <div class="auth-wrap">
        <div class="auth-card is-wide">
            <div class="auth-head">
                <span class="auth-mark"><x-icon name="user" :size="26" /></span>
                <h1 class="auth-title">Create your candidate account</h1>
                <p class="auth-sub">Registration is free and takes only a few minutes.</p>
            </div>

            <ol class="auth-steps">
                <li>Add your personal, contact and qualification details.</li>
                <li>Set a password to secure your account.</li>
                <li>Sign in to complete your dossier and start applying.</li>
            </ol>

            <form method="POST" action="{{ route('candidate.register') }}" data-loading-form>
                @csrf

                <fieldset class="form-section">
                    <legend>Personal details</legend>

                    <x-input name="name" label="Full name (as per Aadhaar)" :required="true" autocomplete="name" />
                    <p class="text-sm muted" style="margin-top:var(--space-2);">Your GYRS enrollment ID is generated automatically after registration.</p>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Contact details</legend>

                    <div class="form-grid">
                        <x-input name="phone" label="Mobile number" type="tel" :required="true" autocomplete="tel" hint="10-digit number, used for OTP access." />
                        <x-input name="email" label="Email address" type="email" autocomplete="email" hint="Optional, but recommended for alerts." />
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
                            placeholder="Select your qualification"
                            :required="true"
                        />
                        <x-input name="cgpa" label="CPI / CGPA" hint="Optional — average marks or grade point." />
                    </div>

                    <p class="form-label" id="skills-label">Key skills</p>
                    <div class="form-grid">
                        <x-input name="skills[]" label="Skill 1" aria-labelledby="skills-label" hint="For example Python, SQL or AutoCAD." />
                        <x-input name="skills[]" label="Skill 2" aria-labelledby="skills-label" />
                        <x-input name="skills[]" label="Skill 3" aria-labelledby="skills-label" />
                    </div>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Account security</legend>

                    <div class="form-grid">
                        <x-input name="password" label="Password" type="password" :required="true" autocomplete="new-password" hint="At least 8 characters." />
                        <x-input name="password_confirmation" label="Confirm password" type="password" :required="true" autocomplete="new-password" />
                    </div>

                    <label class="checkbox-field text-sm">
                        <input type="checkbox" name="terms" value="1" required>
                        <span>I confirm the information provided is accurate and agree to the GYRS terms of use.</span>
                    </label>
                </fieldset>

                <div class="form-actions">
                    <x-button variant="ghost" :href="route('candidate.login')">Already registered? Sign in</x-button>
                    <x-button variant="outline" type="submit">Register free account</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
