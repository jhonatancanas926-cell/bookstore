@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

{{-- ═══════════════════════════ HERO ═══════════════════════════ --}}
<section class="relative overflow-hidden bg-ink-800 text-cream">
    {{-- Fondo decorativo --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-ink-400 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-ink-600 rounded-full translate-x-1/4 translate-y-1/4"></div>
    </div>
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center gap-2 bg-ink-700 text-ink-200 text-xs font-medium px-3 py-1.5 rounded-full mb-6">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                    +10.000 títulos disponibles
                </div>
                <h1 class="font-display text-5xl md:text-6xl lg:text-7xl font-bold leading-tight mb-6">
                    Libros que<br>
                    <span class="italic text-ink-300">inspiran</span><br>
                    la mente.
                </h1>
                <p class="text-ink-300 text-lg leading-relaxed mb-8 max-w-md">
                    Descarga ebooks en PDF y EPUB o recibe tu libro físico. Descarga instantánea tras la compra.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 bg-cream text-ink-800 px-6 py-3 rounded-xl font-semibold hover:bg-white transition-colors">
                        Explorar catálogo
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('books.index') }}?sort=bestseller" class="inline-flex items-center gap-2 border border-ink-600 text-ink-200 px-6 py-3 rounded-xl font-medium hover:border-ink-400 hover:text-cream transition-colors">
                        Más vendidos
                    </a>
                </div>
                {{-- Stats --}}
                <div class="flex gap-8 mt-12 pt-8 border-t border-ink-700">
                    <div>
                        <p class="font-display text-3xl font-bold text-cream">10K+</p>
                        <p class="text-xs text-ink-400 mt-1">Títulos</p>
                    </div>
                    <div>
                        <p class="font-display text-3xl font-bold text-cream">2K+</p>
                        <p class="text-xs text-ink-400 mt-1">Lectores</p>
                    </div>
                    <div>
                        <p class="font-display text-3xl font-bold text-cream">25</p>
                        <p class="text-xs text-ink-400 mt-1">Géneros</p>
                    </div>
                </div>
            </div>

            {{-- Libros decorativos --}}
            <div class="hidden md:flex items-center justify-center relative h-80">
                <div class="absolute" style="transform: rotate(-8deg) translateX(-60px)">
                    <div class="w-36 h-52 bg-ink-600 rounded-lg shadow-2xl flex items-end p-3">
                        <div class="w-full">
                            <div class="h-1.5 bg-ink-400 rounded mb-1.5 w-3/4"></div>
                            <div class="h-1 bg-ink-500 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>
                <div class="absolute z-10" style="transform: rotate(2deg)">
                    <div class="w-40 h-56 bg-parchment rounded-lg shadow-2xl flex items-end p-3">
                        <div class="w-full">
                            <div class="h-1.5 bg-ink-300 rounded mb-1.5 w-2/3"></div>
                            <div class="h-1 bg-ink-200 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>
                <div class="absolute" style="transform: rotate(10deg) translateX(60px)">
                    <div class="w-36 h-52 bg-ink-500 rounded-lg shadow-2xl flex items-end p-3">
                        <div class="w-full">
                            <div class="h-1.5 bg-ink-300 rounded mb-1.5 w-4/5"></div>
                            <div class="h-1 bg-ink-400 rounded w-1/3"></div>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 text-6xl" style="transform: translateY(10px)">📚</div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════ GÉNEROS ═══════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="font-display text-3xl font-bold text-ink-800">Explorar por género</h2>
        <a href="{{ route('books.index') }}" class="text-sm text-ink-500 hover:text-ink-800 transition-colors">Ver todos →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach([
            ['Ficción', '📖', 'ficcion'],
            ['Ciencia Ficción', '🚀', 'ciencia-ficcion'],
            ['Fantasía', '🧙', 'fantasia'],
            ['Thriller', '🔍', 'thriller'],
            ['Negocios', '💼', 'negocios'],
            ['Tecnología', '💻', 'tecnologia'],
            ['Historia', '🏛️', 'historia'],
            ['Autoayuda', '💡', 'autoayuda'],
            ['Romance', '❤️', 'romance'],
            ['Biograf.', '👤', 'biografia'],
            ['Programac.', '⌨️', 'programacion'],
            ['Filosofía', '🤔', 'filosofia'],
        ] as [$name, $icon, $slug])
        <a href="{{ route('books.index') }}?genre={{ $slug }}"
           class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-ink-100 hover:border-ink-300 hover:shadow-md transition-all group">
            <span class="text-3xl group-hover:scale-110 transition-transform">{{ $icon }}</span>
            <span class="text-xs font-medium text-ink-600 text-center leading-tight">{{ $name }}</span>
        </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════ DESTACADOS ═══════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="font-display text-3xl font-bold text-ink-800">Libros destacados</h2>
        <a href="{{ route('books.index') }}?sort=relevance" class="text-sm text-ink-500 hover:text-ink-800 transition-colors">Ver todos →</a>
    </div>

    @if(isset($featured) && $featured->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            @foreach($featured as $book)
                @include('components.book-card', ['book' => $book])
            @endforeach
        </div>
    @else
        {{-- Placeholder si no hay libros aún --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            @for($i = 0; $i < 5; $i++)
            <div class="bg-white rounded-2xl overflow-hidden border border-ink-100 animate-pulse">
                <div class="aspect-[3/4] bg-ink-100"></div>
                <div class="p-3">
                    <div class="h-3 bg-ink-100 rounded w-3/4 mb-2"></div>
                    <div class="h-2 bg-ink-100 rounded w-1/2 mb-3"></div>
                    <div class="h-4 bg-ink-100 rounded w-1/3"></div>
                </div>
            </div>
            @endfor
        </div>
        <p class="text-center text-ink-400 text-sm mt-6">
            Ejecuta <code class="bg-ink-100 px-2 py-0.5 rounded text-ink-700">php artisan db:seed</code> para cargar los libros de ejemplo.
        </p>
    @endif
</section>

{{-- ═══════════════════════════ MÁS VENDIDOS ═══════════════════════════ --}}
<section class="bg-parchment py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-display text-3xl font-bold text-ink-800">Más vendidos</h2>
                <p class="text-ink-500 text-sm mt-1">Los favoritos de nuestra comunidad</p>
            </div>
            <a href="{{ route('books.index') }}?sort=bestseller" class="text-sm text-ink-500 hover:text-ink-800">Ver todos →</a>
        </div>
        @if(isset($bestsellers) && $bestsellers->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
                @foreach($bestsellers->take(4) as $book)
                    @include('components.book-card', ['book' => $book])
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-ink-400">
                <span class="text-5xl">📚</span>
                <p class="mt-3 text-sm">Los bestsellers aparecerán aquí una vez cargues los datos.</p>
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════ BANNER CTA ═══════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-ink-800 rounded-3xl p-10 md:p-14 flex flex-col md:flex-row items-center justify-between gap-8 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-ink-700 rounded-full translate-x-1/3 -translate-y-1/3 opacity-50"></div>
        <div class="relative">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-cream mb-3">
                Descarga instantánea
            </h2>
            <p class="text-ink-300 max-w-md">
                Compra un ebook y tenlo en segundos. Compatible con cualquier lector PDF o EPUB.
            </p>
        </div>
        <div class="relative shrink-0">
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-cream text-ink-800 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white transition-colors">
                    Crear cuenta gratis
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            @else
                <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 bg-cream text-ink-800 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white transition-colors">
                    Ver catálogo completo
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            @endguest
        </div>
    </div>
</section>

@endsection
