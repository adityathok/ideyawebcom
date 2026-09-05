@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        {{-- Mobile --}}
        <div class="flex items-center justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex cursor-not-allowed items-center rounded-lg border border-[#d3cec6] bg-white px-4 py-2 text-sm font-medium leading-5 text-[#7b7b78]">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center rounded-lg border border-[#d3cec6] bg-white px-4 py-2 text-sm font-medium leading-5 text-[#111111] hover:bg-[#f5f1ec]">Sebelumnya</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center rounded-lg border border-[#d3cec6] bg-white px-4 py-2 text-sm font-medium leading-5 text-[#111111] hover:bg-[#f5f1ec]">Selanjutnya</a>
            @else
                <span class="inline-flex cursor-not-allowed items-center rounded-lg border border-[#d3cec6] bg-white px-4 py-2 text-sm font-medium leading-5 text-[#7b7b78]">Selanjutnya</span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-2">
            <div>
                <p class="text-sm leading-5 text-[#626260]">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-medium text-[#111111]">{{ $paginator->firstItem() }}</span>
                        sampai
                        <span class="font-medium text-[#111111]">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari
                    <span class="font-medium text-[#111111]">{{ $paginator->total() }}</span>
                    hasil
                </p>
            </div>
            <div>
                <span class="inline-flex overflow-hidden rounded-lg border border-[#d3cec6]">
                    {{-- Previous --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex cursor-not-allowed items-center border-r border-[#d3cec6] bg-white px-2 py-2 text-sm font-medium leading-5 text-[#7b7b78]" aria-hidden="true">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center border-r border-[#d3cec6] bg-white px-2 py-2 text-sm font-medium leading-5 text-[#626260] hover:bg-[#f5f1ec] hover:text-[#111111]" aria-label="{{ __('pagination.previous') }}">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true"><span class="inline-flex cursor-default items-center border-r border-[#d3cec6] bg-white px-4 py-2 text-sm font-medium leading-5 text-[#626260] last:border-r-0">{{ $element }}</span></span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"><span class="inline-flex cursor-default items-center border-r border-[#d3cec6] bg-[#111111] px-4 py-2 text-sm font-medium leading-5 text-white last:border-r-0">{{ $page }}</span></span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center border-r border-[#d3cec6] bg-white px-4 py-2 text-sm font-medium leading-5 text-[#111111] hover:bg-[#f5f1ec] last:border-r-0" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center bg-white px-2 py-2 text-sm font-medium leading-5 text-[#626260] hover:bg-[#f5f1ec] hover:text-[#111111]" aria-label="{{ __('pagination.next') }}">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"><span class="inline-flex cursor-not-allowed items-center bg-white px-2 py-2 text-sm font-medium leading-5 text-[#7b7b78]" aria-hidden="true"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></span></span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
