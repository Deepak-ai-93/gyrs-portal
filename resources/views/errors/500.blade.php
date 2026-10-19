@extends('layouts.app')

@section('title', 'Server error — GYRS')

@section('content')
    <div class="error-page">
        <div class="error-code" aria-hidden="true">500</div>
        <h1 style="font-size:1.75rem;">Something went wrong</h1>
        <p class="muted">A server error prevented this page from loading. Please retry in a moment. If the problem continues, contact the portal support team.</p>
        <div class="cluster" style="justify-content:center;">
            <button type="button" class="btn btn-outline" onclick="window.location.reload()">Retry</button>
            <x-button variant="ghost" :href="route('home')">Return to home</x-button>
        </div>
    </div>
@endsection
