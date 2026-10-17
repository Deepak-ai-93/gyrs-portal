@props([
    'variant' => 'outline',
    'type' => 'button',
    'href' => null,
    'size' => null,
    'block' => false,
])

@php
    $classes = trim(implode(' ', array_filter([
        'btn',
        'btn-' . $variant,
        $size ? 'btn-' . $size : null,
        $block ? 'btn-block' : null,
    ])));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
