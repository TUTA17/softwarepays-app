@if ($paginator->hasPages())
    <div class="flex items-center justify-between mt-4">
        <div class="text-sm text-gray-500">
            Hi?n th? <span class="font-medium">{{ $paginator->firstItem() }}</span> d?n <span class="font-medium">{{ $paginator->lastItem() }}</span> c?a <span class="font-medium">{{ $paginator->total() }}</span> k?t qu?
        </div>
        <div class="flex gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed text-sm">Tru?c</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-md text-sm transition-colors">Tru?c</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-1 bg-white border border-gray-200 text-gray-700 rounded-md text-sm">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1 bg-primary text-white rounded-md text-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-md text-sm transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-md text-sm transition-colors">Sau</a>
            @else
                <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed text-sm">Sau</span>
            @endif
        </div>
    </div>
@endif

