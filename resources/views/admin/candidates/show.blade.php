@extends('layouts.admin')

@section('title', ($candidate->name ?? 'Candidate') . ' — GYRS Portal admin')

@php
    $applications = $candidate->applications ?? collect();
    $initials = collect(explode(' ', trim((string) $candidate->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
        ->implode('');
    $statuses = [
        'pending' => 'Pending',
        'verified' => 'Verified',
        'under_review' => 'Under review',
        'shortlisted' => 'Shortlisted',
        'placed' => 'Placed',
        'rejected' => 'Rejected',
    ];
@endphp

@section('content')
    <section class="section">
        <div class="container">
            <ol class="breadcrumbs">
                <li><a href="{{ route('admin.candidates.index') }}">Candidate roster</a></li>
                <li aria-current="page">{{ $candidate->name }}</li>
            </ol>

            <div class="identity-bar" style="margin-bottom:var(--space-6);">
                <div class="identity-main">
                    <span class="avatar" aria-hidden="true">{{ $initials ?: 'GY' }}</span>
                    <div>
                        <div class="identity-name">
                            <h1>{{ $candidate->name }}</h1>
                            @if ($candidate->enrollment_id)
                                <span class="badge" style="font-variant-numeric:tabular-nums;">{{ $candidate->enrollment_id }}</span>
                            @endif
                            <x-status-badge :status="$candidate->status ?? 'pending'" />
                        </div>
                        <div class="identity-meta">
                            @if ($candidate->qualification)<span><x-icon name="award" :size="14" /> {{ $candidate->qualification }}</span>@endif
                            @if ($candidate->district)<span><x-icon name="map-pin" :size="14" /> {{ $candidate->district->name }}</span>@endif
                        </div>
                    </div>
                </div>
                <div class="identity-actions">
                    <x-button variant="ghost" size="sm" onclick="window.print()"><x-icon name="download" :size="15" /> Print dossier</x-button>
                    <x-button variant="ghost" size="sm" :href="route('admin.candidates.index')">Back to roster</x-button>
                </div>
            </div>

            <div class="dossier-grid">
                <div>
                    <x-card title="Candidate details" meta="Verified registration data">
                        <ul class="data-list" style="grid-template-columns:1fr;">
                            <li><span class="data-label">Mobile</span><span class="data-value">{{ $candidate->phone ?? '—' }}</span></li>
                            <li><span class="data-label">Email</span><span class="data-value">{{ $candidate->email ?? '—' }}</span></li>
                            <li><span class="data-label">Qualification</span><span class="data-value">{{ $candidate->qualification ?? '—' }}</span></li>
                            <li><span class="data-label">CPI / CGPA</span><span class="data-value">{{ $candidate->cgpa ?? '—' }}</span></li>
                            <li><span class="data-label">District</span><span class="data-value">{{ $candidate->district->name ?? '—' }}</span></li>
                            <li><span class="data-label">Registered</span><span class="data-value">{{ $candidate->created_at ? \Illuminate\Support\Carbon::parse($candidate->created_at)->format('d M Y') : '—' }}</span></li>
                        </ul>
                    </x-card>

                    @if (auth()->user()?->hasAdminPermission(\App\Support\AdminPermissions::CANDIDATES_VERIFY))
                        <x-card title="Update verification status" meta="Changes are recorded in the audit trail" style="margin-top:var(--space-4);">
                            <form method="POST" action="{{ route('admin.candidates.status', $candidate) }}" data-loading-form>
                                @csrf
                                @method('PATCH')
                                <x-select name="status" label="Status" :options="$statuses" :value="$candidate->status ?? 'pending'" :required="true" />
                                <x-textarea name="notes" label="Notes" hint="Optional internal note for this status change." :rows="3" />
                                <div class="form-actions" style="border-top:0;padding-top:0;">
                                    <x-button variant="outline" type="submit">Save status</x-button>
                                </div>
                            </form>
                        </x-card>
                    @endif
                </div>

                <div>
                    <x-card title="Skills" :meta="method_exists($candidate, 'skills') ? $candidate->skills->count() . ' skills on record' : null">
                        @if (method_exists($candidate, 'skills') && $candidate->skills->isNotEmpty())
                            <div class="badge-row">
                                @foreach ($candidate->skills as $skill)
                                    <span class="badge">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm muted" style="margin:0;">No skills recorded for this candidate.</p>
                        @endif
                    </x-card>

                    <x-card title="Applications" :meta="$applications->count() . ' total'" style="margin-top:var(--space-4);">
                        @if ($applications->isEmpty())
                            <p class="text-sm muted" style="margin:0;">This candidate has not applied for any vacancy yet.</p>
                        @else
                            <div class="table-wrap">
                                <table class="table">
                                    <caption class="sr-only">Candidate applications</caption>
                                    <thead>
                                        <tr>
                                            <th scope="col">Vacancy</th>
                                            <th scope="col">Applied</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($applications as $application)
                                            <tr>
                                                <td>{{ $application->job->title ?? '—' }}</td>
                                                <td>{{ $application->applied_at ? \Illuminate\Support\Carbon::parse($application->applied_at)->format('d M Y') : '—' }}</td>
                                                <td><x-status-badge :status="$application->status" /></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </x-card>
                </div>
            </div>
        </div>
    </section>
@endsection
