@extends('layouts.app')
@section('title', 'Panel Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-3xl font-bold text-ink-800">Panel de Administración</h1>
            <p class="text-ink-500 text-sm mt-1">{{ now()->format('d \d\e F, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.books') }}" class="btn-outline px-4 py-2 rounded-xl text-sm font-medium">Inventario</a>
            <a href="{{ route('admin.orders') }}" class="btn-primary px-4 py-2 rounded-xl text-sm font-medium">Pedidos</a>
        </div>
    </div>

    {{-- ═══ MÉTRICAS ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-ink-100 p-5">
            <p class="text-xs text-ink-400 uppercase tracking-wider mb-1">Ventas hoy</p>
            <p class="font-display text-3xl font-bold text-ink-800">${{ number_format($stats['total_sales_today'], 2) }}</p>
            <p class="text-xs text-ink-400 mt-1">{{ $stats['orders_today'] }} pedidos</p>
        </div>
        <div class="bg-white rounded-2xl border border-ink-100 p-5">
            <p class="text-xs text-ink-400 uppercase tracking-wider mb-1">Ventas este mes</p>
            <p class="font-display text-3xl font-bold text-ink-800">${{ number_format($stats['total_sales_month'], 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-ink-100 p-5">
            <p class="text-xs text-ink-400 uppercase tracking-wider mb-1">Usuarios activos</p>
            <p class="font-display text-3xl font-bold text-ink-800">{{ number_format($stats['total_users']) }}</p>
            <p class="text-xs text-green-500 mt-1">+{{ $stats['new_users_month'] }} este mes</p>
        </div>
        <div class="bg-white rounded-2xl border border-ink-100 p-5 {{ $stats['books_low_stock'] > 0 ? 'border-amber-200 bg-amber-50' : '' }}">
            <p class="text-xs text-ink-400 uppercase tracking-wider mb-1">Stock bajo</p>
            <p class="font-display text-3xl font-bold {{ $stats['books_low_stock'] > 0 ? 'text-amber-600' : 'text-ink-800' }}">
                {{ $stats['books_low_stock'] }}
            </p>
            <p class="text-xs text-ink-400 mt-1">libros con ≤5 unid.</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- ═══ PEDIDOS RECIENTES ═══ --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-ink-100">
            <div class="flex items-center justify-between p-5 border-b border-ink-100">
                <h2 class="font-display text-lg font-bold text-ink-800">Pedidos Recientes</h2>
                <a href="{{ route('admin.orders') }}" class="text-sm text-ink-500 hover:text-ink-800">Ver todos →</a>
            </div>
            <div class="divide-y divide-ink-50">
                @forelse($recentOrders as $order)
                <div class="flex items-center justify-between px-5 py-3.5 hover:bg-parchment transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-ink-800">{{ $order->order_number }}</p>
                        <p class="text-xs text-ink-400">{{ $order->user->name ?? 'N/A' }} · {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 ml-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' :
                               ($order->status === 'paid' ? 'bg-blue-100 text-blue-700' :
                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-600' :
                               'bg-amber-100 text-amber-700')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                        <span class="text-sm font-bold text-ink-800">${{ number_format($order->total_cents / 100, 2) }}</span>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs text-ink-400 hover:text-ink-700">Ver →</a>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-ink-400 text-sm">No hay pedidos aún.</div>
                @endforelse
            </div>
        </div>

        {{-- ═══ TOP LIBROS ═══ --}}
        <div class="bg-white rounded-2xl border border-ink-100">
            <div class="flex items-center justify-between p-5 border-b border-ink-100">
                <h2 class="font-display text-lg font-bold text-ink-800">Top Libros</h2>
                <a href="{{ route('admin.books') }}" class="text-sm text-ink-500 hover:text-ink-800">Ver todos →</a>
            </div>
            <div class="divide-y divide-ink-50">
                @forelse($stats['top_books'] as $i => $book)
                <div class="flex items-center gap-3 px-5 py-3.5">
                    <span class="font-display text-2xl font-bold text-ink-200 w-6 text-center">{{ $i + 1 }}</span>
                    <div class="w-10 h-14 bg-ink-100 rounded-lg overflow-hidden shrink-0">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-ink-600 flex items-center justify-center text-cream text-sm">📖</div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink-800 line-clamp-2 leading-tight">{{ $book->title }}</p>
                        <p class="text-xs text-ink-400 mt-0.5">{{ number_format($book->sales_count) }} ventas</p>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-ink-400 text-sm">Sin datos aún.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
