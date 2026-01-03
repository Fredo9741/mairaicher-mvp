@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg shadow-xl p-8 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-4">Bienvenue au Domaine des Papangues</h1>
        <p class="text-xl">Légumes frais, volaille fermière et paniers de saison de l'océan Indien</p>
    </div>

    <!-- Paniers de saison -->
    @if($bundles->count() > 0)
    <section class="mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Nos Paniers de Saison</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($bundles as $bundle)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $bundle->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $bundle->description }}</p>
                    <div class="text-2xl font-bold text-green-600 mb-4">
                        {{ number_format($bundle->price_cents / 100, 2, ',', ' ') }} €
                    </div>

                    <form action="{{ route('cart.add.bundle', $bundle) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                            <input type="number" name="quantity" value="1" min="1" step="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <button type="submit"
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                            Ajouter au panier
                        </button>
                    </form>

                    <a href="{{ route('bundles.show', $bundle) }}"
                        class="block mt-3 text-center text-green-600 hover:text-green-700">
                        Voir détails →
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
            'legume' => 'Légumes',
            'volaille' => 'Volaille',
            'autre' => 'Autres Produits'
        ];
    @endphp

    @foreach($categories as $categoryKey => $categoryLabel)
        @php
            $categoryProducts = $products->where('category', $categoryKey);
        @endphp

        @if($categoryProducts->count() > 0)
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">{{ $categoryLabel }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($categoryProducts as $product)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $product->description }}</p>

                        <div class="flex justify-between items-center mb-4">
                            <div class="text-xl font-bold text-green-600">
                                {{ number_format($product->price_cents / 100, 2, ',', ' ') }} €
                            </div>
                            <span class="text-sm text-gray-500">/ {{ $product->unit === 'kg' ? 'kg' : 'pièce' }}</span>
                        </div>

                        <div class="mb-4">
                            <span class="text-sm font-medium text-gray-700">
                                Stock: {{ $product->stock }} {{ $product->unit === 'kg' ? 'kg' : 'pièces' }}
                            </span>
                        </div>

                        <form action="{{ route('cart.add.product', $product) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                                <input type="number" name="quantity" value="1" min="{{ $product->unit === 'kg' ? '0.1' : '1' }}"
                                    step="{{ $product->unit === 'kg' ? '0.1' : '1' }}" max="{{ $product->stock }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <button type="submit"
                                class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                                Ajouter au panier
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
    <div class="text-center py-12">
        <p class="text-xl text-gray-600">Aucun produit disponible pour le moment.</p>
    </div>
    @endif
</div>
@endsection
