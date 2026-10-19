@props([
    'role' => 'Demo',
    'login' => null,
    'password' => null,
    'loginLabel' => 'Username',
    'fillName' => 'email',
    'form' => null,
])

@php($demo = config('gyrs.demo', []))

@if (($demo['enabled'] ?? false) && $login && $password)
    <div class="demo-access">
        <div class="demo-access-head">
            <span class="badge badge-info"><x-icon name="sparkles" :size="13" /> Demo access</span>
            <span class="text-sm muted">{{ $role }} evaluation account</span>
        </div>

        <div class="demo-access-row">
            <span class="demo-access-label">{{ $loginLabel }}</span>
            <code class="demo-access-value">{{ $login }}</code>
            <button type="button" class="btn btn-link btn-sm" data-copy="{{ $login }}">Copy</button>
        </div>

        <div class="demo-access-row">
            <span class="demo-access-label">Password</span>
            <code class="demo-access-value">{{ $password }}</code>
            <button type="button" class="btn btn-link btn-sm" data-copy="{{ $password }}">Copy</button>
        </div>

        @if ($form)
            <x-button
                variant="outline"
                size="sm"
                block
                type="button"
                data-demo-fill
                data-demo-form="#{{ $form }}"
                data-demo-login-name="{{ $fillName }}"
                data-demo-login="{{ $login }}"
                data-demo-password="{{ $password }}"
            >
                <x-icon name="sparkles" :size="15" /> Fill demo credentials
            </x-button>
        @endif

        <p class="text-sm muted" style="margin:var(--space-3) 0 0;">
            Evaluation access only. Disable this panel with <code>GYRS_DEMO=false</code>.
        </p>
    </div>
@endif
