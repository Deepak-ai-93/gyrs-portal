@extends('layouts.admin')

@section('title', 'Applications audit — GYRS Portal admin')

@php
    $keyword = $filters['keyword'] ?? '';
    $activeStatus = $filters['status'] ?? '';

    $statusOptions = [];
    foreach (($statuses ?? []) as $key => $value) {
        if (is_array($value)) {
            $statusOptions[(string) ($value['value'] ?? $key)] = $value['label'] ?? $value['name'] ?? '';
        } elseif (is_object($value)) {
            $statusOptions[(string) ($value->value ?? $value->id ?? $key)] = $value->label ?? $value->name ?? '';
        } elseif (is_string($key)) {
            $statusOptions[$key] = (string) $value;
        } else {
            $statusOptions[(string) $value] = Str::headline((string) $value);
        }
    }
    if (empty($statusOptions)) {
        $statusOptions = [
            'applied' => 'Applied',
            'under_review' => 'Under review',
            'shortlisted' => 'Shortlisted',
            'interview_call' => 'Interview call',
            'placed' => 'Placed',
            'rejected' => 'Rejected',
        ];
    }

    $canManageApplications = (bool) auth()->user()?->hasAdminPermission(\App\Support\AdminPermissions::APPLICATIONS_MANAGE);
    $canExportCandidates = (bool) auth()->user()?->hasAdminPermission(\App\Support\AdminPermissions::EXPORT_CANDIDATES);
@endphp

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Applications audit</h1>
                    <p class="lead">Statewide audit trail of candidate submissions and current recruitment stage.</p>
                </div>
                @if ($canExportCandidates)
                    <x-button variant="outline" :href="route('admin.exports.candidates')">
                        <x-icon name="download" :size="16" /> Export CSV
                    </x-button>
                @endif
            </div>

            <form class="toolbar" method="GET" action="{{ route('admin.applications.index') }}" data-loading-form>
                <div class="form-field is-grow">
                    <label class="form-label" for="application-keyword">Search</label>
                    <input class="form-control" type="search" id="application-keyword" name="keyword" value="{{ $keyword }}" placeholder="Candidate or vacancy">
                </div>
                <div class="form-field">
                    <label class="form-label" for="application-status">Status</label>
                    <select class="form-select" id="application-status" name="status">
                        <option value="">All statuses</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected((string) $activeStatus === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="toolbar-actions">
                    <x-button variant="outline" type="submit">Apply filters</x-button>
                    <x-button variant="ghost" :href="route('admin.applications.index')">Reset</x-button>
                </div>
            </form>

            @if ($applications->isEmpty())
                <x-empty-state
                    icon="inbox"
                    title="No applications found"
                    message="Adjust the filters, or wait for candidates to submit applications to open vacancies."
                />
            @else
                <div class="table-wrap">
                    <table class="table">
                        <caption class="sr-only">Applications audit trail</caption>
                        <thead>
                            <tr>
                                <th scope="col">Candidate</th>
                                <th scope="col">Vacancy</th>
                                <th scope="col">Applied</th>
                                <th scope="col">Status</th>
                                <th scope="col">Update status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $application)
                                <tr>
                                    <td>
                                        @if ($application->candidate)
                                            <a href="{{ route('admin.candidates.show', $application->candidate) }}">{{ $application->candidate->name }}</a>
                                            <div class="text-sm muted">{{ $application->candidate->enrollment_id ?? '' }}</div>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if ($application->job)
                                            <a href="{{ route('admin.jobs.show', $application->job) }}">{{ Str::limit($application->job->title, 40) }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $application->applied_at ? \Illuminate\Support\Carbon::parse($application->applied_at)->format('d M Y') : '—' }}</td>
                                    <td><x-status-badge :status="$application->status" /></td>
                                    <td>
                                        @if ($canManageApplications)
                                            <form class="inline-status" method="POST" action="{{ route('admin.applications.status', $application) }}">
                                                @csrf
                                                @method('PATCH')
                                                <label class="sr-only" for="application-status-{{ $application->id }}">Status</label>
                                                <select class="form-select" id="application-status-{{ $application->id }}" name="status">
                                                    @foreach ($statusOptions as $value => $label)
                                                        <option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <x-button variant="ghost" size="sm" type="submit" title="Update status">Save</x-button>
                                            </form>
                                        @else
                                            <span class="text-sm muted">View only</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$applications" />
            @endif
        </div>
    </section>
@endsection
