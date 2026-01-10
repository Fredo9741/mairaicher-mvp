@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mon Profil</h1>
        <p class="mt-2 text-gray-600">Gérez vos informations et consultez votre historique de commandes</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-100">
                <!-- Avatar -->
                <div class="flex flex-col items-center mb-6">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-emerald-500 shadow-lg">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center text-white font-bold text-3xl shadow-lg border-4 border-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <h3 class="mt-4 text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($user->role === 'developer') bg-purple-100 text-purple-800
                        @elseif($user->role === 'maraicher') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($user->role) }}
                    </span>
                    @if($user->provider)
                        <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Via {{ ucfirst($user->provider) }}
                        </span>
                    @endif
                </div>

                <!-- Info -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $user->email }}
                    </div>
                    @if($user->phone)
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $user->phone }}
                        </div>
                    @endif
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Membre depuis {{ $user->created_at->format('d/m/Y') }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        {{ $orders->total() }} commande(s)
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    <a href="{{ route('profile.edit') }}" class="block w-full text-center px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-semibold">
                        Modifier mon profil
                    </a>
                    @if($user->isAdmin())
                        <a href="/admin" class="block w-full text-center px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-semibold">
                            Accéder à l'administration
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Orders History -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Historique des commandes</h2>
                    <p class="text-sm text-gray-600 mt-1">Consultez toutes vos commandes passées</p>
                </div>

                <!-- Orders List -->
                <div class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <!-- Order Header -->
                            <div class="flex flex-wrap items-center justify-between mb-4">
                                <div>
                                    <h3 class="font-bold text-gray-900">
                                        Commande #{{ $order->order_number }}
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($order->status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <span class="text-lg font-bold text-emerald-600">{{ number_format($order->total_amount, 2) }} €</span>
                                </div>
                            </div>

                            <!-- Pickup Info -->
                            @if($order->pickupSlot)
                                <div class="mb-4 p-3 bg-emerald-50 rounded-lg flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-emerald-800">
                                        Retrait prévu le {{ \Carbon\Carbon::parse($order->pickupSlot->date)->format('d/m/Y') }}
                                        de {{ \Carbon\Carbon::parse($order->pickupSlot->start_time)->format('H:i') }}
                                        à {{ \Carbon\Carbon::parse($order->pickupSlot->end_time)->format('H:i') }}
                                    </span>
                                </div>
                            @endif

                            <!-- Order Items -->
                            <div class="space-y-2">
                                @foreach($order->items as $item)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-700">
                                            @if($item->product)
                                                {{ $item->product->name }}
                                            @elseif($item->bundle)
                                                🎁 {{ $item->bundle->name }}
                                            @endif
                                            <span class="text-gray-500">× {{ $item->quantity }}</span>
                                        </span>
                                        <span class="font-medium text-gray-900">{{ number_format($item->price * $item->quantity, 2) }} €</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 pt-4 border-t border-gray-200 flex gap-2">
                                <a href="{{ route('checkout.confirmation', $order) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium transition-colors">
                                    Voir les détails →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune commande</h3>
                            <p class="text-gray-600 mb-6">Vous n'avez pas encore passé de commande</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Découvrir nos produits
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
