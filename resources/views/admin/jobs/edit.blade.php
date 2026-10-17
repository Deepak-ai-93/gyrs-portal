@extends('layouts.admin')

@section('title', 'Edit job — GYRS Portal admin')

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Edit job posting</h1>
                    <p class="lead">{{ $job->title }} &middot; {{ $job->department }}</p>
                </div>
                <div class="cluster">
                    <x-status-badge :status="$job->status" />
                    <x-button variant="ghost" :href="route('admin.jobs.show', $job)">View public page</x-button>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.jobs.update', $job) }}" enctype="multipart/form-data" data-guard-unsaved data-loading-form>
                @csrf
                @method('PUT')

                @include('admin.jobs._form', ['job' => $job])

                <div class="form-actions">
                    <x-button variant="ghost" :href="route('admin.jobs.index')">Cancel</x-button>
                    <x-button variant="outline" type="submit">Save changes</x-button>
                </div>
            </form>
        </div>
    </section>
@endsection
