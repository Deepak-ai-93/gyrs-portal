@extends('layouts.admin')

@section('title', 'Job postings — GYRS Portal admin')

@php
    $keyword = $filters['keyword'] ?? '';
    $activeDistrict = (string) ($filters['district'] ?? '');
    $activeStatus = $filters['status'] ?? '';
    $statuses = ['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed', 'archived' => 'Archived'];
    $districtLabel = fn ($district) => is_object($district) ? (string) ($district->name ?? '') : (string) $district;
    $districtValue = fn ($district) => is_object($district) ? (string) ($district->id ?? $district->slug ?? '') : (string) $district;
    $canManageJobs = (bool) auth()->user()?->hasAdminPermission(\App\Support\AdminPermissions::JOBS_MANAGE);
@endphp

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Job postings</h1>
                    <p class="lead">Create, publish, edit and archive vacancies shown to candidates.</p>
                </div>
                @if ($canManageJobs)
                    <x-button variant="outline" :href="route('admin.jobs.create')">
                        <x-icon name="plus" :size="16" /> Post new job
                    </x-button>
                @endif
            </div>

            <form class="toolbar" method="GET" action="{{ route('admin.jobs.index') }}" data-loading-form>
                <div class="form-field is-grow">
                    <label class="form-label" for="job-keyword">Search</label>
                    <input class="form-control" type="search" id="job-keyword" name="keyword" value="{{ $keyword }}" placeholder="Job title or department">
                </div>
                <div class="form-field">
                    <label class="form-label" for="job-district">District</label>
                    <select class="form-select" id="job-district" name="district">
                        <option value="">All districts</option>
                        @foreach ($districts as $district)
                            <option value="{{ $districtValue($district) }}" @selected($activeDistrict === $districtValue($district))>{{ $districtLabel($district) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="form-label" for="job-status">Status</label>
                    <select class="form-select" id="job-status" name="status">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="toolbar-actions">
                    <x-button variant="outline" type="submit">Apply filters</x-button>
                    <x-button variant="ghost" :href="route('admin.jobs.index')">Reset</x-button>
                </div>
            </form>

            @if ($jobs->isEmpty())
                <x-empty-state
                    icon="briefcase"
                    title="No job postings found"
                    message="Adjust your filters, or create the first job posting to publish vacancies to candidates."
                >
                    <x-slot:action>
                        @if ($canManageJobs)
                            <x-button variant="outline" :href="route('admin.jobs.create')">Post new job</x-button>
                        @endif
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <caption class="sr-only">Job postings</caption>
                        <thead>
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Department</th>
                                <th scope="col">District</th>
                                <th scope="col">Type</th>
                                <th scope="col" class="num">Posts</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jobs as $job)
                                <tr>
                                    <td><a href="{{ route('admin.jobs.show', $job) }}">{{ Str::limit($job->title, 44) }}</a></td>
                                    <td>{{ Str::limit($job->department, 32) }}</td>
                                    <td>{{ $job->district->name ?? '—' }}</td>
                                    <td>{{ $job->employment_type ?? '—' }}</td>
                                    <td class="num">{{ $job->total_posts ?? '—' }}</td>
                                    <td><x-status-badge :status="$job->status" /></td>
                                    <td>
                                        <div class="actions">
                                            <x-button variant="ghost" size="sm" :href="route('admin.jobs.show', $job)"><x-icon name="eye" :size="15" /> View</x-button>
                                            @if ($canManageJobs)
                                                <x-button variant="ghost" size="sm" :href="route('admin.jobs.edit', $job)"><x-icon name="edit" :size="15" /> Edit</x-button>
                                                <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" data-confirm="Delete the job posting &quot;{{ $job->title }}&quot;? This cannot be undone.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-button variant="danger" size="sm" type="submit"><x-icon name="trash" :size="15" /> Delete</x-button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$jobs" />
            @endif
        </div>
    </section>
@endsection
