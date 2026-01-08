@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Hero Section - Design Moderne & Organique -->
    <div class="relative bg-red-600 rounded-3xl shadow-2xl overflow-hidden mb-16">
        <!-- Image de fond - Tomates -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-reunion.jpg') }}"
                 alt="Tomates fraîches"
                 class="w-full h-full object-cover"
                 loading="eager">
            <!-- Overlay léger pour meilleure lisibilité sans dénaturer les couleurs -->
            <div class="absolute inset-0 bg-gradient-to-br from-black/40 via-black/30 to-black/50"></div>
        </div>

        <div class="relative px-8 py-20 md:py-28">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-5 py-2 rounded-full mb-6 border border-white/30">
                    <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span>
                    <span class="text-white text-sm font-medium">Production locale & Agriculture durable</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-bold mb-6 text-white leading-tight tracking-tight">
                    Domaine des Papangues
                </h1>

                <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed max-w-3xl mx-auto font-light">
                    Domaine des Papangues : agriculture, élevage biologique et traditionnel
                </p>

                <div class="flex flex-wrap justify-center gap-4 mb-10">
                    <div class="flex items-center gap-2 text-white/90">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        <span class="text-sm">Agriculture familiale</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/90">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span class="text-sm">Fraîcheur garantie</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/90">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                        </svg>
                        <span class="text-sm">Livraison rapide</span>
                    </div>
                </div>

                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#produits" class="inline-flex items-center gap-2 bg-white text-emerald-700 px-8 py-4 rounded-xl font-semibold hover:bg-emerald-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span>Explorer nos produits</span>
                    </a>
                    <a href="#paniers" class="inline-flex items-center gap-2 bg-emerald-800/30 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-semibold hover:bg-emerald-800/40 transition-all border-2 border-white/30 hover:border-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Nos paniers</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Forme organique ondulée -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 48L60 42C120 36 240 24 360 26C480 28 600 44 720 48C840 52 960 44 1080 38C1200 32 1320 28 1380 26L1440 24V80H1380C1320 80 1200 80 1080 80C960 80 840 80 720 80C600 80 480 80 360 80C240 80 120 80 60 80H0V48Z" fill="white"/>
            </svg>
        </div>
    </div>

    <!-- Valeurs & Engagements -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
        <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-emerald-200 hover:-translate-y-1">
            <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-emerald-200 transition-colors">
                <svg class="w-7 h-7 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-3">Cultivé localement</h3>
            <p class="text-gray-600 leading-relaxed">Des légumes frais cultivés avec passion dans le respect de la nature et des saisons.</p>
        </div>

        <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-emerald-200 hover:-translate-y-1">
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-green-200 transition-colors">
                <svg class="w-7 h-7 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-3">Qualité contrôlée</h3>
            <p class="text-gray-600 leading-relaxed">Chaque produit est soigneusement sélectionné pour garantir fraîcheur et saveur.</p>
        </div>

        <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-emerald-200 hover:-translate-y-1">
            <div class="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-teal-200 transition-colors">
                <svg class="w-7 h-7 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-3">Livraison express</h3>
            <p class="text-gray-600 leading-relaxed">Vos produits frais livrés rapidement, de la ferme à votre table en toute simplicité.</p>
        </div>
    </div>

    <!-- Paniers de saison -->
    @if($bundles->count() > 0)
    <section id="paniers" class="mb-20">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <span>⭐</span>
                <span>Sélection de la semaine</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Nos Paniers de Saison</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Découvrez nos paniers gourmands composés de légumes frais de saison</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($bundles as $bundle)
            <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-emerald-200 hover:-translate-y-1">
                @if($bundle->image)
                <!-- Image du panier -->
                <div class="relative h-56 overflow-hidden bg-gray-50">
                    <img src="https://files-maraicher.fredlabs.org/{{ $bundle->image }}"
                         alt="{{ $bundle->name }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center gap-2 bg-white/95 backdrop-blur-sm text-emerald-700 px-3 py-1.5 rounded-full text-xs font-bold shadow-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                            </svg>
                            Panier
                        </span>
                    </div>
                    <div class="absolute bottom-3 left-3">
                        <h3 class="text-2xl font-bold text-white drop-shadow-lg">{{ $bundle->name }}</h3>
                    </div>
                </div>
                @else
                <!-- Version gradient si pas d'image -->
                <div class="relative bg-gradient-to-br from-emerald-500 to-green-600 p-6 pb-8">
                    <div class="flex items-start justify-between mb-3">
                        <span class="inline-flex items-center gap-2 bg-white/90 text-emerald-700 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                            </svg>
                            Panier
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-white">{{ $bundle->name }}</h3>

                    <!-- Vague décorative -->
                    <div class="absolute bottom-0 left-0 right-0">
                        <svg viewBox="0 0 400 30" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                            <path d="M0 15C50 5 100 20 150 15C200 10 250 25 300 15C350 5 400 20 400 15V30H0V15Z" fill="white"/>
                        </svg>
                    </div>
                </div>
                @endif

                <div class="p-6 pt-2">
                    <p class="text-gray-600 mb-6 min-h-[3rem] leading-relaxed">{{ $bundle->description }}</p>

                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl p-4 mb-6 border-2 border-emerald-100">
                        <div class="flex items-baseline justify-between">
                            <div>
                                <div class="text-3xl font-bold text-emerald-700">
                                    {{ number_format($bundle->price_cents / 100, 2, ',', ' ') }} €
                                </div>
                                <p class="text-xs text-emerald-600 mt-1">Prix tout compris</p>
                            </div>
                            <div class="text-emerald-600">
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('cart.add.bundle', $bundle) }}" method="POST" class="space-y-4" data-cart-form>
                        @csrf
                        <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4">
                            <label class="text-sm font-semibold text-gray-700 flex-shrink-0">Quantité</label>
                            <input type="number" name="quantity" value="1" min="1" step="1"
                                class="flex-1 px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-semibold transition-all">
                        </div>
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-emerald-600 to-green-600 text-white px-6 py-4 rounded-xl font-semibold hover:from-emerald-700 hover:to-green-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>Ajouter au panier</span>
                        </button>
                    </form>

                    <a href="{{ route('bundles.show', $bundle) }}"
                        class="block mt-4 text-center text-emerald-600 hover:text-emerald-700 font-semibold hover:underline transition-colors">
                        Voir le détail du panier →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Produits par catégorie -->
    @php
        $categories = [
            'legume' => [
                'label' => 'Légumes Frais',
                'description' => 'Une sélection variée de légumes de saison',
                'icon' => '<path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z"/>',
                'gradient' => 'from-green-600 to-emerald-600',
                'bgColor' => 'from-green-50 to-emerald-50',
                'borderColor' => 'border-green-200'
            ],
            'volaille' => [
                'label' => 'Volaille Fermière',
                'description' => 'Élevage en plein air, qualité premium',
                'icon' => '<path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>',
                'gradient' => 'from-orange-600 to-amber-600',
                'bgColor' => 'from-orange-50 to-amber-50',
                'borderColor' => 'border-orange-200'
            ],
            'autre' => [
                'label' => 'Autres Produits',
                'description' => 'Découvrez nos spécialités du terroir',
                'icon' => '<path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
                'gradient' => 'from-amber-600 to-yellow-600',
                'bgColor' => 'from-amber-50 to-yellow-50',
                'borderColor' => 'border-amber-200'
            ]
        ];
    @endphp

    @foreach($categories as $categoryKey => $categoryData)
        @php
            $categoryProducts = $products->where('category', $categoryKey);
        @endphp

        @if($categoryProducts->count() > 0)
        <section id="produits" class="mb-20">
            <div class="flex items-center justify-between mb-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br {{ $categoryData['gradient'] }} rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            {!! $categoryData['icon'] !!}
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-4xl font-bold bg-gradient-to-r {{ $categoryData['gradient'] }} bg-clip-text text-transparent">
                            {{ $categoryData['label'] }}
                        </h2>
                        <p class="text-gray-600 mt-1">{{ $categoryData['description'] }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($categoryProducts as $product)
                <div class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-emerald-200 hover:-translate-y-1">
                    @if($product->image)
                    <div class="relative h-56 overflow-hidden bg-gray-50">
                        <img src="https://files-maraicher.fredlabs.org/{{ $product->image }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute top-3 right-3">
                            <span class="bg-white/95 backdrop-blur-sm text-gray-800 px-3 py-2 rounded-full text-xs font-bold shadow-lg border border-gray-200">
                                {{ $product->unit === 'kg' ? 'Au kilo' : 'À la pièce' }}
                            </span>
                        </div>
                        <!-- Badge qualité -->
                        <div class="absolute top-3 left-3">
                            <span class="bg-emerald-500 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-lg flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                <span>Frais</span>
                            </span>
                        </div>
                    </div>
                    @else
                    <div class="relative h-56 bg-gradient-to-br {{ $categoryData['bgColor'] }} flex items-center justify-center">
                        <span class="text-7xl filter drop-shadow-lg">
                            {{ $product->category === 'legume' ? '🥬' : ($product->category === 'volaille' ? '🐓' : '🌾') }}
                        </span>
                        <!-- Badge qualité -->
                        <div class="absolute top-3 left-3">
                            <span class="bg-emerald-500 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-lg flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                <span>Frais</span>
                            </span>
                        </div>
                    </div>
                    @endif

                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-emerald-700 transition-colors">
                            {{ $product->name }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2 min-h-[2.5rem] leading-relaxed">
                            {{ $product->description }}
                        </p>

                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-3 mb-4 border-2 {{ $categoryData['borderColor'] }}">
                            <div class="flex items-baseline gap-2">
                                <div class="text-2xl font-bold text-gray-800">
                                    {{ number_format($product->price_cents / 100, 2, ',', ' ') }} €
                                </div>
                                <span class="text-sm text-gray-500 font-medium">/ {{ $product->unit === 'kg' ? 'kg' : 'pièce' }}</span>
                            </div>
                        </div>

                        <form action="{{ route('cart.add.product', $product) }}" method="POST" class="space-y-3" data-cart-form>
                            @csrf
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                                <label class="text-xs font-semibold text-gray-600 flex-shrink-0">Qté</label>
                                <input type="number" name="quantity" value="1" min="{{ $product->unit === 'kg' ? '0.1' : '1' }}"
                                    step="{{ $product->unit === 'kg' ? '0.1' : '1' }}"
                                    class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-semibold text-sm transition-all">
                            </div>
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-emerald-600 to-green-600 text-white px-5 py-3 rounded-xl font-semibold hover:from-emerald-700 hover:to-green-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <span>Ajouter</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    @endforeach

    @if($products->count() === 0 && $bundles->count() === 0)
    <div class="text-center py-20 bg-gradient-to-br from-gray-50 to-emerald-50 rounded-3xl border-2 border-dashed border-gray-300">
        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">Bientôt disponible</h3>
        <p class="text-gray-600 text-lg">Nos produits frais arrivent très prochainement</p>
    </div>
    @endif

    <!-- Section Pourquoi nous choisir -->
    <div class="mt-24 mb-16 bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 rounded-3xl p-10 md:p-16 border border-emerald-100">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Pourquoi choisir nos produits ?</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">L'excellence au service de votre alimentation quotidienne</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl p-6 text-center shadow-md hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 mb-2 text-lg">100% Local</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Produits cultivés à proximité pour soutenir l'économie locale</p>
            </div>

            <div class="bg-white rounded-2xl p-6 text-center shadow-md hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 mb-2 text-lg">Fraîcheur Garantie</h3>
                <p class="text-sm text-gray-600 leading-relaxed">De la ferme à votre table, qualité et fraîcheur assurées</p>
            </div>

            <div class="bg-white rounded-2xl p-6 text-center shadow-md hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="w-14 h-14 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 mb-2 text-lg">Agriculture Familiale</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Savoir-faire transmis de génération en génération</p>
            </div>

            <div class="bg-white rounded-2xl p-6 text-center shadow-md hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 mb-2 text-lg">Pratiques Durables</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Respect de l'environnement et agriculture responsable</p>
            </div>
        </div>
    </div>
</div>
@endsection
