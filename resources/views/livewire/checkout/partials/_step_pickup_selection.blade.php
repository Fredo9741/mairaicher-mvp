{{-- Étape 2 : Sélection du point de retrait et horaire --}}
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    {{-- En-tête de l'étape --}}
    <div
        class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition"
        @click="stepCompleted[1] ? (currentStep = 2) : null"
        :class="{
            'bg-green-50': stepCompleted[2],
            'border-l-4 border-green-600': currentStep === 2,
            'opacity-50 cursor-not-allowed': !stepCompleted[1]
        }"
    >
        <div class="flex items-center gap-4">
            {{-- Indicateur d'étape --}}
            <div
                class="flex items-center justify-center w-10 h-10 rounded-full border-2 font-bold"
                :class="{
                    'bg-green-600 text-white border-green-600': stepCompleted[2],
                    'bg-blue-600 text-white border-blue-600': currentStep === 2 && !stepCompleted[2],
                    'bg-gray-100 text-gray-400 border-gray-300': currentStep !== 2 && !stepCompleted[2]
                }"
            >
                <span x-show="!stepCompleted[2]">2</span>
                <svg x-show="stepCompleted[2]" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <div>
                <h2 class="text-xl font-bold text-gray-800">Point de retrait et horaire</h2>
                <p class="text-sm text-gray-600" x-show="stepCompleted[2]">
                    @if($selectedPickupSlotId)
                        @php $selectedPoint = $pickupPoints->firstWhere('id', $selectedPickupSlotId); @endphp
                        {{ $selectedPoint['name'] ?? 'Point sélectionné' }}
                        @if($pickupDate && $selectedTimeSlot)
                            • {{ \Carbon\Carbon::parse($pickupDate)->format('d/m/Y') }} {{ str_replace('-', ' - ', $selectedTimeSlot) }}
                        @endif
                    @endif
                </p>
                <p class="text-sm text-orange-600" x-show="!stepCompleted[1]">
                    Complétez d'abord vos informations personnelles
                </p>
            </div>
        </div>

        {{-- Icône expand/collapse --}}
        <svg
            class="w-6 h-6 text-gray-400 transition-transform"
            :class="{'rotate-180': currentStep === 2}"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    {{-- Contenu de l'étape --}}
    <div
        x-show="currentStep === 2"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="p-6 border-t border-gray-200"
    >
        {{-- Carte interactive avec Alpine.data --}}
        <div x-data="checkoutMap({{ Js::from($pickupPoints) }})" class="mb-6">
            {{-- Vue carte --}}
            <div x-show="!showListView" class="relative z-0">
                <div wire:ignore>
                    <div
                        x-ref="mapContainer"
                        class="w-full rounded-lg border-2 border-gray-300 relative z-0"
                        style="height: 400px;"
                    ></div>
                </div>
                <div class="mt-2 flex justify-between items-center">
                    <p class="text-sm text-gray-600">
                        Cliquez sur un marqueur pour sélectionner votre point de retrait
                    </p>
                    <button
                        type="button"
                        @click="showListView = true"
                        class="inline-flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 px-3 py-1 rounded transition focus:outline-none focus:ring-2 focus:ring-gray-500"
                        aria-label="Retour à la liste des points de retrait"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        Retour à la liste
                    </button>
                </div>
            </div>

            {{-- Vue liste (affichée par défaut) --}}
            <div x-show="showListView">
                <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800 font-semibold">
                        👉 <strong>Sélectionnez un lieu de rendez-vous :</strong>
                    </p>
                    <p class="text-xs text-blue-700 mt-1">
                        Cliquez sur un point ci-dessous ou utilisez "Voir sur la carte" pour une vue géographique.
                    </p>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto border border-gray-300 rounded-lg p-4">
                    <template x-for="point in pickupPoints" :key="point.id">
                        <div
                            class="p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                            :class="selectedMarkerId === point.id ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                            @click="selectedMarkerId = point.id"
                        >
                            <h4 class="font-bold text-gray-900" x-text="point.name"></h4>
                            <p class="text-sm text-gray-600 mt-1" x-text="point.address"></p>
                            <div class="mt-2 text-xs text-gray-700">
                                <strong>Horaires :</strong>
                                <div x-html="formatHours(point.working_hours)"></div>
                            </div>
                            <div class="mt-3 text-xs text-green-700 font-medium" x-show="selectedMarkerId === point.id">
                                ✓ Point sélectionné
                            </div>
                        </div>
                    </template>
                </div>
                <div class="mt-2">
                    <button
                        type="button"
                        @click="showListView = false; $nextTick(() => { if (!map) initMap(); if(selectedMarkerId) selectMarker(selectedMarkerId); })"
                        class="inline-flex items-center gap-2 text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Afficher la carte interactive"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Voir sur la carte
                    </button>
                </div>
            </div>
        </div>

        {{-- Point sélectionné --}}
        @if($selectedPickupSlotId)
            @php $selectedPoint = $pickupPoints->firstWhere('id', $selectedPickupSlotId); @endphp
            @if($selectedPoint)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="h-6 w-6 text-green-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-green-900">
                                Point sélectionné : {{ $selectedPoint['name'] }}
                            </h4>
                            @if($selectedPoint['address'])
                                <p class="text-sm text-green-700 mt-1">{{ $selectedPoint['address'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif

        @error('selectedPickupSlotId')
            <p class="text-sm text-red-600 mb-4">{{ $message }}</p>
        @enderror

        {{-- Date et horaire --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="pickupDate" class="block text-sm font-medium text-gray-700 mb-1">
                    Date de retrait <span class="text-red-600">*</span>
                </label>
                <div x-data="checkoutDatePicker(@entangle('availableDays').live, @entangle('pickupDate').live)">
                    <input
                        type="text"
                        x-ref="dateInput"
                        id="pickupDate"
                        placeholder="Sélectionnez une date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('pickupDate') border-red-500 @enderror"
                        readonly
                    >
                </div>
                @if($selectedPickupSlotId && empty($availableDays))
                    <p class="mt-1 text-xs text-orange-600">
                        ⚠️ Ce point de retrait n'a pas de jours d'ouverture définis.
                    </p>
                @elseif($selectedPickupSlotId && !empty($availableDays))
                    <p class="mt-1 text-xs text-gray-600">
                        💡 Seuls les jours d'ouverture du point sont sélectionnables
                    </p>
                @endif
                @error('pickupDate')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="timeSlot" class="block text-sm font-medium text-gray-700 mb-1">
                    Créneau horaire <span class="text-red-600">*</span>
                </label>

                {{-- Indicateur de chargement --}}
                <div wire:loading wire:target="pickupDate" class="mb-2 text-xs text-blue-600 flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Mise à jour des créneaux...</span>
                </div>

                @if(count($availableTimeSlots) > 0)
                    <select
                        id="timeSlot"
                        wire:model="selectedTimeSlot"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('selectedTimeSlot') border-red-500 @enderror">
                        <option value="">Sélectionnez un horaire</option>
                        @foreach($availableTimeSlots as $slot)
                            <option value="{{ $slot['value'] }}">{{ $slot['label'] }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full px-4 py-3 border border-gray-300 bg-gray-50 rounded-md text-sm text-gray-500">
                        {{ ($selectedPickupSlotId && $pickupDate) ? 'Aucun créneau disponible pour cette date' : 'Sélectionnez un point de retrait et une date' }}
                    </div>
                @endif
                @error('selectedTimeSlot')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Boutons navigation --}}
        <div class="flex justify-between">
            <button
                type="button"
                @click="currentStep = 1"
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition"
            >
                <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Retour
            </button>
            <button
                type="button"
                @click="validateStep2()"
                class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-medium"
            >
                Continuer
                <svg class="inline w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
