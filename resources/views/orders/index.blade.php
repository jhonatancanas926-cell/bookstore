@extends('layouts.app')
@section('title', 'Mis Pedidos')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="font-display text-4xl font-bold text-ink-800 mb-8">Mis Pedidos</h1>

    @if($orders->count() === 0)
        <div class="text-center py-20 bg-white rounded-3xl border border-ink-100">
            <span class="text-6xl">📦</span>
            <h2 class="font-display text-2xl font-bold text-ink-700 mt-4 mb-2">Sin pedidos aún</h2>
            <p class="text-ink-400 mb-6">Cuando realices una compra aparecerá aquí.</p>
            <a href="{{ route('books.index') }}" class="btn-primary px-8 py-3 rounded-xl inline-block font-semibold">
                Explorar catálogo
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-2xl border border-ink-100 p-5 hover:border-ink-300 transition-colors">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <p class="font-semibold text-ink-800">{{ $order->order_number }}</p>
                        <p class="text-xs text-ink-400 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' :
                               ($order->status === 'paid' ? 'bg-blue-100 text-blue-700' :
                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-600' :
                               'bg-amber-100 text-amber-700')) }}">
                            {{ match($order->status) {
                                'completed' => '✓ Completado',
                                'paid' => '💳 Pagado',
                                'pending' => '⏳ Pendiente',
                                'cancelled' => '✕ Cancelado',
                                'refunded' => '↩ Reembolsado',
                                default => ucfirst($order->status)
                            } }}
                        </span>
                        <span class="font-bold text-ink-800">${{ number_format($order->total_cents / 100, 2) }}</span>
                    </div>
                </div>

                {{-- Items preview --}}
                <div class="mt-4 flex gap-2 flex-wrap">
                    @foreach($order->items->take(4) as $item)
                    <div class="flex items-center gap-2 bg-parchment rounded-xl px-3 py-2">
                        <span class="text-sm">{{ $item->format === 'pdf' ? '📄' : ($item->format === 'epub' ? '📱' : '📦') }}</span>
                        <span class="text-xs text-ink-700 max-w-24 truncate">{{ $item->book_title }}</span>
                    </div>
                    @endforeach
                    @if($order->items->count() > 4)
                        <div class="flex items-center px-3 py-2 text-xs text-ink-400">
                            +{{ $order->items->count() - 4 }} más
                        </div>
                    @endif
                </div>

                <div class="flex gap-3 mt-4">
                    <a href="{{ route('orders.show', $order->id) }}"
                       class="text-sm text-ink-600 hover:text-ink-900 font-medium transition-colors">
                        Ver detalle →
                    </a>
                    @if($order->hasDigitalItems() && in_array($order->status, ['paid','completed']))
                    <a href="{{ route('downloads.index') }}"
                       class="text-sm text-ink-600 hover:text-ink-900 font-medium transition-colors">
                        Descargar →
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links('components.pagination') }}
        </div>
    @endif
</div>
@endsection
