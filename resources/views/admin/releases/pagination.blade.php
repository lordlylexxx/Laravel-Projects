@if ($paginator->hasPages())
    <nav class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <p class="text-sm text-slate-600">
            @if ($paginator->firstItem())
                Showing
                <span class="font-semibold text-slate-800">{{ $paginator->firstItem() }}</span>
                –
                <span class="font-semibold text-slate-800">{{ $paginator->lastItem() }}</span>
                of
                <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span>
                releases
            @else
                Showing <span class="font-semibold text-slate-800">{{ $paginator->count() }}</span>
                of <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span> releases
            @endif
        </p>

        <ul class="flex flex-wrap items-center justify-center gap-1 sm:justify-end">
            @if ($paginator->onFirstPage())
                <li>
                    <span class="inline-flex min-h-[2.25rem] min-w-[2.25rem] items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-400" aria-disabled="true">&lsaquo;</span>
                </li>
            @else
                <li>
                    <a
                        class="inline-flex min-h-[2.25rem] min-w-[2.25rem] items-center justify-center rounded-lg border border-emerald-200 bg-white text-sm font-semibold text-emerald-800 shadow-sm hover:bg-emerald-50"
                        href="{{ $paginator->previousPageUrl() }}"
                        rel="prev"
                        aria-label="{{ __('pagination.previous') }}"
                    >&lsaquo;</a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="inline-flex min-h-[2.25rem] min-w-[2.25rem] items-center justify-center text-sm text-slate-400">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span
                                    class="inline-flex min-h-[2.25rem] min-w-[2.25rem] items-center justify-center rounded-lg bg-emerald-700 text-sm font-bold text-white shadow-sm"
                                    aria-current="page"
                                >{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a
                                    class="inline-flex min-h-[2.25rem] min-w-[2.25rem] items-center justify-center rounded-lg border border-slate-200 bg-white text-sm font-semibold text-slate-700 shadow-sm hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-900"
                                    href="{{ $url }}"
                                >{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li>
                    <a
                        class="inline-flex min-h-[2.25rem] min-w-[2.25rem] items-center justify-center rounded-lg border border-emerald-200 bg-white text-sm font-semibold text-emerald-800 shadow-sm hover:bg-emerald-50"
                        href="{{ $paginator->nextPageUrl() }}"
                        rel="next"
                        aria-label="{{ __('pagination.next') }}"
                    >&rsaquo;</a>
                </li>
            @else
                <li>
                    <span class="inline-flex min-h-[2.25rem] min-w-[2.25rem] items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-400" aria-disabled="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
