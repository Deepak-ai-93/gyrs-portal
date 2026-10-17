@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'placeholder' => null,
])

@php
    $id = $attributes->get('id') ?: 'field-' . str_replace(['[', ']', '.'], ['-', '', '-'], $name);
    $message = $error ?? (isset($errors) ? $errors->first($name) : null);
    $hasError = filled($message);
    $describedBy = $hasError ? $id . '-error' : (filled($hint) ? $id . '-hint' : null);
@endphp

<div class="form-field {{ $hasError ? 'has-error' : '' }}">
    @if ($label)
        <label class="form-label" for="{{ $id }}">
            {{ $label }}@if ($required)<span class="req" aria-hidden="true">*</span>@endif
        </label>
    @endif

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

    @if ($hint)
        <p class="form-hint" id="{{ $id }}-hint">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p class="form-error" id="{{ $id }}-error">{{ $message }}</p>
    @endif
</div>
