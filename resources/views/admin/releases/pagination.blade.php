@if ($paginator->hasPages())
    <nav class="release-pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <p class="release-pagination__summary">
            @if ($paginator->firstItem())
                Showing <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong> of <strong>{{ $paginator->total() }}</strong> releases
            @else
                Showing <strong>{{ $paginator->count() }}</strong> of <strong>{{ $paginator->total() }}</strong> releases
            @endif
        </p>

        <ul class="release-pagination__list">
            @if ($paginator->onFirstPage())
                <li><span class="release-pagination__link release-pagination__link--disabled" aria-disabled="true">&lsaquo;</span></li>
            @else
                <li><a class="release-pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">&lsaquo;</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="release-pagination__link release-pagination__link--ellipsis">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="release-pagination__link release-pagination__link--current" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a class="release-pagination__link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a class="release-pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">&rsaquo;</a></li>
            @else
                <li><span class="release-pagination__link release-pagination__link--disabled" aria-disabled="true">&rsaquo;</span></li>
            @endif
        </ul>
    </nav>
@endif
