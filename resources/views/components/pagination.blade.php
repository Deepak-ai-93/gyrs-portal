@props(['paginator' => null])

@if ($paginator && $paginator->hasPages())
    {{ $paginator->appends(request()->query())->links('pagination.gyrs') }}
@elseif ($paginator && $paginator->total() > 0)
    <p class="text-sm muted" style="margin-top:var(--space-4);">
        Showing {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </p>
@endif
