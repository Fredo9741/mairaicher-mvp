<div class="min-h-screen bg-gray-50 pb-12">
    {{-- Indicateur de chargement global --}}
    <x-page-loading />

    {{-- En-tête --}}
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white py-8 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Finaliser votre commande</h1>
                    <p class="text-green-100">Complétez les étapes ci-dessous pour valider votre achat</p>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-6 py-4">
                        <p class="text-sm text-green-100">Total à payer</p>
                        <p class="text-3xl font-bold">{{ number_format($total / 100, 2, ',', ' ') }} €</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages et conteneur principal --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Messages de succès/erreur --}}
        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-r-lg shadow-md" role="alert">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <strong class="font-bold">Erreur!</strong>
                        <span class="block sm:inline ml-2">{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Conteneur principal avec Alpine.js --}}
        <div x-data="checkoutSteps()" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Colonne gauche : Formulaire par étapes --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Indicateur de progression (barre) --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progression</span>
                        <span class="text-sm font-medium text-gray-700" x-text="(Object.values(stepCompleted).filter(v => v).length) + ' / 3 étapes'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-500 ease-out"
                            :style="`width: ${(Object.values(stepCompleted).filter(v => v).length / 3) * 100}%`"
                        ></div>
                    </div>
                </div>

                {{-- Étape 1 : Informations client --}}
                @include('livewire.checkout.partials._step_customer_info')

                {{-- Étape 2 : Point de retrait --}}
                @include('livewire.checkout.partials._step_pickup_selection')

                {{-- Étape 3 : Confirmation --}}
                @include('livewire.checkout.partials._step_confirmation')

                {{-- Bouton retour au panier --}}
                <div class="flex justify-start">
                    <a
                        href="{{ route('cart.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition font-medium"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Retour au panier
                    </a>
                </div>
            </div>

            {{-- Colonne droite : Récapitulatif simplifié (visible sur desktop) --}}
            <div class="lg:col-span-1">
                <div class="hidden lg:block sticky top-8">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Votre panier</h2>

                        <div class="space-y-3 mb-6">
                            @foreach($cart as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">
                                        {{ $item['name'] }}
                                        @if($item['type'] === 'product')
                                            <span class="text-gray-500">({{ $item['quantity'] }} {{ $item['unit'] === 'kg' ? 'kg' : 'pc' }})</span>
                                        @else
                                            <span class="text-gray-500">(x{{ $item['quantity'] }})</span>
                                        @endif
                                    </span>
                                    <span class="font-medium text-gray-800">
                                        {{ number_format(($item['price_cents'] * $item['quantity']) / 100, 2, ',', ' ') }} €
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total --}}
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-gray-800">Total</span>
                                <span class="text-2xl font-bold text-green-600">
                                    {{ number_format($total / 100, 2, ',', ' ') }} €
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chargement de Leaflet CSS et JS --}}
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
    <style>
        /* Style pour x-cloak (masque les éléments avant que Alpine soit chargé) */
        [x-cloak] { display: none !important; }
    </style>
@endpush

@push('scripts')
    {{-- Leaflet --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    {{-- Alpine.js checkout logic --}}
    <script src="{{ asset('js/checkout-alpine.js') }}"></script>
@endpush
