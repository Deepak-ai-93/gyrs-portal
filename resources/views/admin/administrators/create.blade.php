@extends('layouts.admin')

@section('title', 'Add administrator — GYRS Portal admin')

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Add an administrator</h1>
                    <p class="lead">Create an account and grant only the sections it needs to manage.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.administrators.store') }}" data-loading-form>
                @csrf

                @include('admin.administrators._form')

                <div class="form-actions">
                    <x-button variant="ghost" :href="route('admin.administrators.index')">Cancel</x-button>
                    <x-button variant="outline" type="submit">Create administrator</x-button>
                </div>
            </form>
        </div>
    </section>
@endsection
