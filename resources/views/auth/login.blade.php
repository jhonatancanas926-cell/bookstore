@extends('layouts.app')

@section('title', 'Ingresar')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-3xl shadow-xl border border-ink-100 overflow-hidden">
        <div class="bg-ink-800 p-8 text-center">
            <span class="text-4xl mb-4 block">📚</span>
            <h1 class="font-display text-2xl font-bold text-cream">Bienvenido de nuevo</h1>
            <p class="text-ink-300 text-sm mt-2">Ingresa a tu cuenta de BookStore</p>
        </div>

        <div class="p-8">
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-ink-500 uppercase tracking-wider mb-2">Correo electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border border-ink-100 focus:outline-none focus:border-ink-400 focus:ring-2 focus:ring-ink-100 transition-all text-ink-800">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-ink-500 uppercase tracking-wider mb-2">Contraseña</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-3 rounded-xl border border-ink-100 focus:outline-none focus:border-ink-400 focus:ring-2 focus:ring-ink-100 transition-all text-ink-800">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-ink-200 text-ink-800 focus:ring-ink-800">
                        <span class="text-sm text-ink-600">Recordarme</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-ink-400 hover:text-ink-800 transition-colors">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="w-full btn-primary py-3.5 rounded-xl font-bold text-lg shadow-lg shadow-ink-800/20">
                    Ingresar
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-ink-50 text-center">
                <p class="text-sm text-ink-400">
                    ¿No tienes cuenta? 
                    <a href="{{ route('register') }}" class="text-ink-700 font-semibold hover:underline">Regístrate gratis</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
