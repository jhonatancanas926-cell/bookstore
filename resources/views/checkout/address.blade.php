@extends('layouts.app')
@section('title', 'Datos de Facturación')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Pasos del checkout --}}
    <div class="flex items-center justify-center gap-3 mb-10">
        <div class="flex items-center gap-2 text-ink-400">
            <a href="{{ route('cart.index') }}" class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold">✓</a>
            <span class="text-sm hidden sm:block">Carrito</span>
        </div>
        <div class="h-px w-12 bg-ink-200"></div>
        <div class="flex items-center gap-2 text-ink-800">
            <span class="w-8 h-8 rounded-full bg-ink-800 text-cream flex items-center justify-center text-sm font-bold">2</span>
            <span class="text-sm font-semibold hidden sm:block">Facturación</span>
        </div>
        <div class="h-px w-12 bg-ink-200"></div>
        <div class="flex items-center gap-2 text-ink-300">
            <span class="w-8 h-8 rounded-full border-2 border-ink-200 text-ink-300 flex items-center justify-center text-sm font-bold">3</span>
            <span class="text-sm hidden sm:block">Pago</span>
        </div>
    </div>

    <h1 class="font-display text-3xl font-bold text-ink-800 mb-8 text-center">Datos de Facturación</h1>

    <div class="grid lg:grid-cols-3 gap-8">

        {{-- ═══ FORMULARIO ═══ --}}
        <div class="lg:col-span-2">
            <form action="{{ route('checkout.store-address') }}" method="POST" class="bg-white rounded-2xl border border-ink-100 p-6 sm:p-8">
                @csrf

                <div class="grid sm:grid-cols-2 gap-5">
                    {{-- Nombre completo --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink-700 mb-1.5">Nombre completo *</label>
                        <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}"
                               class="w-full border border-ink-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-ink-500 focus:ring-2 focus:ring-ink-100 @error('full_name') border-red-400 @enderror"
                               placeholder="Juan Pérez García">
                        @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink-700 mb-1.5">Email de facturación *</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                               class="w-full border border-ink-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-ink-500 focus:ring-2 focus:ring-ink-100 @error('email') border-red-400 @enderror"
                               placeholder="juan@ejemplo.com">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1.5">Teléfono</label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                               class="w-full border border-ink-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-ink-500 focus:ring-2 focus:ring-ink-100"
                               placeholder="+57 300 123 4567">
                    </div>

                    {{-- País --}}
                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1.5">País *</label>
                        <select name="country"
                                class="w-full border border-ink-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-ink-500 bg-white @error('country') border-red-400 @enderror">
                            <option value="">Seleccionar...</option>
                            <option value="CO" {{ old('country', 'CO') == 'CO' ? 'selected' : '' }}>🇨🇴 Colombia</option>
                            <option value="MX" {{ old('country') == 'MX' ? 'selected' : '' }}>🇲🇽 México</option>
                            <option value="AR" {{ old('country') == 'AR' ? 'selected' : '' }}>🇦🇷 Argentina</option>
                            <option value="CL" {{ old('country') == 'CL' ? 'selected' : '' }}>🇨🇱 Chile</option>
                            <option value="PE" {{ old('country') == 'PE' ? 'selected' : '' }}>🇵🇪 Perú</option>
                            <option value="ES" {{ old('country') == 'ES' ? 'selected' : '' }}>🇪🇸 España</option>
                            <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>🇺🇸 Estados Unidos</option>
                            <option value="VE" {{ old('country') == 'VE' ? 'selected' : '' }}>🇻🇪 Venezuela</option>
                            <option value="EC" {{ old('country') == 'EC' ? 'selected' : '' }}>🇪🇨 Ecuador</option>
                        </select>
                        @error('country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dirección --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink-700 mb-1.5">Dirección *</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full border border-ink-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-ink-500 focus:ring-2 focus:ring-ink-100 @error('address') border-red-400 @enderror"
                               placeholder="Calle 123 # 45-67">
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dirección 2 --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink-700 mb-1.5">Apto / Oficina (opcional)</label>
                        <input type="text" name="address2" value="{{ old('address2') }}"
                               class="w-full border border-ink-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-ink-500 focus:ring-2 focus:ring-ink-100"
                               placeholder="Apt 4B">
                    </div>

                    {{-- Ciudad --}}
                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1.5">Ciudad *</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               class="w-full border border-ink-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-ink-500 focus:ring-2 focus:ring-ink-100 @error('city') border-red-400 @enderror"
                               placeholder="Bogotá">
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Código postal --}}
                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1.5">Código postal</label>
                        <input type="text" name="zip" value="{{ old('zip') }}"
                               class="w-full border border-ink-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-ink-500 focus:ring-2 focus:ring-ink-100"
                               placeholder="110111">
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <a href="{{ route('cart.index') }}"
                       class="flex-1 btn-outline text-center py-3 rounded-xl font-medium text-sm">
                        ← Volver al carrito
                    </a>
                    <button type="submit"
                            class="flex-1 btn-primary py-3 rounded-xl font-semibold">
                        Continuar al pago →
                    </button>
                </div>
            </form>
        </div>

        {{-- ═══ RESUMEN DEL PEDIDO ═══ --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-ink-100 p-5 sticky top-24">
                <h3 class="font-display text-lg font-bold text-ink-800 mb-4">Tu pedido</h3>
                <div class="space-y-3 mb-4">
                    @foreach($cart->items as $item)
                    <div class="flex gap-3">
                        <div class="w-10 h-14 bg-ink-100 rounded-lg overflow-hidden shrink-0">
                            @if($item->book->cover_image)
                                <img src="{{ asset('storage/' . $item->book->cover_image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-ink-600 flex items-center justify-center text-xs text-cream">📖</div>
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
                        <span>Subtotal</span>
                        <span>{{ $summary['subtotal'] }}</span>
                    </div>
                    <div class="flex justify-between text-ink-500">
                        <span>IVA 19%</span>
                        <span>{{ $summary['tax'] }}</span>
                    </div>
                    @if($summary['discount_cents'] > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Descuento</span>
                        <span>−{{ $summary['discount'] }}</span>
                    </div>
                    @endif
                </div>
                <hr class="border-ink-100 my-3">
                <div class="flex justify-between font-bold text-ink-800">
                    <span>Total</span>
                    <span>{{ $summary['total'] }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
