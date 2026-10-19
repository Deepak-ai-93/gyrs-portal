@extends('layouts.candidate')

@section('title', 'Interested jobs — GYRS')

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Interested jobs</h1>
                    <p class="lead">Every vacancy you have marked as interested, saved in one place for quick access.</p>
                </div>
                <x-button variant="outline" :href="route('home') . '#job-explorer'">
                    <x-icon name="search" :size="16" /> Explore more vacancies
                </x-button>
            </div>

            @if ($jobs->isEmpty())
                <x-empty-state
                    icon="sparkles"
                    title="You have not marked any jobs as interested yet"
                    message="Browse verified government and private vacancies and choose “Mark interested” to save them here."
                >
                    <x-slot:action>
                        <x-button variant="outline" :href="route('home') . '#job-explorer'">Find your first vacancy</x-button>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="jobs-grid">
                    @foreach ($jobs as $job)
                        <x-job-card :job="$job" :saved="true" />
                    @endforeach
                </div>

                <x-pagination :paginator="$jobs" />
            @endif
        </div>
    </section>
@endsection
