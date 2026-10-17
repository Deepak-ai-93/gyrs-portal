@props([
    'title' => null,
    'message' => null,
    'icon' => 'inbox',
])

<div class="empty-state" role="status">
    <span class="empty-icon"><x-icon :name="$icon" :size="24" /></span>
    @if ($title)
        <h3 class="empty-title">{{ $title }}</h3>
    @endif
    @if ($message)
        <p class="empty-message">{{ $message }}</p>
    @endif
    @isset($action)
        <div class="empty-action">{{ $action }}</div>
    @endisset
</div>
