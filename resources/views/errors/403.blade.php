@extends('layouts.app')

@section('title', 'Access forbidden — GYRS')

@section('content')
    <div class="error-page">
        <div class="error-code" aria-hidden="true">403</div>
        <h1 style="font-size:1.75rem;">Access forbidden</h1>

        @auth
            @if (auth()->user()->isAdmin())
                <p class="muted">This action needs a candidate account. You are currently signed in as an administrator, so you cannot perform it here.</p>
                <p class="muted">Sign out and sign in with a candidate account if you were trying to apply for a job or mark it as interested.</p>
                <div class="cluster" style="justify-content:center;">
                    <x-button variant="outline" :href="route('admin.dashboard')">Back to admin dashboard</x-button>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <x-button variant="ghost" type="submit">Sign out</x-button>
                    </form>
                </div>
            @else
                <p class="muted">This action needs an administrator account. You are currently signed in as a candidate, so you cannot perform it here.</p>
                <div class="cluster" style="justify-content:center;">
                    <x-button variant="outline" :href="route('candidate.dashboard')">Back to my dashboard</x-button>
                    <form method="POST" action="{{ route('candidate.logout') }}">
                        @csrf
                        <x-button variant="ghost" type="submit">Sign out</x-button>
                    </form>
                </div>
            @endif
        @else
            <p class="muted">You do not have permission to view this page. Sign in with the correct account or contact the Directorate systems team.</p>
            <div class="cluster" style="justify-content:center;">
                <x-button variant="outline" :href="route('home')">Return to home</x-button>
                <x-button variant="ghost" :href="route('candidate.login')">Candidate sign in</x-button>
            </div>
        @endauth
    </div>
@endsection
