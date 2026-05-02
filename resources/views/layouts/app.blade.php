<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BookStore') — Librería Digital</title>
    <meta name="description" content="@yield('description', 'Tu librería digital con miles de títulos en PDF, EPUB y formato físico.')">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind CDN (desarrollo) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"Playfair Display"', 'serif'],
                        body: ['"DM Sans"', 'sans-serif'],
                    },
                    colors: {
                        ink: {
                            50:  '#f5f3ee',
                            100: '#e8e3d8',
                            200: '#d4cab4',
                            300: '#bbaa88',
                            400: '#a08d65',
                            500: '#8a7450',
                            600: '#725e40',
                            700: '#5a4930',
                            800: '#2c2215',
                            900: '#1a1409',
                        },
                        cream: '#faf7f2',
                        parchment: '#f0ebe0',
                    },
                }
            }
        }
    </script>

    <style>
        * { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }

        body { background-color: #faf7f2; color: #2c2215; }

        /* Navbar scroll effect */
        .navbar-scrolled { background: rgba(250,247,242,0.97); backdrop-filter: blur(12px); box-shadow: 0 1px 20px rgba(44,34,21,0.08); }

        /* Book card hover */
        .book-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .book-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(44,34,21,0.15); }
        .book-card:hover .book-cover { transform: scale(1.03); }
        .book-cover { transition: transform 0.4s ease; }

        /* Button styles */
        .btn-primary { background: #2c2215; color: #faf7f2; transition: all 0.2s ease; }
        .btn-primary:hover { background: #5a4930; transform: translateY(-1px); }
        .btn-outline { border: 1.5px solid #2c2215; color: #2c2215; transition: all 0.2s ease; }
        .btn-outline:hover { background: #2c2215; color: #faf7f2; }

        /* Cart badge */
        .cart-badge { animation: pop 0.3s ease; }
        @keyframes pop { 0%,100%{transform:scale(1)} 50%{transform:scale(1.3)} }

        /* Page fade in */
        .page-enter { animation: fadeUp 0.5s ease forwards; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f0ebe0; }
        ::-webkit-scrollbar-thumb { background: #bbaa88; border-radius: 3px; }

        /* Search dropdown */
        #search-results { box-shadow: 0 8px 30px rgba(44,34,21,0.12); }

        /* Toast notifications */
        .toast { transform: translateX(120%); transition: transform 0.35s cubic-bezier(.34,1.56,.64,1); }
        .toast.show { transform: translateX(0); }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- NAVBAR --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    <span class="font-display text-2xl font-bold text-ink-800">📚 BookStore</span>
                </a>

                {{-- Search bar (desktop) --}}
                <div class="flex-1 max-w-xl hidden md:block relative">
                    <form action="{{ route('books.index') }}" method="GET">
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                placeholder="Buscar libros, autores, géneros..."
                                value="{{ request('search') }}"
                                class="w-full pl-4 pr-12 py-2.5 rounded-xl border border-ink-200 bg-white/80 text-ink-800 placeholder-ink-300 focus:outline-none focus:border-ink-400 focus:ring-2 focus:ring-ink-200 text-sm"
                                autocomplete="off"
                            >
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 hover:text-ink-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </button>
                        </div>
                    </form>
                    {{-- Autocomplete dropdown --}}
                    <div id="search-results" class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl border border-ink-100 hidden z-50 overflow-hidden"></div>
                </div>

                {{-- Nav links --}}
                <div class="flex items-center gap-1 sm:gap-3">
                    <a href="{{ route('books.index') }}" class="hidden sm:block text-sm font-medium text-ink-600 hover:text-ink-900 px-3 py-2 rounded-lg hover:bg-ink-50 transition-colors">
                        Catálogo
                    </a>

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2 rounded-xl hover:bg-ink-100 transition-colors">
                        <svg class="w-6 h-6 text-ink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @php $cartCount = session('cart_count', 0); @endphp
                        @if($cartCount > 0)
                            <span class="cart-badge absolute -top-1 -right-1 bg-ink-800 text-cream text-xs w-5 h-5 rounded-full flex items-center justify-center font-semibold">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    {{-- User menu --}}
                    @auth
                        <div class="relative group">
                            <button class="flex items-center gap-2 p-2 rounded-xl hover:bg-ink-100 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-ink-800 text-cream flex items-center justify-center text-sm font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block text-sm font-medium text-ink-700">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div class="absolute right-0 top-full mt-1 w-52 bg-white border border-ink-100 rounded-xl shadow-lg py-1 hidden group-hover:block">
                                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink-700 hover:bg-ink-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Mis pedidos
                                </a>
                                <a href="{{ route('downloads.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink-700 hover:bg-ink-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Mis descargas
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <hr class="my-1 border-ink-100">
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink-700 hover:bg-ink-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                        Panel Admin
                                    </a>
                                @endif
                                <hr class="my-1 border-ink-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-outline text-sm px-4 py-2 rounded-xl font-medium">
                            Ingresar
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm px-4 py-2 rounded-xl font-medium hidden sm:block">
                            Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Espaciado por el navbar fijo --}}
    <div class="h-20"></div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- FLASH MESSAGES --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if(session('success') || session('error'))
        <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2">
            @if(session('success'))
                <div class="toast show flex items-center gap-3 bg-ink-800 text-cream px-5 py-3.5 rounded-xl shadow-xl text-sm font-medium max-w-sm">
                    <svg class="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="toast show flex items-center gap-3 bg-red-700 text-white px-5 py-3.5 rounded-xl shadow-xl text-sm font-medium max-w-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>
        <script>setTimeout(() => document.querySelectorAll('.toast').forEach(t => t.classList.remove('show')), 4000)</script>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MAIN CONTENT --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <main class="flex-1 page-enter">
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- FOOTER --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <footer class="bg-ink-800 text-ink-200 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <span class="font-display text-2xl font-bold text-cream">📚 BookStore</span>
                    <p class="mt-3 text-sm text-ink-300 leading-relaxed max-w-xs">
                        Tu librería digital con más de 10.000 títulos en PDF, EPUB y formato físico. Descarga instantánea tras la compra.
                    </p>
                </div>
                <div>
                    <h4 class="text-cream font-semibold mb-3 text-sm uppercase tracking-wider">Explorar</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('books.index') }}" class="hover:text-cream transition-colors">Catálogo</a></li>
                        <li><a href="{{ route('books.index') }}?sort=bestseller" class="hover:text-cream transition-colors">Más vendidos</a></li>
                        <li><a href="{{ route('books.index') }}?sort=newest" class="hover:text-cream transition-colors">Novedades</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-cream font-semibold mb-3 text-sm uppercase tracking-wider">Mi cuenta</h4>
                    <ul class="space-y-2 text-sm">
                        @auth
                            <li><a href="{{ route('orders.index') }}" class="hover:text-cream transition-colors">Mis pedidos</a></li>
                            <li><a href="{{ route('downloads.index') }}" class="hover:text-cream transition-colors">Mis descargas</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-cream transition-colors">Ingresar</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-cream transition-colors">Registrarse</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
            <div class="border-t border-ink-700 mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-ink-400">
                <span>© {{ date('Y') }} BookStore. Todos los derechos reservados.</span>
                <span>Pagos seguros con Stripe 🔒</span>
            </div>
        </div>
    </footer>

    {{-- Navbar scroll script --}}
    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('navbar-scrolled', window.scrollY > 20);
        });

        // Autocomplete búsqueda
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');
        let searchTimer;

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);
                const q = searchInput.value.trim();
                if (q.length < 2) { searchResults.classList.add('hidden'); return; }
                searchTimer = setTimeout(async () => {
                    try {
                        const res = await fetch(`/books/search?q=${encodeURIComponent(q)}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const books = await res.json();
                        if (!books.length) { searchResults.classList.add('hidden'); return; }
                        searchResults.innerHTML = books.map(b => `
                            <a href="${b.url}" class="flex items-center gap-3 px-4 py-3 hover:bg-ink-50 transition-colors border-b border-ink-50 last:border-0">
                                <div class="w-10 h-14 bg-ink-100 rounded shrink-0 overflow-hidden">
                                    ${b.cover ? `<img src="${b.cover}" class="w-full h-full object-cover">` : '<div class="w-full h-full flex items-center justify-center text-ink-300 text-xs">📖</div>'}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-ink-800 truncate">${b.title}</p>
                                    <p class="text-xs text-ink-400">${b.author || ''}</p>
                                    <p class="text-sm font-semibold text-ink-700 mt-0.5">${b.price}</p>
                                </div>
                            </a>
                        `).join('');
                        searchResults.classList.remove('hidden');
                    } catch(e) { searchResults.classList.add('hidden'); }
                }, 300);
            });
            document.addEventListener('click', e => {
                if (!searchInput.contains(e.target)) searchResults.classList.add('hidden');
            });
        }

        // Global Shopping Cart Functions
        async function addToCart(bookId, format) {
            try {
                const res = await fetch('/cart/items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ book_id: bookId, format, quantity: 1 })
                });
                const data = await res.json();
                if (res.ok) {
                    showToast('✓ Agregado al carrito');
                    // Opcional: Actualizar el contador en el navbar si existe
                    const badge = document.querySelector('.cart-badge');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    } else if (data.count > 0) {
                        location.reload(); // Recargar para mostrar el badge por primera vez
                    }
                } else {
                    showToast(data.error || 'Error al agregar', 'error');
                }
            } catch(e) { 
                console.error(e);
                showToast('Error de conexión', 'error'); 
            }
        }

        function showToast(msg, type = 'success') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-2';
                document.body.appendChild(container);
            }
            
            const t = document.createElement('div');
            t.className = `toast flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-xl text-sm font-medium transition-all duration-300 ${type === 'success' ? 'bg-ink-800 text-cream' : 'bg-red-700 text-white'}`;
            t.innerHTML = `
                ${type === 'success' ? '<svg class="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' : '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'}
                <span>${msg}</span>
            `;
            container.appendChild(t);
            setTimeout(() => t.classList.add('show'), 10);
            setTimeout(() => { 
                t.classList.remove('show'); 
                setTimeout(() => t.remove(), 400); 
            }, 3000);
        }
    </script>

    @stack('scripts')
</body>
</html>
