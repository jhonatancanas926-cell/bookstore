@if ($paginator->hasPages())
<nav class="flex items-center justify-center gap-1">
    {{-- Anterior --}}
    @if ($paginator->onFirstPage())
        <span class="px-3 py-2 text-ink-300 cursor-not-allowed text-sm">← Anterior</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-ink-600 hover:text-ink-900 text-sm hover:bg-ink-100 rounded-lg transition-colors">← Anterior</a>
    @endif

    {{-- Páginas --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2 text-ink-400">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-2 bg-ink-800 text-cream rounded-lg text-sm font-medium">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-2 text-ink-600 hover:bg-ink-100 rounded-lg text-sm transition-colors">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Siguiente --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-ink-600 hover:text-ink-900 text-sm hover:bg-ink-100 rounded-lg transition-colors">Siguiente →</a>
    @else
        <span class="px-3 py-2 text-ink-300 cursor-not-allowed text-sm">Siguiente →</span>
    @endif
</nav>
@endif
