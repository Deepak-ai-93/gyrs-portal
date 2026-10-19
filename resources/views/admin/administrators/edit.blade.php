@extends('layouts.admin')

@section('title', 'Edit administrator — GYRS Portal admin')

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Edit administrator</h1>
                    <p class="lead">Update the account details and the sections this administrator can access.</p>
                </div>
                <x-badge variant="info">{{ $administrator->adminRoleLabel() }}</x-badge>
            </div>

            <form method="POST" action="{{ route('admin.administrators.update', $administrator) }}" data-loading-form>
                @csrf
                @method('PUT')

                @include('admin.administrators._form')

                <div class="form-actions">
                    <x-button variant="ghost" :href="route('admin.administrators.index')">Cancel</x-button>
                    <x-button variant="outline" type="submit">Save changes</x-button>
                </div>
            </form>
        </div>
    </section>
@endsection
