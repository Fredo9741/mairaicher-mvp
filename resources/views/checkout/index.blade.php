@extends('layouts.app')

@section('title', 'Commander')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Finaliser la commande</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulaire -->
        <div class="lg:col-span-2">
            <form action="{{ route('checkout.store') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6 space-y-6">
                @csrf

                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Vos informations</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                            <input type="text" name="customer_name" id="customer_name" required
                                value="{{ old('customer_name', $user->name ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="customer_email" id="customer_email" required
                                value="{{ old('customer_email', $user->email ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                            <input type="tel" name="customer_phone" id="customer_phone" required
                                value="{{ old('customer_phone', $user->phone ?? '') }}"
                                placeholder="+262 692 XX XX XX"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Retrait de la commande</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="pickup_date" class="block text-sm font-medium text-gray-700 mb-1">Date de retrait *</label>
                            <input type="date" name="pickup_date" id="pickup_date" required
                                value="{{ old('pickup_date') }}"
                                min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label for="pickup_slot_id" class="block text-sm font-medium text-gray-700 mb-1">Créneau horaire *</label>
                            <select name="pickup_slot_id" id="pickup_slot_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Choisissez un créneau</option>
                                @foreach($pickupSlots as $slot)
                                    <option value="{{ $slot->id }}" {{ old('pickup_slot_id') == $slot->id ? 'selected' : '' }}>
                                        {{ $slot->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optionnel)</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <a href="{{ route('cart.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                        Retour au panier
                    </a>
                    <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-bold">
                        Confirmer la commande
                    </button>
                </div>
            </form>
        </div>

        <!-- Récapitulatif -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Récapitulatif</h2>

                <div class="space-y-3 mb-6">
                    @php
                        $total = 0;
                    @endphp

                    @foreach($cart as $item)
                        @php
                            $itemTotal = $item['price_cents'] * $item['quantity'];
                            $total += $itemTotal;
                        @endphp

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">
                                {{ $item['name'] }}
                                @if($item['type'] === 'product')
                                    ({{ $item['quantity'] }} {{ $item['unit'] === 'kg' ? 'kg' : 'pc' }})
                                @else
                                    (x{{ $item['quantity'] }})
                                @endif
                            </span>
                            <span class="font-medium text-gray-800">
                                {{ number_format($itemTotal / 100, 2, ',', ' ') }} €
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-gray-800">Total</span>
                        <span class="text-2xl font-bold text-green-600">
                            {{ number_format($total / 100, 2, ',', ' ') }} €
                        </span>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-md">
                    <p class="text-sm text-blue-800">
                        <strong>Note:</strong> Le paiement se fera lors du retrait de votre commande.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
