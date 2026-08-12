<span class="font-label-sm text-label-sm text-on-surface-variant">
    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
</span>
<div class="flex gap-1">
    {{-- Previous Page Link --}}
    @if ($products->onFirstPage())
        <button class="w-8 h-8 flex items-center justify-center border border-outline-variant rounded text-on-surface-variant opacity-50 cursor-not-allowed" disabled>
            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
        </button>
    @else
        <a href="{{ $products->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-outline-variant rounded text-on-surface-variant hover:text-primary hover:border-primary transition-colors js-page-link">
            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
        </a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
        @if ($page == $products->currentPage())
            <button class="w-8 h-8 flex items-center justify-center border border-outline-variant rounded text-on-primary bg-primary font-label-sm">{{ $page }}</button>
        @else
            <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center border border-outline-variant rounded text-on-surface-variant hover:text-primary hover:border-primary transition-colors font-label-sm js-page-link">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($products->hasMorePages())
        <a href="{{ $products->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-outline-variant rounded text-on-surface-variant hover:text-primary hover:border-primary transition-colors js-page-link">
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        </a>
    @else
        <button class="w-8 h-8 flex items-center justify-center border border-outline-variant rounded text-on-surface-variant opacity-50 cursor-not-allowed" disabled>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        </button>
    @endif
</div>
