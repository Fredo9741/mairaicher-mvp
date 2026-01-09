<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Domaine des Papangues')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/cart.js') }}" defer></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl">🌿</span>
                        </div>
                        <span class="hidden sm:block text-xl font-bold text-green-600">Domaine des Papangues</span>
                        <span class="sm:hidden text-lg font-bold text-green-600">Papangues</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg font-medium transition-colors">
                        Accueil
                    </a>
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg font-medium transition-colors">
                        <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Panier</span>
                        @php
                            $cart = session()->get('cart', []);
                            $itemCount = count($cart);
                        @endphp
                        <span data-cart-count class="absolute -top-1 -right-1 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-emerald-600 rounded-full {{ $itemCount > 0 ? '' : 'hidden' }}">{{ $itemCount > 0 ? $itemCount : '' }}</span>
                    </a>

                    @auth
                        <div class="flex items-center gap-2 ml-2 pl-2 border-l border-gray-300">
                            <a href="/admin" class="inline-flex items-center gap-2 text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Admin</span>
                            </a>
                            <span class="text-gray-600 text-sm">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 text-gray-700 hover:text-red-600 px-3 py-2 rounded-lg font-medium transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-2 ml-2 pl-2 border-l border-gray-300">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg font-semibold transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span>Connexion</span>
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-green-600 text-white px-3 py-2 rounded-lg font-semibold hover:from-emerald-700 hover:to-green-700 transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                <span>Inscription</span>
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Mobile menu button + Cart -->
                <div class="md:hidden flex items-center gap-2">
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @php
                            $cart = session()->get('cart', []);
                            $itemCount = count($cart);
                        @endphp
                        <span data-cart-count class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-emerald-600 rounded-full {{ $itemCount > 0 ? '' : 'hidden' }}">{{ $itemCount > 0 ? $itemCount : '' }}</span>
                    </a>
                    <button @click="open = !open" class="text-gray-700 p-2">
                        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="open" @click.away="open = false" class="md:hidden border-t border-gray-200" style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium">
                    Accueil
                </a>
                @auth
                    <a href="/admin" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium">
                        Admin
                    </a>
                    <div class="px-3 py-2 text-sm text-gray-600">
                        Connecté en tant que {{ auth()->user()->name }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 font-medium">
                            Déconnexion
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-green-600 text-white font-semibold text-center">
                        Inscription
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Messages flash -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 mb-4">
            <div class="bg-gradient-to-r from-yellow-400 via-yellow-300 to-amber-400 border-2 border-yellow-500 text-gray-900 px-6 py-4 rounded-2xl shadow-2xl relative overflow-hidden" role="alert">
                <div class="absolute inset-0 bg-yellow-400/20 animate-pulse"></div>
                <div class="relative flex items-center gap-3">
                    <svg class="w-6 h-6 text-yellow-800 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span class="block font-bold text-lg">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 mb-4">
            <div class="bg-gradient-to-r from-orange-500 via-orange-400 to-red-500 border-2 border-red-600 text-white px-6 py-4 rounded-2xl shadow-2xl relative overflow-hidden" role="alert">
                <div class="absolute inset-0 bg-red-500/20 animate-pulse"></div>
                <div class="relative flex items-center gap-3">
                    <svg class="w-6 h-6 text-white flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <span class="block font-bold text-lg">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 mb-4">
            <div class="bg-gradient-to-r from-orange-500 via-orange-400 to-red-500 border-2 border-red-600 text-white px-6 py-4 rounded-2xl shadow-2xl relative overflow-hidden" role="alert">
                <div class="absolute inset-0 bg-red-500/20 animate-pulse"></div>
                <div class="relative flex items-start gap-3">
                    <svg class="w-6 h-6 text-white flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <ul class="space-y-1 font-bold text-lg">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Contenu principal -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} Domaine des Papangues - Tous droits réservés</p>
                <p class="mt-2 text-gray-400">Légumes frais, volaille et paniers de saison - Océan Indien</p>
            </div>
        </div>
    </footer>
</body>
</html>
