@extends('layouts.admin')

@section('title', 'Candidate roster — GYRS Portal admin')

@php
    $keyword = $filters['keyword'] ?? '';
    $activeDistrict = (string) ($filters['district'] ?? '');
    $activeStatus = $filters['status'] ?? '';
    $statuses = [
        'pending' => 'Pending',
        'verified' => 'Verified',
        'under_review' => 'Under review',
        'shortlisted' => 'Shortlisted',
        'placed' => 'Placed',
        'rejected' => 'Rejected',
    ];
    $districtLabel = fn ($district) => is_object($district) ? (string) ($district->name ?? '') : (string) $district;
    $districtValue = fn ($district) => is_object($district) ? (string) ($district->id ?? $district->slug ?? '') : (string) $district;
    $canVerifyCandidates = (bool) auth()->user()?->hasAdminPermission(\App\Support\AdminPermissions::CANDIDATES_VERIFY);
    $canExportCandidates = (bool) auth()->user()?->hasAdminPermission(\App\Support\AdminPermissions::EXPORT_CANDIDATES);
@endphp

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Candidate roster</h1>
                    <p class="lead">Supervise registered candidates, verification status and placement records.</p>
                </div>
                @if ($canExportCandidates)
                    <x-button variant="outline" :href="route('admin.exports.candidates')">
                        <x-icon name="download" :size="16" /> Export roster CSV
                    </x-button>
                @endif
            </div>

            <form class="toolbar" method="GET" action="{{ route('admin.candidates.index') }}" data-loading-form>
                <div class="form-field is-grow">
                    <label class="form-label" for="candidate-keyword">Search</label>
                    <input class="form-control" type="search" id="candidate-keyword" name="keyword" value="{{ $keyword }}" placeholder="Name, enrollment ID or skill">
                </div>
                <div class="form-field">
                    <label class="form-label" for="candidate-district">District</label>
                    <select class="form-select" id="candidate-district" name="district">
                        <option value="">All districts</option>
                        @foreach ($districts as $district)
                            <option value="{{ $districtValue($district) }}" @selected($activeDistrict === $districtValue($district))>{{ $districtLabel($district) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="form-label" for="candidate-status">Status</label>
                    <select class="form-select" id="candidate-status" name="status">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="toolbar-actions">
                    <x-button variant="outline" type="submit">Apply filters</x-button>
                    <x-button variant="ghost" :href="route('admin.candidates.index')">Reset</x-button>
                </div>
            </form>

            @if ($candidates->isEmpty())
                <x-empty-state
                    icon="users"
                    title="No candidates found"
                    message="Adjust the filters, or check back once candidates register on the portal."
                />
            @else
                <div class="table-wrap">
                    <table class="table">
                        <caption class="sr-only">Candidate roster</caption>
                        <thead>
                            <tr>
                                <th scope="col">Enrollment ID</th>
                                <th scope="col">Candidate</th>
                                <th scope="col">Qualification</th>
                                <th scope="col">District</th>
                                <th scope="col">Status</th>
                                <th scope="col">Update status</th>
                                <th scope="col">Dossier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($candidates as $candidate)
                                <tr>
                                    <td style="font-variant-numeric:tabular-nums;">{{ $candidate->enrollment_id ?? '—' }}</td>
                                    <td>
                                        <a href="{{ route('admin.candidates.show', $candidate) }}">{{ $candidate->name }}</a>
                                        <div class="text-sm muted">{{ $candidate->phone ?? $candidate->email ?? '' }}</div>
                                    </td>
                                    <td>{{ Str::limit($candidate->qualification ?? '—', 28) }}</td>
                                    <td>{{ $candidate->district->name ?? '—' }}</td>
                                    <td><x-status-badge :status="$candidate->status ?? 'pending'" /></td>
                                    <td>
                                        @if ($canVerifyCandidates)
                                            <form class="inline-status" method="POST" action="{{ route('admin.candidates.status', $candidate) }}">
                                                @csrf
                                                @method('PATCH')
                                                <label class="sr-only" for="status-{{ $candidate->id }}">Status for {{ $candidate->name }}</label>
                                                <select class="form-select" id="status-{{ $candidate->id }}" name="status">
                                                    @foreach ($statuses as $value => $label)
                                                        <option value="{{ $value }}" @selected(($candidate->status ?? 'pending') === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <x-button variant="ghost" size="sm" type="submit" title="Update status">Save</x-button>
                                            </form>
                                        @else
                                            <span class="text-sm muted">View only</span>
                                        @endif
                                    </td>
                                    <td>
                                        <x-button variant="ghost" size="sm" :href="route('admin.candidates.show', $candidate)"><x-icon name="eye" :size="15" /> Open</x-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$candidates" />
            @endif
        </div>
    </section>
@endsection
