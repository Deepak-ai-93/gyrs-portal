@extends('layouts.app')

@section('title', 'Page not found — GYRS')

@section('content')
    <div class="error-page">
        <div class="error-code" aria-hidden="true">404</div>
        <h1 style="font-size:1.75rem;">Page not found</h1>
        <p class="muted">The page you are looking for may have been moved, closed, or the vacancy is no longer published.</p>
        <div class="cluster" style="justify-content:center;">
            <x-button variant="outline" :href="route('home')">Browse open vacancies</x-button>
        </div>
    </div>
@endsection
