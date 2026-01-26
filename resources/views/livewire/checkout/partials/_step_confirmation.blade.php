{{-- Étape 3 : Confirmation de la commande --}}
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    {{-- En-tête de l'étape --}}
    <div
        class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition"
        @click="stepCompleted[2] ? (currentStep = 3) : null"
        :class="{
            'bg-green-50': stepCompleted[3],
            'border-l-4 border-green-600': currentStep === 3,
            'opacity-50 cursor-not-allowed': !stepCompleted[2]
        }"
    >
        <div class="flex items-center gap-4">
            {{-- Indicateur d'étape --}}
            <div
                class="flex items-center justify-center w-10 h-10 rounded-full border-2 font-bold"
                :class="{
                    'bg-green-600 text-white border-green-600': stepCompleted[3],
                    'bg-blue-600 text-white border-blue-600': currentStep === 3 && !stepCompleted[3],
                    'bg-gray-100 text-gray-400 border-gray-300': currentStep !== 3 && !stepCompleted[3]
                }"
            >
                <span x-show="!stepCompleted[3]">3</span>
                <svg x-show="stepCompleted[3]" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <div>
                <h2 class="text-xl font-bold text-gray-800">Confirmation de votre commande</h2>
                <p class="text-sm text-gray-600" x-show="!stepCompleted[2]">
                    Complétez les étapes précédentes pour continuer
                </p>
                <p class="text-sm text-green-600" x-show="stepCompleted[2] && !stepCompleted[3]">
                    Vérifiez et confirmez votre commande
                </p>
                <p class="text-sm text-green-700 font-medium" x-show="stepCompleted[3]">
                    ✓ Commande validée
                </p>
            </div>
        </div>

        {{-- Icône expand/collapse --}}
        <svg
            class="w-6 h-6 text-gray-400 transition-transform"
            :class="{'rotate-180': currentStep === 3}"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    {{-- Contenu de l'étape --}}
    <div
        x-show="currentStep === 3"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="p-6 border-t border-gray-200"
    >
        {{-- Récapitulatif de la commande --}}
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Récapitulatif de votre commande
            </h3>

            {{-- Informations client --}}
            <div class="mb-6 p-4 bg-white rounded-lg border border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Vos informations
                </h4>
                <div class="space-y-2 text-sm">
                    <p><strong class="text-gray-700">Nom :</strong> <span class="text-gray-600">{{ $customer_name }}</span></p>
                    <p><strong class="text-gray-700">Email :</strong> <span class="text-gray-600">{{ $customer_email }}</span></p>
                    <p><strong class="text-gray-700">Téléphone :</strong> <span class="text-gray-600">{{ $customer_phone }}</span></p>
                    @if($notes)
                        <p><strong class="text-gray-700">Notes :</strong> <span class="text-gray-600 italic">{{ $notes }}</span></p>
                    @endif
                </div>
            </div>

            {{-- Point de retrait --}}
            @if($selectedPickupSlotId)
                @php $selectedPoint = $pickupPoints->firstWhere('id', $selectedPickupSlotId); @endphp
                @if($selectedPoint)
                    <div class="mb-6 p-4 bg-white rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Point de retrait
                        </h4>
                        <div class="space-y-2 text-sm">
                            <p><strong class="text-gray-700">Lieu :</strong> <span class="text-gray-600">{{ $selectedPoint['name'] }}</span></p>
                            @if($selectedPoint['address'])
                                <p><strong class="text-gray-700">Adresse :</strong> <span class="text-gray-600">{{ $selectedPoint['address'] }}</span></p>
                            @endif
                            @if($pickupDate)
                                <p><strong class="text-gray-700">Date :</strong> <span class="text-gray-600">{{ \Carbon\Carbon::parse($pickupDate)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span></p>
                            @endif
                            @if($selectedTimeSlot)
                                <p><strong class="text-gray-700">Horaire :</strong> <span class="text-gray-600">{{ str_replace('-', ' - ', $selectedTimeSlot) }}</span></p>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- Articles commandés --}}
            <div class="p-4 bg-white rounded-lg border border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Vos articles
                </h4>
                <div class="space-y-3">
                    @foreach($cart as $item)
                        <div class="flex justify-between items-start text-sm pb-3 border-b border-gray-100 last:border-0">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $item['name'] }}</p>
                                <p class="text-gray-500 text-xs mt-1">
                                    @if($item['type'] === 'product')
                                        Quantité : {{ $item['quantity'] }} {{ $item['unit'] === 'kg' ? 'kg' : 'pièce(s)' }}
                                    @else
                                        Quantité : {{ $item['quantity'] }} panier(s)
                                    @endif
                                </p>
                            </div>
                            <p class="font-semibold text-gray-800 ml-4">
                                {{ number_format(($item['price_cents'] * $item['quantity']) / 100, 2, ',', ' ') }} €
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- Total --}}
                <div class="mt-4 pt-4 border-t-2 border-gray-300">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Total à payer</span>
                        <span class="text-2xl font-bold text-green-600">
                            {{ number_format($total / 100, 2, ',', ' ') }} €
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Note de paiement --}}
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-blue-800 font-medium">
                        <strong>Information importante :</strong>
                    </p>
                    <p class="text-sm text-blue-700 mt-1">
                        Le paiement s'effectue en ligne via <strong>Stripe</strong>, une plateforme de paiement sécurisée. En cliquant sur "Confirmer ma commande", une nouvelle page s'ouvrira pour finaliser votre paiement.
                    </p>
                </div>
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-between">
            <button
                type="button"
                @click="currentStep = 2"
                class="order-2 sm:order-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition font-medium"
            >
                <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Retour
            </button>
            <button
                wire:click="submitOrder"
                wire:loading.attr="disabled"
                class="order-1 sm:order-2 flex-1 sm:flex-initial px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition font-bold text-lg shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove class="flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Confirmer ma commande
                </span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Traitement en cours...
                </span>
            </button>
        </div>
    </div>
</div>
