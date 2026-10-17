@props([
    'title' => null,
    'meta' => null,
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || $meta || isset($actions))
        <div class="card-head">
            <div class="card-head-text">
                @if ($title)
                    <h3 class="card-title">{{ $title }}</h3>
                @endif
                @if ($meta)
                    <p class="card-meta">{{ $meta }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="card-actions">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-foot">{{ $footer }}</div>
    @endisset
</div>
