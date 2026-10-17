@extends('layouts.admin')

@section('title', 'Post new job — GYRS Portal admin')

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Post a new job</h1>
                    <p class="lead">Complete the structured form below. Fields marked with an asterisk are required.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.jobs.store') }}" enctype="multipart/form-data" data-guard-unsaved data-loading-form>
                @csrf

                @include('admin.jobs._form', ['job' => null])

                <div class="form-actions">
                    <x-button variant="ghost" :href="route('admin.jobs.index')">Cancel</x-button>
                    <x-button variant="outline" type="submit">Save job posting</x-button>
                </div>
            </form>
        </div>
    </section>
@endsection
