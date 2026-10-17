@props([
    'variant' => 'info',
    'dismissible' => false,
    'autodismiss' => false,
])

@php
    $icons = [
        'success' => 'check-circle',
        'warning' => 'alert',
        'danger' => 'alert',
        'info' => 'info',
    ];
    $role = in_array($variant, ['danger', 'warning'], true) ? 'alert' : 'status';
@endphp

<div
    class="alert alert-{{ $variant }}"
    role="{{ $role }}"
    @if ($dismissible) data-alert-dismissable @endif
    @if ($autodismiss && $dismissible) data-alert-autodismiss @endif
>
    <span class="alert-icon"><x-icon :name="$icons[$variant] ?? 'info'" :size="18" /></span>
    <div class="alert-body">{{ $slot }}</div>
    @if ($dismissible)
        <button type="button" class="alert-close" data-alert-dismiss aria-label="Dismiss notification">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif
</div>
