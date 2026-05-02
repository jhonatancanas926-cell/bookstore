@extends('layouts.app')
@section('title', 'Mis Descargas')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="font-display text-4xl font-bold text-ink-800 mb-2">Mis Descargas</h1>
    <p class="text-ink-500 mb-8">Los enlaces de descarga son válidos por 72 horas y tienen un límite de 5 descargas.</p>

    @if($downloads->count() === 0)
        <div class="text-center py-20 bg-white rounded-3xl border border-ink-100">
            <span class="text-6xl">📥</span>
            <h2 class="font-display text-2xl font-bold text-ink-700 mt-4 mb-2">Sin descargas disponibles</h2>
            <p class="text-ink-400 mb-6">Compra un ebook para obtener acceso a descarga inmediata.</p>
            <a href="{{ route('books.index') }}?format=pdf" class="btn-primary px-8 py-3 rounded-xl inline-block font-semibold">
                Ver ebooks
            </a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach($downloads as $token)
            <div class="bg-white rounded-2xl border border-ink-100 p-5">
                <div class="flex gap-4">
                    {{-- Portada --}}
                    <div class="w-14 h-20 bg-ink-100 rounded-xl overflow-hidden shrink-0">
                        @if($token->book->cover_image)
                            <img src="{{ asset('storage/' . $token->book->cover_image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-ink-700 flex items-center justify-center text-cream text-xl">📖</div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-ink-800 text-sm leading-tight line-clamp-2">{{ $token->book->title }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs bg-ink-100 text-ink-600 px-2 py-0.5 rounded-full uppercase font-medium">
                                {{ $token->format }}
                            </span>
                        </div>

                        {{-- Expiración y usos --}}
                        <div class="mt-2 text-xs text-ink-400 space-y-0.5">
                            <p>Expira: {{ $token->expires_at->format('d M Y, H:i') }}</p>
                            <p>Descargas: {{ $token->downloads_used }}/{{ $token->downloads_limit }}</p>
                        </div>

                        {{-- Barra de usos --}}
                        <div class="mt-2 bg-ink-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-ink-600 h-full rounded-full"
                                 style="width: {{ ($token->downloads_used / $token->downloads_limit) * 100 }}%"></div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('downloads.file', $token->token) }}"
                   class="mt-4 w-full btn-primary py-2.5 rounded-xl font-medium text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar {{ strtoupper($token->format) }}
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
