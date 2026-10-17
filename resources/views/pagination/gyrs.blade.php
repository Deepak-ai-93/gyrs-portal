@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination navigation">
        <ul class="pagination-list">
            @if ($paginator->onFirstPage())
                <li class="pagination-item disabled"><span class="page-link" aria-disabled="true">Previous</span></li>
            @else
                <li class="pagination-item"><a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev">Previous</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="pagination-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item active"><span class="page-link" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li class="pagination-item"><a href="{{ $url }}" class="page-link">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="pagination-item"><a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next">Next</a></li>
            @else
                <li class="pagination-item disabled"><span class="page-link" aria-disabled="true">Next</span></li>
            @endif
        </ul>
    </nav>
@endif
