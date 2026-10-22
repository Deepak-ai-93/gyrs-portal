@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'placeholder' => null,
    'toggle' => false,
])

@php
    $id = $attributes->get('id') ?: 'field-' . str_replace(['[', ']', '.'], ['-', '', '-'], $name);
    $message = $error ?? (isset($errors) ? $errors->first($name) : null);
    $hasError = filled($message);
    $describedBy = $hasError ? $id . '-error' : (filled($hint) ? $id . '-hint' : null);
    $reveal = $toggle && $type === 'password';
@endphp

<div class="form-field {{ $hasError ? 'has-error' : '' }}">
    @if ($label)
        <label class="form-label" for="{{ $id }}">
            {{ $label }}@if ($required)<span class="req" aria-hidden="true">*</span>@endif
        </label>
    @endif

    @if ($reveal)
        <div class="input-control">
            <input
                id="{{ $id }}"
                name="{{ $name }}"
                type="{{ $type }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                @if ($required) required aria-required="true" @endif
                @if ($hasError) aria-invalid="true" @endif
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
                {{ $attributes->merge(['class' => 'form-control']) }}
            >

            <button
                type="button"
                class="input-reveal-btn"
                data-password-toggle
                aria-controls="{{ $id }}"
                aria-label="Show password"
                aria-pressed="false"
            >
                <span class="icon-show"><x-icon name="eye" :size="18" /></span>
                <span class="icon-hide"><x-icon name="eye-off" :size="18" /></span>
            </button>
        </div>
    @else
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required aria-required="true" @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->merge(['class' => 'form-control']) }}
        >
    @endif

    @if ($hint)
        <p class="form-hint" id="{{ $id }}-hint">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p class="form-error" id="{{ $id }}-error">{{ $message }}</p>
    @endif
</div>
