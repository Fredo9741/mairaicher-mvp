@extends('layouts.app')

@section('title', 'Commande confirmée')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg p-8">

        {{-- Messages flash --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Message de succès -->
        <div class="text-center mb-8">
            @if($order->status === 'paid')
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Paiement confirmé !</h1>
                <p class="text-lg text-gray-600">
                    Merci {{ $order->customer_name }}, votre paiement a été effectué avec succès.
                </p>
            @else
                <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Commande enregistrée</h1>
                <p class="text-lg text-gray-600">
                    Merci {{ $order->customer_name }}, votre commande a bien été enregistrée.
                </p>
            @endif
        </div>

        <!-- Informations de commande -->
        <div class="border-t border-gray-200 pt-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-3">Détails de la commande</h2>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium">N° de commande:</span> <span class="text-green-600 font-bold">{{ $order->order_number }}</span></p>
                        <p><span class="font-medium">Email:</span> {{ $order->customer_email }}</p>
                        <p><span class="font-medium">Téléphone:</span> {{ $order->customer_phone }}</p>
                        <p><span class="font-medium">Statut:</span>
                            @switch($order->status)
                                @case('paid')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Payée
                                    </span>
                                    @break
                                @case('ready')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Prête
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Terminée
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Annulée
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        En attente de paiement
                                    </span>
                            @endswitch
                        </p>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-3">Informations de retrait</h2>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium">Date:</span> {{ \Carbon\Carbon::parse($order->pickup_date)->format('d/m/Y') }}</p>

                        @if($order->pickupSlot)
                            <p><span class="font-medium">Lieu:</span> {{ $order->pickupSlot->name }}</p>

                            @if($order->pickupSlot->address)
                                <p><span class="font-medium">Adresse:</span> {{ $order->pickupSlot->address }}</p>
                            @endif

                            @if($order->pickupSlot->lat && $order->pickupSlot->lng)
                                <p>
                                    <span class="font-medium">GPS:</span>
                                    <a href="https://www.google.com/maps?q={{ $order->pickupSlot->lat }},{{ $order->pickupSlot->lng }}"
                                       target="_blank"
                                       class="text-green-600 hover:text-green-700 underline">
                                        Voir sur la carte
                                    </a>
                                </p>
                            @endif

                            @if($order->pickupSlot->working_hours && !empty($order->pickupSlot->working_hours))
                                @php
                                    $dayOfWeek = strtolower(\Carbon\Carbon::parse($order->pickup_date)->locale('en')->dayName);
                                    $daySchedule = collect($order->pickupSlot->working_hours)->firstWhere('day', $dayOfWeek);
                                @endphp

                                @if($daySchedule && (!isset($daySchedule['closed']) || !$daySchedule['closed']))
                                    <p>
                                        <span class="font-medium">Horaires du jour:</span>
                                        {{ substr($daySchedule['open'], 0, 5) }} - {{ substr($daySchedule['close'], 0, 5) }}
                                    </p>
                                @endif
                            @endif
                        @endif

                        @if($order->notes)
                            <p><span class="font-medium">Notes:</span> {{ $order->notes }}</p>
                        @endif
                    </div>

                    <div class="mt-4 p-3 bg-blue-50 rounded-md">
                        <p class="text-xs text-blue-800">
                            <strong>Important:</strong> Veuillez vous présenter aux horaires indiqués pour récupérer votre commande.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles commandés -->
        <div class="border-t border-gray-200 pt-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Articles commandés</h2>

            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-2">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">{{ $item->item_name }}</p>
                            <p class="text-sm text-gray-600">
                                Quantité: {{ $item->quantity }}
                                @if($item->item_type === 'product')
                                    @php
                                        $product = \App\Models\Product::find($item->item_id);
                                    @endphp
                                    {{ $product ? ($product->unit === 'kg' ? 'kg' : 'pièce(s)') : '' }}
                                @else
                                    panier(s)
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-800">{{ number_format($item->total_price_cents / 100, 2, ',', ' ') }} €</p>
                            <p class="text-sm text-gray-600">{{ number_format($item->unit_price_cents / 100, 2, ',', ' ') }} € / unité</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Total -->
        <div class="border-t border-gray-200 pt-6 mb-8">
            <div class="flex justify-between items-center">
                <span class="text-xl font-bold text-gray-800">Total</span>
                <span class="text-2xl font-bold text-green-600">
                    {{ number_format($order->total_price_cents / 100, 2, ',', ' ') }} €
                </span>
            </div>
        </div>

        <!-- Section Paiement -->
        @if($order->status === 'paid')
            {{-- Paiement effectué --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-bold text-green-800">Paiement effectué</h3>
                        <p class="text-green-700">
                            Votre paiement de {{ number_format($order->total_price_cents / 100, 2, ',', ' ') }} € a été confirmé.
                        </p>
                    </div>
                </div>
            </div>
        @else
            {{-- Paiement en attente - Proposer de payer maintenant --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-yellow-800 mb-2">Paiement en attente</h3>
                <p class="text-yellow-700 mb-4">
                    Votre commande est en attente de paiement. Vous pouvez payer maintenant en ligne ou lors du retrait.
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('stripe.checkout', $order) }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Payer maintenant ({{ number_format($order->total_price_cents / 100, 2, ',', ' ') }} €)
                    </a>

                    <span class="text-sm text-yellow-600 self-center">
                        ou payez en espèces/CB lors du retrait
                    </span>
                </div>
            </div>
        @endif

        <!-- Email de confirmation -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-8">
            <p class="text-sm text-gray-700">
                <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Un email de confirmation a été envoyé à <strong>{{ $order->customer_email }}</strong>
            </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('home') }}" class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                Retour à l'accueil
            </a>
            <button onclick="window.print()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                Imprimer
            </button>
        </div>
    </div>
</div>

<style>
    @media print {
        nav, footer, button {
            display: none !important;
        }
    }
</style>
@endsection
