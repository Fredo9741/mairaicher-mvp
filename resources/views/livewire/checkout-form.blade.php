<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Finaliser la commande</h1>

    {{-- Messages de succès/erreur --}}
    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Erreur!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Récapitulatif rapide mobile (sticky top) --}}
    <div class="lg:hidden mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 sticky top-20 z-40 shadow-md">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-600">Total de votre commande</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($total / 100, 2, ',', ' ') }} €</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">{{ count($cart) }} article{{ count($cart) > 1 ? 's' : '' }}</p>
                <p class="text-xs text-blue-800 mt-1">
                    <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Paiement au retrait
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulaire -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations client -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Vos informations</h2>

                <div class="space-y-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                        <input
                            type="text"
                            id="customer_name"
                            wire:model="customer_name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('customer_name') border-red-500 @enderror">
                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input
                            type="email"
                            id="customer_email"
                            wire:model="customer_email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('customer_email') border-red-500 @enderror">
                        @error('customer_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                        <input
                            type="tel"
                            id="customer_phone"
                            wire:model="customer_phone"
                            placeholder="+262 692 XX XX XX"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('customer_phone') border-red-500 @enderror">
                        @error('customer_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optionnel)</label>
                        <textarea
                            id="notes"
                            wire:model="notes"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Informations complémentaires..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Sélection du point de retrait avec carte -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Point de retrait et horaire</h2>

                {{-- Carte interactive --}}
                <div
                    x-data="{
                        map: null,
                        markers: {},
                        selectedMarkerId: $wire.entangle('selectedPickupSlotId').live,
                        pickupPoints: {{ Js::from($pickupPoints) }},
                        showListView: false,

                        init() {
                            this.$nextTick(() => {
                                this.initMap();
                            });

                            // Watch pour appeler la méthode Livewire quand on change de point
                            this.$watch('selectedMarkerId', (value) => {
                                if (value) {
                                    this.$wire.selectPickupPoint(value);
                                }
                            });
                        },

                        // Nettoie la carte lors de la destruction du composant
                        destroy() {
                            if (this.map) {
                                this.map.remove();
                                this.map = null;
                            }
                        },

                        // Formate les horaires de manière lisible
                        formatHours(hoursString) {
                            if (!hoursString || hoursString === 'Horaires non définis') {
                                return 'Horaires non définis';
                            }

                            // Le string contient déjà le format lisible avec <br>
                            // On le retourne directement
                            return hoursString;
                        },

                        initMap() {
                            // Initialise la carte centrée sur Saint-Leu
                            this.map = L.map(this.$refs.mapContainer).setView([-21.1705, 55.2886], 13);

                            // Ajoute la couche de tuiles OpenStreetMap
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a>',
                                maxZoom: 19
                            }).addTo(this.map);

                            // Ajoute tous les points de retrait comme marqueurs
                            this.addMarkers();
                        },

                        addMarkers() {
                            this.pickupPoints.forEach((point) => {
                                // Icône pour les marqueurs
                                const icon = L.icon({
                                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                    iconSize: [25, 41],
                                    iconAnchor: [12, 41],
                                    popupAnchor: [1, -34],
                                    shadowSize: [41, 41]
                                });

                                // Crée le conteneur de la popup
                                const popupContainer = document.createElement('div');
                                popupContainer.className = 'p-2';

                                // Titre
                                const title = document.createElement('h4');
                                title.className = 'font-bold text-gray-900';
                                title.textContent = point.name;
                                popupContainer.appendChild(title);

                                // Adresse (si disponible)
                                if (point.address) {
                                    const address = document.createElement('p');
                                    address.className = 'text-sm text-gray-600 mt-1';
                                    address.textContent = point.address;
                                    popupContainer.appendChild(address);
                                }

                                // Horaires
                                const hoursContainer = document.createElement('div');
                                hoursContainer.className = 'mt-2 text-xs text-gray-700';
                                const hoursLabel = document.createElement('strong');
                                hoursLabel.textContent = 'Horaires :';
                                hoursContainer.appendChild(hoursLabel);
                                hoursContainer.appendChild(document.createElement('br'));

                                const hoursText = document.createElement('div');
                                hoursText.innerHTML = this.formatHours(point.working_hours);
                                hoursContainer.appendChild(hoursText);
                                popupContainer.appendChild(hoursContainer);

                                // Bouton de sélection
                                const button = document.createElement('button');
                                button.className = 'mt-3 w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded transition';
                                button.textContent = 'Sélectionner ce point';

                                // Attache l'événement au bouton
                                button.addEventListener('click', (e) => {
                                    e.preventDefault();
                                    this.selectMarker(point.id);
                                    this.selectedMarkerId = point.id;
                                    this.map.closePopup();
                                });

                                popupContainer.appendChild(button);

                                // Crée et ajoute le marqueur
                                const marker = L.marker([point.lat, point.lng], { icon })
                                    .addTo(this.map)
                                    .bindPopup(popupContainer, {
                                        maxWidth: 300
                                    });

                                // Événement de clic sur le marqueur
                                marker.on('click', () => {
                                    this.selectMarker(point.id);
                                });

                                this.markers[point.id] = marker;
                            });

                            // Ajuste la vue pour montrer tous les marqueurs
                            if (this.pickupPoints.length > 0) {
                                const bounds = L.latLngBounds(this.pickupPoints.map(p => [p.lat, p.lng]));
                                this.map.fitBounds(bounds, { padding: [50, 50] });
                            }
                        },

                        selectMarker(pointId) {
                            // Met en surbrillance le marqueur sélectionné
                            Object.keys(this.markers).forEach(id => {
                                const marker = this.markers[id];
                                const isSelected = parseInt(id) === parseInt(pointId);

                                const iconUrl = isSelected
                                    ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png'
                                    : 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png';

                                marker.setIcon(L.icon({
                                    iconUrl,
                                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                    iconSize: [25, 41],
                                    iconAnchor: [12, 41],
                                    popupAnchor: [1, -34],
                                    shadowSize: [41, 41]
                                }));
                            });
                        }
                    }"
                    class="mb-4"
                >
                    {{-- Carte --}}
                    <div x-show="!showListView">
                        <div wire:ignore>
                            <div
                                x-ref="mapContainer"
                                class="w-full rounded-lg border-2 border-gray-300"
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
                                class="text-sm text-blue-600 hover:text-blue-800 underline focus:outline-none focus:ring-2 focus:ring-blue-500 rounded px-2 py-1"
                                aria-label="Afficher la liste des points de retrait"
                            >
                                Afficher la liste
                            </button>
                        </div>
                    </div>

                    {{-- Vue liste alternative (accessibilité) --}}
                    <div x-show="showListView" style="display: none;">
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
                        <div class="mt-2 flex justify-between items-center">
                            <button
                                type="button"
                                @click="showListView = false; if(selectedMarkerId) { selectMarker(selectedMarkerId) }"
                                class="text-sm text-blue-600 hover:text-blue-800 underline focus:outline-none focus:ring-2 focus:ring-blue-500 rounded px-2 py-1"
                                aria-label="Afficher la carte"
                            >
                                Afficher la carte
                            </button>
                            <p class="text-xs text-gray-500" x-show="selectedMarkerId">
                                Point sélectionné
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Point sélectionné --}}
                @if($selectedPickupSlotId)
                    @php
                        $selectedPoint = $pickupPoints->firstWhere('id', $selectedPickupSlotId);
                    @endphp

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
                                        <p class="text-sm text-green-700 mt-1">
                                            {{ $selectedPoint['address'] }}
                                        </p>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pickupDate" class="block text-sm font-medium text-gray-700 mb-1">Date de retrait *</label>
                        <input
                            type="date"
                            id="pickupDate"
                            wire:model.live="pickupDate"
                            min="{{ now()->format('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('pickupDate') border-red-500 @enderror">
                        @error('pickupDate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="timeSlot" class="block text-sm font-medium text-gray-700 mb-1">Créneau horaire *</label>

                        {{-- Indicateur de chargement --}}
                        <div wire:loading wire:target="pickupDate" class="mb-2 text-xs text-blue-600 flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Mise à jour des créneaux disponibles...</span>
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
                                @if($selectedPickupSlotId && $pickupDate)
                                    Aucun créneau disponible pour cette date
                                @else
                                    Sélectionnez un point de retrait et une date
                                @endif
                            </div>
                        @endif
                        @error('selectedTimeSlot')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-between">
                <a href="{{ route('cart.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                    Retour au panier
                </a>
                <button
                    wire:click="submitOrder"
                    wire:loading.attr="disabled"
                    class="px-8 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Confirmer la commande</span>
                    <span wire:loading>Traitement en cours...</span>
                </button>
            </div>
        </div>

        <!-- Récapitulatif -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Récapitulatif</h2>

                <div class="space-y-3 mb-6">
                    @foreach($cart as $item)
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
                                {{ number_format(($item['price_cents'] * $item['quantity']) / 100, 2, ',', ' ') }} €
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

{{-- Chargement de Leaflet CSS et JS --}}
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
@endpush
