<div class="book-card bg-white rounded-2xl overflow-hidden border border-ink-100 flex flex-col">
    <a href="{{ route('books.show', $book->slug) }}" class="block aspect-[3/4] bg-ink-50 overflow-hidden relative">
        @if($book->cover_image)
            <img src="{{ asset('storage/' . $book->cover_image) }}"
                 alt="{{ $book->title }}"
                 class="book-cover w-full h-full object-cover">
        @else
            {{-- Portada generada con colores según ID --}}
            @php
                $colors = ['bg-ink-700','bg-ink-600','bg-ink-500','bg-ink-400'];
                $color = $colors[$book->id % 4];
            @endphp
            <div class="book-cover w-full h-full {{ $color }} flex flex-col items-center justify-center p-4 text-center">
                <span class="text-4xl mb-3">📖</span>
                <p class="text-cream text-xs font-medium leading-tight line-clamp-3 font-display">{{ $book->title }}</p>
            </div>
        @endif

        {{-- Badge descuento --}}
        @if($book->is_on_sale)
            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                -{{ $book->discount_percent }}%
            </div>
        @endif

        {{-- Formato disponible --}}
        <div class="absolute top-2 right-2 flex flex-col gap-1">
            @if($book->has_pdf)
                <span class="bg-black/60 text-white text-xs px-1.5 py-0.5 rounded font-medium">PDF</span>
            @endif
            @if($book->has_epub)
                <span class="bg-black/60 text-white text-xs px-1.5 py-0.5 rounded font-medium">EPUB</span>
            @endif
        </div>
    </a>

    <div class="p-3 flex flex-col flex-1">
        {{-- Autor --}}
        @if($book->authors && $book->authors->count())
            <p class="text-xs text-ink-400 mb-1 truncate">{{ $book->authors->first()->name }}</p>
        @endif

        {{-- Título --}}
        <a href="{{ route('books.show', $book->slug) }}" class="font-medium text-ink-800 text-sm leading-tight line-clamp-2 hover:text-ink-600 transition-colors flex-1">
            {{ $book->title }}
        </a>

        {{-- Rating --}}
        @if($book->rating_count > 0)
            <div class="flex items-center gap-1 mt-2">
                <span class="text-amber-400 text-xs">★</span>
                <span class="text-xs text-ink-500">{{ number_format($book->rating_avg, 1) }}</span>
                <span class="text-xs text-ink-300">({{ $book->rating_count }})</span>
            </div>
        @endif

        {{-- Precio --}}
        <div class="flex items-center justify-between mt-3">
            <div>
                <span class="font-bold text-ink-800 text-sm">${{ number_format($book->price_cents / 100, 2) }}</span>
                @if($book->is_on_sale)
                    <span class="text-ink-400 text-xs line-through ml-1">${{ number_format($book->original_price_cents / 100, 2) }}</span>
                @endif
            </div>
            <button
                onclick="addToCart({{ $book->id }}, '{{ $book->has_pdf ? 'pdf' : ($book->has_epub ? 'epub' : 'physical') }}')"
                class="w-8 h-8 bg-ink-800 text-cream rounded-lg flex items-center justify-center hover:bg-ink-600 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
        </div>
    </div>
</div>
