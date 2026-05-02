@extends('layouts.app')

@section('title', 'Catálogo de Libros')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="font-display text-4xl font-bold text-ink-800">Catálogo</h1>
        @if(request('search'))
            <p class="text-ink-500 mt-1">Resultados para: <strong>"{{ request('search') }}"</strong></p>
        @endif
    </div>

    <div class="flex gap-8">

        {{-- ═══════════ SIDEBAR FILTROS ═══════════ --}}
        <aside class="hidden lg:block w-60 shrink-0">
            <form id="filter-form" method="GET" action="{{ route('books.index') }}">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                {{-- Géneros --}}
                <div class="bg-white rounded-2xl border border-ink-100 p-5 mb-4">
                    <h3 class="font-semibold text-ink-800 mb-3 text-sm uppercase tracking-wider">Género</h3>
                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="genre" value="" {{ !request('genre') ? 'checked' : '' }}
                                   class="w-4 h-4 text-ink-700 border-ink-300 focus:ring-ink-500" onchange="this.form.submit()">
                            <span class="text-sm text-ink-600 group-hover:text-ink-900">Todos</span>
                        </label>
                        @if(isset($genres))
                            @foreach($genres as $genre)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="genre" value="{{ $genre['slug'] }}"
                                       {{ request('genre') == $genre['slug'] ? 'checked' : '' }}
                                       class="w-4 h-4 text-ink-700 border-ink-300 focus:ring-ink-500"
                                       onchange="this.form.submit()">
                                <span class="text-sm text-ink-600 group-hover:text-ink-900">
                                    {{ $genre['icon'] ?? '' }} {{ $genre['name'] }}
                                </span>
                            </label>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Formato --}}
                <div class="bg-white rounded-2xl border border-ink-100 p-5 mb-4">
                    <h3 class="font-semibold text-ink-800 mb-3 text-sm uppercase tracking-wider">Formato</h3>
                    <div class="space-y-2">
                        @foreach(['' => 'Todos', 'pdf' => '📄 PDF', 'epub' => '📱 EPUB', 'physical' => '📦 Físico'] as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="format" value="{{ $val }}"
                                   {{ request('format', '') == $val ? 'checked' : '' }}
                                   class="w-4 h-4 text-ink-700 border-ink-300"
                                   onchange="this.form.submit()">
                            <span class="text-sm text-ink-600 group-hover:text-ink-900">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Precio --}}
                <div class="bg-white rounded-2xl border border-ink-100 p-5 mb-4">
                    <h3 class="font-semibold text-ink-800 mb-3 text-sm uppercase tracking-wider">Precio (USD)</h3>
                    <div class="flex gap-2 items-center">
                        <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}"
                               class="w-full border border-ink-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-ink-400"
                               min="0" max="999">
                        <span class="text-ink-400 text-sm">—</span>
                        <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}"
                               class="w-full border border-ink-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-ink-400"
                               min="0" max="999">
                    </div>
                    <button type="submit" class="w-full mt-3 btn-primary py-2 rounded-lg text-sm font-medium">Aplicar</button>
                </div>

                {{-- Limpiar filtros --}}
                @if(request()->hasAny(['genre','format','min_price','max_price','sort']))
                    <a href="{{ route('books.index') }}" class="block text-center text-sm text-ink-500 hover:text-ink-800 transition-colors">
                        ✕ Limpiar filtros
                    </a>
                @endif
            </form>
        </aside>

        {{-- ═══════════ LISTADO DE LIBROS ═══════════ --}}
        <div class="flex-1 min-w-0">

            {{-- Barra superior: resultados + ordenamiento --}}
            <div class="flex items-center justify-between mb-5 gap-4">
                <p class="text-sm text-ink-500">
                    @if(isset($books))
                        <strong class="text-ink-800">{{ number_format($books->total()) }}</strong> libros encontrados
                    @endif
                </p>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-ink-500 hidden sm:block">Ordenar:</label>
                    <select name="sort" form="filter-form" onchange="document.getElementById('filter-form').submit()"
                            class="border border-ink-200 rounded-xl px-3 py-2 text-sm text-ink-700 focus:outline-none focus:border-ink-400 bg-white">
                        @foreach($sortOptions ?? [] as $val => $label)
                            <option value="{{ $val }}" {{ request('sort', 'relevance') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Grid de libros --}}
            @if(isset($books) && $books->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($books as $book)
                        @include('components.book-card', ['book' => $book])
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-8">
                    {{ $books->withQueryString()->links('components.pagination') }}
                </div>

            @else
                <div class="text-center py-20">
                    <span class="text-6xl">🔍</span>
                    <h3 class="font-display text-2xl font-bold text-ink-700 mt-4 mb-2">Sin resultados</h3>
                    <p class="text-ink-400 mb-6">No encontramos libros con esos filtros.</p>
                    <a href="{{ route('books.index') }}" class="btn-primary px-6 py-3 rounded-xl inline-block font-medium">
                        Ver todo el catálogo
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Agregar al carrito desde el catálogo
async function addToCart(bookId, format) {
    try {
        const res = await fetch('/cart/items', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ book_id: bookId, format: format, quantity: 1 })
        });
        const data = await res.json();
        if (res.ok) {
            showToast('✓ Libro agregado al carrito', 'success');
            // Actualizar badge del carrito
            const badge = document.querySelector('.cart-badge');
            if (badge) badge.textContent = data.item_count;
        } else {
            showToast(data.error || 'Error al agregar', 'error');
        }
    } catch(e) {
        showToast('Error de conexión', 'error');
    }
}

function showToast(msg, type) {
    const toast = document.createElement('div');
    toast.className = `toast fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-xl text-sm font-medium ${type === 'success' ? 'bg-ink-800 text-white' : 'bg-red-700 text-white'}`;
    toast.innerHTML = `<span>${msg}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 400); }, 3000);
}
</script>
@endpush
@endsection
