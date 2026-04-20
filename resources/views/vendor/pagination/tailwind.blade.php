@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-top: 1px solid var(--border);">
        <div style="display: flex; justify-content: space-between; flex: 1; @media (min-width: 640px) { display: none; }">
            @if ($paginator->onFirstPage())
                <span class="btn btn-outline" style="opacity: 0.5; cursor: not-allowed;">{!! __('pagination.previous') !!}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline">{!! __('pagination.previous') !!}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline">{!! __('pagination.next') !!}</a>
            @else
                <span class="btn btn-outline" style="opacity: 0.5; cursor: not-allowed;">{!! __('pagination.next') !!}</span>
            @endif
        </div>

        <div style="display: none; @media (min-width: 640px) { display: flex; flex: 1; align-items: center; justify-content: space-between; }">
            <div>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">
                    Menampilkan 
                    @if ($paginator->firstItem())
                        <span style="font-weight: 700; color: var(--text-main);">{{ $paginator->firstItem() }}</span>
                        hingga
                        <span style="font-weight: 700; color: var(--text-main);">{{ $paginator->lastItem() }}</span>
                    @else
                        <span style="font-weight: 700; color: var(--text-main);">{{ $paginator->count() }}</span>
                    @endif
                    dari total
                    <span style="font-weight: 700; color: var(--text-main);">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <span style="display: inline-flex; box-shadow: var(--shadow-sm); border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" style="padding: 8px 12px; background: var(--bg-main); color: var(--text-muted); opacity: 0.5; border-right: 1px solid var(--border);">
                            <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" style="padding: 8px 12px; background: white; color: var(--text-main); border-right: 1px solid var(--border); text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='var(--bg-main)'" onmouseout="this.style.background='white'">
                            <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true" style="padding: 8px 12px; background: white; color: var(--text-muted); border-right: 1px solid var(--border);">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" style="padding: 8px 14px; background: var(--brand-start); color: white; font-weight: 700; border-right: 1px solid var(--border);">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" style="padding: 8px 14px; background: white; color: var(--text-main); font-weight: 600; text-decoration: none; border-right: 1px solid var(--border); transition: 0.2s;" onmouseover="this.style.background='var(--bg-main)'" onmouseout="this.style.background='white'">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" style="padding: 8px 12px; background: white; color: var(--text-main); text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='var(--bg-main)'" onmouseout="this.style.background='white'">
                            <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" style="padding: 8px 12px; background: var(--bg-main); color: var(--text-muted); opacity: 0.5;">
                            <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
