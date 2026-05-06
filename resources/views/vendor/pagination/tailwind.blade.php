@if ($paginator->hasPages())
<nav class="flex justify-end mt-6">
    <div class="flex items-center gap-1">

        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">← Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-600 hover:text-blue-500 transition">← Anterior</a>
        @endif

        {{-- Páginas --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2 text-sm text-gray-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-2 text-sm bg-blue-500 text-white rounded-lg font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 text-sm text-gray-600 hover:text-blue-500 rounded-lg transition">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Siguiente --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-600 hover:text-blue-500 transition">Siguiente →</a>
        @else
            <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">Siguiente →</span>
        @endif

    </div>
</nav>
@endif