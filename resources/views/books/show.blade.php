@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-ink-400 mb-8">
        <a href="{{ route('home') }}" class="hover:text-ink-700 transition-colors">Inicio</a>
        <span>›</span>
        <a href="{{ route('books.index') }}" class="hover:text-ink-700 transition-colors">Catálogo</a>
        <span>›</span>
        <span class="text-ink-700 truncate max-w-xs">{{ $book->title }}</span>
    </nav>

    <div class="grid md:grid-cols-5 gap-10">

        {{-- ═══ PORTADA + ACCIONES ═══ --}}
        <div class="md:col-span-2">
            {{-- Portada --}}
            <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-ink-100 shadow-xl mb-5">
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                @else
                    @php $colors = ['bg-ink-700','bg-ink-600','bg-ink-500']; $color = $colors[$book->id % 3]; @endphp
                    <div class="w-full h-full {{ $color }} flex flex-col items-center justify-center p-8 text-center">
                        <span class="text-6xl mb-4">📖</span>
                        <p class="text-cream font-display text-lg font-bold leading-tight">{{ $book->title }}</p>
                    </div>
                @endif
            </div>

            {{-- Formatos disponibles --}}
            <div class="bg-white rounded-2xl border border-ink-100 p-5">
                <h3 class="font-semibold text-ink-700 text-sm mb-4">Formatos disponibles</h3>

                @if($hasPurchased)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center mb-4">
                        <span class="text-2xl">✅</span>
                        <p class="text-sm font-medium text-green-700 mt-1">Ya tienes este libro</p>
                        <a href="{{ route('downloads.index') }}" class="text-xs text-green-600 underline mt-1 block">Ver mis descargas</a>
                    </div>
                @endif

                <div class="space-y-3">
                    @if($book->has_pdf)
                    <div class="flex items-center justify-between p-3 border border-ink-100 rounded-xl hover:border-ink-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📄</span>
                            <div>
                                <p class="text-sm font-medium text-ink-800">PDF</p>
                                <p class="text-xs text-ink-400">Descarga inmediata</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-ink-800">${{ number_format($book->price_cents / 100, 2) }}</p>
                            @if(!$hasPurchased)
                            <button onclick="addToCart({{ $book->id }}, 'pdf')"
                                    class="text-xs btn-primary px-3 py-1.5 rounded-lg mt-1 font-medium">
                                + Carrito
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($book->has_epub)
                    <div class="flex items-center justify-between p-3 border border-ink-100 rounded-xl hover:border-ink-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📱</span>
                            <div>
                                <p class="text-sm font-medium text-ink-800">EPUB</p>
                                <p class="text-xs text-ink-400">Para e-readers</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-ink-800">${{ number_format($book->price_cents / 100, 2) }}</p>
                            @if(!$hasPurchased)
                            <button onclick="addToCart({{ $book->id }}, 'epub')"
                                    class="text-xs btn-primary px-3 py-1.5 rounded-lg mt-1 font-medium">
                                + Carrito
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($book->has_physical)
                    <div class="flex items-center justify-between p-3 border border-ink-100 rounded-xl hover:border-ink-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📦</span>
                            <div>
                                <p class="text-sm font-medium text-ink-800">Físico</p>
                                <p class="text-xs text-ink-400">Stock: {{ $book->stock }} unid.</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-ink-800">${{ number_format($book->price_cents / 100, 2) }}</p>
                            @if(!$hasPurchased && $book->stock > 0)
                            <button onclick="addToCart({{ $book->id }}, 'physical')"
                                    class="text-xs btn-primary px-3 py-1.5 rounded-lg mt-1 font-medium">
                                + Carrito
                            </button>
                            @elseif($book->stock == 0)
                            <span class="text-xs text-red-500 block mt-1">Agotado</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══ DETALLE DEL LIBRO ═══ --}}
        <div class="md:col-span-3">

            {{-- Géneros --}}
            @if($book->genres->count())
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($book->genres as $genre)
                    <a href="{{ route('books.index') }}?genre={{ $genre->slug }}"
                       class="px-3 py-1 bg-ink-100 text-ink-600 text-xs font-medium rounded-full hover:bg-ink-200 transition-colors">
                        {{ $genre->name }}
                    </a>
                @endforeach
            </div>
            @endif

            {{-- Título y autor --}}
            <h1 class="font-display text-4xl font-bold text-ink-800 leading-tight mb-3">{{ $book->title }}</h1>
            @if($book->authors->count())
                <p class="text-ink-500 text-lg mb-4">
                    por <span class="text-ink-700 font-medium">{{ $book->authors->pluck('name')->join(', ') }}</span>
                </p>
            @endif

            {{-- Rating --}}
            @if($book->rating_count > 0)
            <div class="flex items-center gap-3 mb-6">
                <div class="flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= round($book->rating_avg) ? 'text-amber-400' : 'text-ink-200' }} text-xl">★</span>
                    @endfor
                </div>
                <span class="font-semibold text-ink-700">{{ number_format($book->rating_avg, 1) }}</span>
                <span class="text-ink-400 text-sm">({{ number_format($book->rating_count) }} reseñas)</span>
            </div>
            @endif

            {{-- Info rápida --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                @if($book->published_at)
                <div class="bg-parchment rounded-xl p-3 text-center">
                    <p class="text-xs text-ink-400">Publicado</p>
                    <p class="text-sm font-semibold text-ink-700 mt-0.5">{{ $book->published_at->format('Y') }}</p>
                </div>
                @endif
                @if($book->pages)
                <div class="bg-parchment rounded-xl p-3 text-center">
                    <p class="text-xs text-ink-400">Páginas</p>
                    <p class="text-sm font-semibold text-ink-700 mt-0.5">{{ $book->pages }}</p>
                </div>
                @endif
                @if($book->language)
                <div class="bg-parchment rounded-xl p-3 text-center">
                    <p class="text-xs text-ink-400">Idioma</p>
                    <p class="text-sm font-semibold text-ink-700 mt-0.5 uppercase">{{ $book->language }}</p>
                </div>
                @endif
                @if($book->publisher)
                <div class="bg-parchment rounded-xl p-3 text-center">
                    <p class="text-xs text-ink-400">Editorial</p>
                    <p class="text-sm font-semibold text-ink-700 mt-0.5 truncate">{{ $book->publisher }}</p>
                </div>
                @endif
            </div>

            {{-- Descripción --}}
            <div>
                <h2 class="font-display text-xl font-bold text-ink-800 mb-3">Descripción</h2>
                <div class="text-ink-600 leading-relaxed text-sm prose max-w-none">
                    {!! nl2br(e($book->description)) !!}
                </div>
            </div>

            {{-- Reseñas recientes --}}
            @if($book->reviews->count())
            <div class="mt-8">
                <h2 class="font-display text-xl font-bold text-ink-800 mb-4">Reseñas</h2>
                <div class="space-y-4">
                    @foreach($book->reviews->take(3) as $review)
                    <div class="bg-white border border-ink-100 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-ink-800 text-cream rounded-full flex items-center justify-center text-sm font-semibold">
                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-ink-700">{{ $review->user->name ?? 'Usuario' }}</span>
                            </div>
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? 'text-amber-400' : 'text-ink-200' }} text-sm">★</span>
                                @endfor
                            </div>
                        </div>
                        @if($review->title)
                            <p class="font-medium text-ink-800 text-sm mb-1">{{ $review->title }}</p>
                        @endif
                        @if($review->body)
                            <p class="text-ink-500 text-sm">{{ $review->body }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Libros similares --}}
    @if($similar->count())
    <div class="mt-16">
        <h2 class="font-display text-2xl font-bold text-ink-800 mb-6">También te puede gustar</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($similar as $relatedBook)
                @include('components.book-card', ['book' => $relatedBook])
            @endforeach
        </div>
    </div>
    @endif
</div>

@endsection
