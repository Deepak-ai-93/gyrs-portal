<div class="flash-region" aria-live="polite" aria-atomic="true">
    @if (session('status'))
        <x-alert variant="success" :dismissible="true" :autodismiss="true">{{ session('status') }}</x-alert>
    @endif
    @if (session('success'))
        <x-alert variant="success" :dismissible="true" :autodismiss="true">{{ session('success') }}</x-alert>
    @endif
    @if (session('warning'))
        <x-alert variant="warning" :dismissible="true">{{ session('warning') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert variant="danger" :dismissible="true">{{ session('error') }}</x-alert>
    @endif
    @if (session('info'))
        <x-alert variant="info" :dismissible="true" :autodismiss="true">{{ session('info') }}</x-alert>
    @endif
</div>
