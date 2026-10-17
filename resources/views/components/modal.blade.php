@props([
    'name',
    'title' => null,
    'wide' => false,
])

<div class="modal" data-modal="{{ $name }}" hidden>
    <div class="modal-overlay" data-modal-close></div>

    <div
        class="modal-panel {{ $wide ? 'is-wide' : '' }}"
        role="dialog"
        aria-modal="true"
        @if ($title) aria-labelledby="modal-{{ $name }}-title" @endif
        tabindex="-1"
    >
        <div class="modal-head">
            @if ($title)
                <h2 class="modal-title" id="modal-{{ $name }}-title">{{ $title }}</h2>
            @endif
            <button type="button" class="modal-close" data-modal-close aria-label="Close dialog">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="modal-foot">{{ $footer }}</div>
        @endisset
    </div>
</div>
