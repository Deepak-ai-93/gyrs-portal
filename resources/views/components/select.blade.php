@props([
    'name',
    'label' => null,
    'options' => [],
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
    $selected = (string) old($name, $value);

    $normalized = [];
    foreach ($options as $key => $option) {
        if (is_array($option)) {
            $optionValue = (string) ($option['value'] ?? $option['id'] ?? $key);
            $normalized[$optionValue] = $option['label'] ?? $option['name'] ?? $option['title'] ?? $optionValue;
        } elseif (is_object($option)) {
            $optionValue = (string) ($option->value ?? $option->id ?? $key);
            $normalized[$optionValue] = $option->label ?? $option->name ?? $option->title ?? $optionValue;
        } elseif (is_int($key)) {
            $normalized[(string) $option] = $option;
        } else {
            $normalized[(string) $key] = $option;
        }
    }
@endphp

<div class="form-field {{ $hasError ? 'has-error' : '' }}">
    @if ($label)
        <label class="form-label" for="{{ $id }}">
            {{ $label }}@if ($required)<span class="req" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($required) required aria-required="true" @endif
        @if ($hasError) aria-invalid="true" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->merge(['class' => 'form-select']) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($normalized as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @if ($hint)
        <p class="form-hint" id="{{ $id }}-hint">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p class="form-error" id="{{ $id }}-error">{{ $message }}</p>
    @endif
</div>
