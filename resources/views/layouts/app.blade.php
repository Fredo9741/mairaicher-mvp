<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Domaine des Papangues')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='0.9em' font-size='90'>🌿</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="{{ asset('js/cart.js') }}" defer></script>
    @stack('styles')
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                @php
                    $cart = session()->get('cart', []);
                    $itemCount = count($cart);
                @endphp

                <!-- Gauche : Menu hamburger (mobile) -->
                <div class="flex items-center">
                    <button @click="open = !open" class="md:hidden text-gray-700 p-2 hover:bg-gray-100 rounded-xl transition-colors">
                        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Desktop : Liens de navigation à gauche -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-xl font-medium transition-all">
                            Accueil
                        </a>
                        <a href="{{ route('home') }}#paniers" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-xl font-medium transition-all">
                            Paniers
                        </a>
                        <a href="{{ route('home') }}#produits" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-xl font-medium transition-all">
                            Produits
                        </a>
                    </div>
                </div>

                <!-- Centre : Logo -->
                <div class="absolute left-1/2 transform -translate-x-1/2">
                    <a href="{{ route('home') }}" class="flex items-center gap-1.5 sm:gap-2 lg:gap-3 group">
                        <img src="{{ asset('images/logopapangue.webp') }}"
                             alt="Domaine des Papangues"
                             class="h-10 sm:h-12 lg:h-14 w-auto object-contain transition-transform group-hover:scale-105">
                        <!-- Mobile : titre + sous-titre compact -->
                        <span class="lg:hidden flex flex-col leading-tight">
                            <span class="text-xs sm:text-sm font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors">Domaine des Papangues</span>
                            <span class="text-xs sm:text-sm text-emerald-600" style="font-family: 'Caveat', cursive;">Maraîchage & Élevage</span>
                        </span>
                        <!-- Desktop : titre complet + sous-titre -->
                        <span class="hidden lg:flex flex-col leading-tight">
                            <span class="text-lg font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors">Domaine des Papangues</span>
                            <span class="text-base text-emerald-600" style="font-family: 'Caveat', cursive;">Maraîchage & Élevage</span>
                        </span>
                    </a>
                </div>

                <!-- Droite : Profil + Panier -->
                <div class="flex items-center gap-2">
                    @auth
                        <!-- Desktop : User Dropdown -->
                        <div class="hidden md:block relative" x-data="{ dropdownOpen: false }">
                            <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-100 transition-colors">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-emerald-500 shadow-sm">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-180': dropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="dropdownOpen"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-1 z-50">
                                <!-- En-tête avec infos utilisateur -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                    <span class="inline-flex mt-2 items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if(auth()->user()->role === 'developer') bg-purple-100 text-purple-800
                                        @elseif(auth()->user()->role === 'maraicher') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </span>
                                </div>

                                <!-- Lien Administration (si admin) -->
                                @if(auth()->user()->isAdmin())
                                <a href="/admin" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Administration
                                </a>
                                @endif

                                <!-- Lien Mon profil -->
                                <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Mon profil
                                </a>

                                <!-- Séparateur -->
                                <div class="border-t border-gray-100 my-1"></div>

                                <!-- Déconnexion -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Se déconnecter
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Non connecté : Bouton connexion (desktop uniquement) -->
                        <a href="{{ route('login') }}" class="hidden md:inline-flex items-center gap-2 text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-xl font-medium transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Connexion</span>
                        </a>
                    @endauth

                    <!-- Panier -->
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all group">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span data-cart-count class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-emerald-600 rounded-full shadow-sm {{ $itemCount > 0 ? '' : 'hidden' }}">{{ $itemCount > 0 ? $itemCount : '' }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.away="open = false"
             class="md:hidden border-t border-gray-200 bg-white">
            <div class="px-4 pt-4 pb-6 space-y-2">
                <!-- En-tête utilisateur (si connecté) -->
                @auth
                    <div class="flex items-center gap-3 px-4 py-3 mb-2 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-emerald-100">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-emerald-500 shadow-sm">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium
                                @if(auth()->user()->role === 'developer') bg-purple-100 text-purple-800
                                @elseif(auth()->user()->role === 'maraicher') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 my-3"></div>
                @endauth

                <!-- Navigation -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Accueil
                </a>
                <a href="{{ route('home') }}#paniers" @click="open = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Nos Paniers
                </a>
                <a href="{{ route('home') }}#produits" @click="open = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Nos Produits
                </a>

                <div class="border-t border-gray-200 my-3"></div>

                <!-- Liens compte -->
                @auth
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Mon profil
                    </a>
                    @if(auth()->user()->isAdmin())
                        <a href="/admin" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-purple-50 hover:text-purple-700 font-medium transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Administration
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 font-medium transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Connexion
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
    <main class="flex-grow py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} Domaine des Papangues - Tous droits réservés</p>
                <p class="mt-2 text-gray-400">Légumes frais, volaille et paniers de saison - Océan Indien</p>
            </div>
        </div>
    </footer>

    {{-- Script de gestion automatique des indicateurs de chargement --}}
    <script src="{{ asset('js/form-loading.js') }}" defer></script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
