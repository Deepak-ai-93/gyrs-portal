@extends('layouts.admin')

@section('title', 'Administrators — GYRS Portal admin')

@php($currentUser = auth()->user())

@section('content')
    <section class="section">
        <div class="container">
            <div class="page-head">
                <div>
                    <h1 style="font-size:clamp(1.75rem,3vw,2.25rem);">Administrators</h1>
                    <p class="lead">Create administrator accounts and control exactly which sections of the portal they can manage.</p>
                </div>
                <x-button variant="outline" :href="route('admin.administrators.create')">
                    <x-icon name="plus" :size="16" /> Add administrator
                </x-button>
            </div>

            <form class="toolbar" method="GET" action="{{ route('admin.administrators.index') }}" data-loading-form>
                <div class="form-field is-grow">
                    <label class="form-label" for="admin-keyword">Search</label>
                    <input class="form-control" type="search" id="admin-keyword" name="keyword" value="{{ $keyword }}" placeholder="Name or email address">
                </div>
                <div class="toolbar-actions">
                    <x-button variant="outline" type="submit">Search</x-button>
                    <x-button variant="ghost" :href="route('admin.administrators.index')">Reset</x-button>
                </div>
            </form>

            @if ($administrators->isEmpty())
                <x-empty-state
                    icon="shield"
                    title="No administrator accounts found"
                    message="Add an administrator to delegate part of the portal's workload."
                />
            @else
                <div class="table-wrap">
                    <table class="table">
                        <caption class="sr-only">Administrator accounts</caption>
                        <thead>
                            <tr>
                                <th scope="col">Administrator</th>
                                <th scope="col">Role</th>
                                <th scope="col">Access</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($administrators as $administrator)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.administrators.edit', $administrator) }}">{{ $administrator->name }}</a>
                                        @if ($administrator->is($currentUser))
                                            <x-badge variant="success">You</x-badge>
                                        @endif
                                        <div class="text-sm muted">{{ $administrator->email }}</div>
                                    </td>
                                    <td>
                                        @if ($administrator->isSuperAdmin())
                                            <x-badge variant="info"><x-icon name="shield" :size="13" /> Super administrator</x-badge>
                                        @else
                                            <x-badge variant="neutral">Administrator</x-badge>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($administrator->isSuperAdmin())
                                            <span class="text-sm muted">Full access to every section.</span>
                                        @elseif (count($administrator->adminPermissions()) === 0)
                                            <span class="text-sm muted">No access granted yet.</span>
                                        @else
                                            <div class="cluster" style="gap:6px;">
                                                @foreach ($administrator->adminPermissions() as $permission)
                                                    <x-badge variant="neutral">{{ $permissionLabels[$permission] ?? $permission }}</x-badge>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="cluster">
                                            <x-button variant="ghost" size="sm" :href="route('admin.administrators.edit', $administrator)">
                                                <x-icon name="edit" :size="15" /> Edit
                                            </x-button>
                                            @unless ($administrator->is($currentUser) || $administrator->isSuperAdmin())
                                                <form method="POST" action="{{ route('admin.administrators.destroy', $administrator) }}" data-confirm="Remove the administrator account for {{ $administrator->name }}?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-button variant="ghost" size="sm" type="submit"><x-icon name="trash" :size="15" /> Remove</x-button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$administrators" />
            @endif
        </div>
    </section>
@endsection
