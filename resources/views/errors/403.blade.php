@extends('layouts.app')

@section('title', 'Access forbidden — GYRS')

@section('content')
    <div class="error-page">
        <div class="error-code" aria-hidden="true">403</div>
        <h1 style="font-size:1.75rem;">Access forbidden</h1>
        <p class="muted">You do not have permission to view this page. If you believe this is a mistake, sign in with the correct account or contact the Directorate systems team.</p>
        <div class="cluster" style="justify-content:center;">
            <x-button variant="outline" :href="route('home')">Return to home</x-button>
            <x-button variant="ghost" :href="route('candidate.login')">Candidate sign in</x-button>
        </div>
    </div>
@endsection
