@extends('layouts.app')
@section('title', 'Mi Carrito')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="font-display text-4xl font-bold text-ink-800 mb-8">Mi Carrito</h1>

    @if($cart->items->count() === 0)
        <div class="text-center py-20 bg-white rounded-3xl border border-ink-100">
            <span class="text-7xl">🛒</span>
            <h2 class="font-display text-2xl font-bold text-ink-700 mt-5 mb-2">Tu carrito está vacío</h2>
            <p class="text-ink-400 mb-6">Agrega libros desde el catálogo para comenzar.</p>
            <a href="{{ route('books.index') }}" class="btn-primary px-8 py-3 rounded-xl inline-block font-semibold">
                Explorar catálogo
            </a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- ═══ ITEMS ═══ --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach($cart->items as $item)
                <div class="bg-white rounded-2xl border border-ink-100 p-4 flex gap-4">
                    {{-- Mini portada --}}
                    <div class="w-16 h-24 rounded-xl overflow-hidden bg-ink-100 shrink-0">
                        @if($item->book->cover_image)
                            <img src="{{ asset('storage/' . $item->book->cover_image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-ink-700 flex items-center justify-center text-cream text-xl">📖</div>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('books.show', $item->book->slug) }}"
                           class="font-semibold text-ink-800 hover:text-ink-600 transition-colors line-clamp-2 leading-tight">
                            {{ $item->book_title ?? $item->book->title }}
                        </a>
                        <p class="text-xs text-ink-400 mt-1">
                            Formato: <span class="uppercase font-medium">{{ $item->format }}</span>
                        </p>
                        {{-- Cantidad (solo físicos) --}}
                        @if($item->format === 'physical')
                        <div class="flex items-center gap-2 mt-2">
                            <button onclick="updateQty({{ $item->book_id }}, '{{ $item->format }}', {{ $item->quantity - 1 }})"
                                    class="w-7 h-7 border border-ink-200 rounded-lg text-ink-600 hover:bg-ink-100 flex items-center justify-center text-sm">−</button>
                            <span class="text-sm font-medium text-ink-800 w-6 text-center">{{ $item->quantity }}</span>
                            <button onclick="updateQty({{ $item->book_id }}, '{{ $item->format }}', {{ $item->quantity + 1 }})"
                                    class="w-7 h-7 border border-ink-200 rounded-lg text-ink-600 hover:bg-ink-100 flex items-center justify-center text-sm">+</button>
                        </div>
                        @endif
                    </div>
                    {{-- Precio + eliminar --}}
                    <div class="flex flex-col items-end justify-between shrink-0">
                        <span class="font-bold text-ink-800">${{ number_format($item->unit_price_cents / 100, 2) }}</span>
                        <button onclick="removeItem({{ $item->book_id }}, '{{ $item->format }}')"
                                class="text-xs text-red-500 hover:text-red-700 transition-colors">
                            Eliminar
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ═══ RESUMEN ═══ --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-ink-100 p-6 sticky top-24">
                    <h2 class="font-display text-xl font-bold text-ink-800 mb-5">Resumen</h2>

                    {{-- Cupón --}}
                    <div class="mb-5">
                        <label class="text-xs font-medium text-ink-600 block mb-2">Cupón de descuento</label>
                        <div class="flex gap-2">
                            <input type="text" id="coupon-input" placeholder="CÓDIGO"
                                   class="flex-1 border border-ink-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-ink-400 uppercase">
                            <button onclick="applyCoupon()"
                                    class="btn-outline px-3 py-2 rounded-xl text-sm font-medium">
                                Aplicar
                            </button>
                        </div>
                        <p id="coupon-msg" class="text-xs mt-1 hidden"></p>
                    </div>

                    <hr class="border-ink-100 mb-4">

                    {{-- Totales --}}
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-ink-600">
                            <span>Subtotal</span>
                            <span id="subtotal">{{ $summary['subtotal'] }}</span>
                        </div>
                        <div class="flex justify-between text-ink-600">
                            <span>IVA (19%)</span>
                            <span id="tax">{{ $summary['tax'] }}</span>
                        </div>
                        @if($summary['discount_cents'] > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Descuento</span>
                            <span id="discount">−{{ $summary['discount'] }}</span>
                        </div>
                        @endif
                    </div>

                    <hr class="border-ink-100 my-4">

                    <div class="flex justify-between font-bold text-ink-800 text-lg mb-5">
                        <span>Total</span>
                        <span id="total">{{ $summary['total'] }}</span>
                    </div>

                    @auth
                        <a href="{{ route('checkout.address') }}"
                           class="block w-full btn-primary text-center py-3.5 rounded-xl font-semibold text-base">
                            Proceder al pago →
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full btn-primary text-center py-3.5 rounded-xl font-semibold text-base">
                            Inicia sesión para pagar
                        </a>
                        <a href="{{ route('register') }}" class="block text-center text-sm text-ink-500 hover:text-ink-800 mt-3">
                            ¿No tienes cuenta? Regístrate gratis
                        </a>
                    @endauth

                    <p class="text-xs text-ink-400 text-center mt-3 flex items-center justify-center gap-1">
                        🔒 Pago seguro con Stripe
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

async function removeItem(bookId, format) {
    const res = await fetch(`/cart/items/${bookId}/${format}`, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    });
    if (res.ok) location.reload();
}

async function updateQty(bookId, format, qty) {
    const res = await fetch(`/cart/items/${bookId}/${format}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ quantity: qty })
    });
    if (res.ok) location.reload();
}

async function applyCoupon() {
    const code = document.getElementById('coupon-input').value.trim();
    if (!code) return;
    const res = await fetch('/cart/coupon', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ code })
    });
    const data = await res.json();
    const msg = document.getElementById('coupon-msg');
    msg.classList.remove('hidden');
    if (data.success) {
        msg.className = 'text-xs mt-1 text-green-600';
        msg.textContent = '✓ Cupón aplicado correctamente';
        if (data.summary) {
            document.getElementById('total').textContent = data.summary.total;
        }
    } else {
        msg.className = 'text-xs mt-1 text-red-500';
        msg.textContent = data.message || 'Cupón inválido';
    }
}
</script>
@endpush
@endsection
