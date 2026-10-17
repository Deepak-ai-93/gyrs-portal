@extends('layouts.app')

@section('title', 'Session expired — GYRS')

@section('content')
    <div class="error-page">
        <div class="error-code" aria-hidden="true">419</div>
        <h1 style="font-size:1.75rem;">Your session has expired</h1>
        <p class="muted">For your security, this page timed out. Please go back, refresh the page and try again.</p>
        <div class="cluster" style="justify-content:center;">
            <button type="button" class="btn btn-outline" onclick="window.history.back()">Go back</button>
            <x-button variant="ghost" :href="route('home')">Return to home</x-button>
        </div>
    </div>
@endsection
