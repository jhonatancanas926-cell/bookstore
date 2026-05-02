@extends('layouts.app')
@section('title', 'Pago')

@push('styles')
<style>
    #card-element {
        background: white;
        padding: 14px 16px;
        border: 1.5px solid #d4cab4;
        border-radius: 12px;
        transition: border-color 0.2s;
    }
    #card-element.StripeElement--focus { border-color: #5a4930; box-shadow: 0 0 0 3px rgba(90,73,48,0.1); }
    #card-element.StripeElement--invalid { border-color: #ef4444; }
    #card-errors { color: #dc2626; font-size: 0.8rem; margin-top: 6px; min-height: 20px; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Pasos --}}
    <div class="flex items-center justify-center gap-3 mb-10">
        <div class="flex items-center gap-2 text-ink-400">
            <span class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold">✓</span>
            <span class="text-sm hidden sm:block">Carrito</span>
        </div>
        <div class="h-px w-12 bg-green-400"></div>
        <div class="flex items-center gap-2 text-ink-400">
            <span class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold">✓</span>
            <span class="text-sm hidden sm:block">Facturación</span>
        </div>
        <div class="h-px w-12 bg-ink-200"></div>
        <div class="flex items-center gap-2 text-ink-800">
            <span class="w-8 h-8 rounded-full bg-ink-800 text-cream flex items-center justify-center text-sm font-bold">3</span>
            <span class="text-sm font-semibold hidden sm:block">Pago</span>
        </div>
    </div>

    <h1 class="font-display text-3xl font-bold text-ink-800 mb-8 text-center">Datos de Pago</h1>

    <div class="grid lg:grid-cols-3 gap-8">

        {{-- ═══ FORMULARIO STRIPE ═══ --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-ink-100 p-6 sm:p-8">

                {{-- Aviso de seguridad --}}
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                    <span class="text-2xl">🔒</span>
                    <div>
                        <p class="text-sm font-semibold text-green-800">Pago 100% seguro con Stripe</p>
                        <p class="text-xs text-green-600">Tus datos bancarios van directamente a Stripe. Nunca los almacenamos.</p>
                    </div>
                </div>

                {{-- Datos de facturación (resumen) --}}
                @if(isset($billingAddress))
                <div class="bg-parchment rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider">Facturar a</p>
                        <a href="{{ route('checkout.address') }}" class="text-xs text-ink-500 hover:text-ink-800 underline">Editar</a>
                    </div>
                    <p class="text-sm font-medium text-ink-800">{{ $billingAddress['full_name'] ?? '' }}</p>
                    <p class="text-xs text-ink-500">{{ $billingAddress['email'] ?? '' }}</p>
                    <p class="text-xs text-ink-500">{{ $billingAddress['address'] ?? '' }}, {{ $billingAddress['city'] ?? '' }}</p>
                </div>
                @endif

                {{-- Stripe Elements --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-ink-700 mb-2">
                        Número de tarjeta
                    </label>
                    <div id="card-element"></div>
                    <div id="card-errors" role="alert"></div>
                </div>

                {{-- Logos tarjetas --}}
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-xs text-ink-400">Aceptamos:</span>
                    <div class="flex gap-2 text-2xl">
                        <span title="Visa">💳</span>
                    </div>
                    <span class="text-xs text-ink-300">Visa, Mastercard, Amex</span>
                </div>

                {{-- Total a pagar --}}
                <div class="flex justify-between items-center bg-ink-800 text-cream rounded-xl px-5 py-4 mb-6">
                    <span class="font-medium">Total a pagar</span>
                    <span class="font-display text-2xl font-bold">{{ $summary['total'] }}</span>
                </div>

                {{-- Botón pagar --}}
                <button id="pay-button" onclick="handlePayment()"
                        class="w-full btn-primary py-4 rounded-xl font-bold text-lg flex items-center justify-center gap-3 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span id="pay-label">🔒 Pagar {{ $summary['total'] }}</span>
                    <svg id="pay-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </button>

                <div class="flex gap-3 mt-4">
                    <a href="{{ route('checkout.address') }}"
                       class="flex-1 btn-outline text-center py-3 rounded-xl font-medium text-sm">
                        ← Volver
                    </a>
                </div>
            </div>
        </div>

        {{-- ═══ RESUMEN ═══ --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-ink-100 p-5 sticky top-24">
                <h3 class="font-display text-lg font-bold text-ink-800 mb-4">Resumen</h3>
                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                    @foreach($cart->items as $item)
                    <div class="flex gap-3">
                        <div class="w-10 h-14 bg-ink-100 rounded-lg overflow-hidden shrink-0">
                            @if($item->book->cover_image)
                                <img src="{{ asset('storage/' . $item->book->cover_image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-ink-600 flex items-center justify-center text-cream text-xs">📖</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-ink-800 line-clamp-2 leading-tight">{{ $item->book->title }}</p>
                            <p class="text-xs text-ink-400 uppercase mt-0.5">{{ $item->format }}</p>
                        </div>
                        <span class="text-xs font-semibold text-ink-700 shrink-0">${{ number_format($item->unit_price_cents / 100, 2) }}</span>
                    </div>
                    @endforeach
                </div>
                <hr class="border-ink-100 mb-3">
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-ink-500">
                        <span>Subtotal</span><span>{{ $summary['subtotal'] }}</span>
                    </div>
                    <div class="flex justify-between text-ink-500">
                        <span>IVA 19%</span><span>{{ $summary['tax'] }}</span>
                    </div>
                    @if($summary['discount_cents'] > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Descuento</span><span>−{{ $summary['discount'] }}</span>
                    </div>
                    @endif
                </div>
                <hr class="border-ink-100 my-3">
                <div class="flex justify-between font-bold text-ink-800 text-lg">
                    <span>Total</span>
                    <span>{{ $summary['total'] }}</span>
                </div>
                <p class="text-xs text-ink-400 text-center mt-4">🔒 Cifrado SSL de 256 bits</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ $stripeKey }}');
const elements = stripe.elements();
const card = elements.create('card', {
    style: {
        base: {
            fontFamily: '"DM Sans", sans-serif',
            fontSize: '15px',
            color: '#2c2215',
            '::placeholder': { color: '#bbaa88' },
        }
    }
});
card.mount('#card-element');

card.on('change', ({ error }) => {
    document.getElementById('card-errors').textContent = error ? error.message : '';
});

async function handlePayment() {
    const btn = document.getElementById('pay-button');
    const spinner = document.getElementById('pay-spinner');
    const label = document.getElementById('pay-label');
    btn.disabled = true;
    spinner.classList.remove('hidden');
    label.textContent = 'Procesando...';

    try {
        // 1. Crear PaymentIntent en el servidor
        const intentRes = await fetch('{{ route("checkout.intent") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        const intentData = await intentRes.json();

        if (!intentRes.ok) {
            throw new Error(intentData.error || 'Error al iniciar el pago');
        }

        // 2. Confirmar el pago con Stripe.js
        const { error, paymentIntent } = await stripe.confirmCardPayment(intentData.client_secret, {
            payment_method: { card: card }
        });

        if (error) {
            throw new Error(error.message);
        }

        // 3. Confirmar en nuestro servidor
        const confirmRes = await fetch('{{ route("checkout.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ payment_intent_id: paymentIntent.id })
        });
        const confirmData = await confirmRes.json();

        if (confirmData.success) {
            window.location.href = confirmData.redirect_url;
        } else {
            throw new Error(confirmData.error || 'Error al confirmar el pedido');
        }

    } catch (err) {
        document.getElementById('card-errors').textContent = err.message;
        btn.disabled = false;
        spinner.classList.add('hidden');
        label.textContent = '🔒 Pagar {{ $summary["total"] }}';
    }
}
</script>
@endpush
@endsection
