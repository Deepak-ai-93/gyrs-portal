@props(['job', 'saved' => false, 'size' => 'sm', 'block' => false])

@auth
    @if (auth()->user()->isCandidate())
        <form method="POST" action="{{ route('candidate.interested.toggle', $job) }}" class="interested-form">
            @csrf
            <button
                type="submit"
                class="btn {{ $saved ? 'btn-success' : 'btn-outline' }}{{ $size ? ' btn-'.$size : '' }}{{ $block ? ' btn-block' : '' }}"
                aria-pressed="{{ $saved ? 'true' : 'false' }}"
                title="{{ $saved ? 'Remove from interested jobs' : 'Mark as interested' }}"
            >
                <x-icon name="{{ $saved ? 'check' : 'sparkles' }}" :size="15" />
                {{ $saved ? 'Interested' : 'Mark interested' }}
            </button>
        </form>
    @endif
@endauth
