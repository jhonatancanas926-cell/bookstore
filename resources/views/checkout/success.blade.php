@extends('layouts.app')
@section('title', 'Pedido Confirmado')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">

    {{-- Ícono éxito con animación --}}
    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6" style="animation: pop 0.5s ease">
        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="font-display text-4xl font-bold text-ink-800 mb-3">¡Pedido Confirmado!</h1>
    <p class="text-ink-500 mb-2">Número de pedido: <strong class="text-ink-800">{{ $order->order_number }}</strong></p>
    <p class="text-ink-500 mb-8">Recibirás un email de confirmación en <strong>{{ $order->user->email }}</strong></p>

    {{-- Detalle del pedido --}}
    <div class="bg-white rounded-2xl border border-ink-100 p-6 text-left mb-8">
        <h2 class="font-display text-xl font-bold text-ink-800 mb-4">Detalle del pedido</h2>
        <div class="space-y-3">
            @foreach($order->items as $item)
            <div class="flex items-center justify-between py-2 border-b border-ink-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-ink-800">{{ $item->book_title }}</p>
                    <p class="text-xs text-ink-400 uppercase">{{ $item->format }}
                        @if($item->isDigital())
                            · <a href="{{ route('downloads.index') }}" class="text-ink-600 underline">Descargar</a>
                        @endif
                    </p>
                </div>
                <span class="text-sm font-semibold text-ink-700">${{ number_format($item->total_cents / 100, 2) }}</span>
            </div>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-ink-100 flex justify-between font-bold text-ink-800">
            <span>Total pagado</span>
            <span>${{ number_format($order->total_cents / 100, 2) }}</span>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        @if($order->hasDigitalItems())
        <a href="{{ route('downloads.index') }}"
           class="btn-primary px-8 py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Ir a mis descargas
        </a>
        @endif
        <a href="{{ route('books.index') }}"
           class="btn-outline px-8 py-3.5 rounded-xl font-medium">
            Seguir comprando
        </a>
    </div>
</div>
@endsection
