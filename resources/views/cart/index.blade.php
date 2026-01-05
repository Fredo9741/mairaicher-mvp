@extends('layouts.app')

@section('title', 'Mon Panier')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Mon Panier</h1>

    @if(empty($cart))
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-xl text-gray-600 mb-4">Votre panier est vide</p>
            <a href="{{ route('home') }}" class="inline-block bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">
                Voir nos produits
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            @php
                $total = 0;
            @endphp

            @foreach($cart as $key => $item)
                @php
                    $itemTotal = $item['price_cents'] * $item['quantity'];
                    $total += $itemTotal;
                @endphp

                <div class="border-b border-gray-200 p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800">{{ $item['name'] }}</h3>
                            <p class="text-gray-600 text-sm">
                                @if($item['type'] === 'product')
                                    {{ number_format($item['price_cents'] / 100, 2, ',', ' ') }} € / {{ $item['unit'] === 'kg' ? 'kg' : 'pièce' }}
                                @else
                                    Panier de saison - {{ number_format($item['price_cents'] / 100, 2, ',', ' ') }} €
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center justify-between sm:space-x-4">
                            <form action="{{ route('cart.update', $key) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                    min="{{ $item['type'] === 'product' && $item['unit'] === 'kg' ? '0.1' : '1' }}"
                                    step="{{ $item['type'] === 'product' && $item['unit'] === 'kg' ? '0.1' : '1' }}"
                                    class="w-16 sm:w-20 px-2 py-1 border border-gray-300 rounded-md text-center text-sm"
                                    onchange="this.form.submit()">
                                <span class="text-gray-600 text-sm">
                                    @if($item['type'] === 'product')
                                        {{ $item['unit'] === 'kg' ? 'kg' : 'pc' }}
                                    @else
                                        pc
                                    @endif
                                </span>
                            </form>

                            <div class="text-base sm:text-lg font-bold text-gray-800 min-w-[80px] text-right">
                                {{ number_format($itemTotal / 100, 2, ',', ' ') }} €
                            </div>

                            <form action="{{ route('cart.remove', $key) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Total et actions -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
            <div class="flex justify-between items-center mb-6">
                <span class="text-xl sm:text-2xl font-bold text-gray-800">Total</span>
                <span class="text-2xl sm:text-3xl font-bold text-green-600">{{ number_format($total / 100, 2, ',', ' ') }} €</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-between">
                <form action="{{ route('cart.clear') }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto px-4 sm:px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition text-sm sm:text-base">
                        Vider le panier
                    </button>
                </form>

                <div class="flex flex-col sm:flex-row gap-3 sm:space-x-4">
                    <a href="{{ route('home') }}" class="w-full sm:w-auto text-center px-4 sm:px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition text-sm sm:text-base">
                        Continuer mes achats
                    </a>
                    <a href="{{ route('checkout.index') }}" class="w-full sm:w-auto text-center px-4 sm:px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm sm:text-base font-semibold">
                        Passer commande
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
