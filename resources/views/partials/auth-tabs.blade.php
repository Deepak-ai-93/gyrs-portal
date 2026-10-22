@php($active = $active ?? 'login')

<nav class="auth-tabs" aria-label="Candidate account access">
    <a href="{{ route('candidate.login') }}"
       class="auth-tab @if ($active === 'login') is-active @endif"
       @if ($active === 'login') aria-current="page" @endif>
        <x-icon name="user" :size="16" />
        <span>Sign in</span>
    </a>
    <a href="{{ route('candidate.register') }}"
       class="auth-tab @if ($active === 'register') is-active @endif"
       @if ($active === 'register') aria-current="page" @endif>
        <x-icon name="plus" :size="16" />
        <span>Create account</span>
    </a>
</nav>
