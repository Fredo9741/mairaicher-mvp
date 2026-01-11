<div class="space-y-6">
    {{-- En-tête --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Choisissez votre point de retrait
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Sélectionnez un point sur la carte et choisissez votre créneau horaire
        </p>
    </div>

    {{-- Carte interactive --}}
    <div
        x-data="{
            map: null,
            markers: {},
            selectedMarkerId: @entangle('selectedPickupSlotId'),
            pickupPoints: @js($pickupPoints),

            init() {
                this.$nextTick(() => {
                    this.initMap();
                });
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

                    // Crée le marqueur
                    const marker = L.marker([point.lat, point.lng], { icon })
                        .addTo(this.map)
                        .bindPopup(`
                            <div class=\"p-2\">
                                <h4 class=\"font-bold text-gray-900\">${point.name}</h4>
                                ${point.address ? `<p class=\"text-sm text-gray-600 mt-1\">${point.address}</p>` : ''}
                                <div class=\"mt-2 text-xs text-gray-700\">
                                    <strong>Horaires :</strong><br>
                                    ${point.working_hours}
                                </div>
                                <button
                                    onclick=\"window.Livewire.find('${this.$wire.__instance.id}').call('selectPickupPoint', ${point.id})\"
                                    class=\"mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition\"
                                >
                                    Sélectionner ce point
                                </button>
                            </div>
                        `, {
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
            },

            // Écoute les changements de sélection depuis Livewire
            $watch: {
                selectedMarkerId(value) {
                    if (value) {
                        this.selectMarker(value);
                    }
                }
            }
        }"
        class="w-full"
    >
        <div
            x-ref="mapContainer"
            class="w-full rounded-lg border-2 border-gray-300 dark:border-gray-600"
            style="height: 500px;"
        ></div>
    </div>

    {{-- Informations du point sélectionné --}}
    @if($selectedPickupSlotId)
        @php
            $selectedPoint = $pickupPoints->firstWhere('id', $selectedPickupSlotId);
        @endphp

        @if($selectedPoint)
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-semibold text-green-900 dark:text-green-100">
                            Point sélectionné : {{ $selectedPoint['name'] }}
                        </h4>
                        @if($selectedPoint['address'])
                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                {{ $selectedPoint['address'] }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Sélection de la date et de l'heure --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Date de retrait --}}
        <div>
            <label for="pickupDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Date de retrait
            </label>
            <input
                type="date"
                id="pickupDate"
                wire:model.live="pickupDate"
                min="{{ now()->format('Y-m-d') }}"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            @error('pickupDate')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Créneau horaire --}}
        <div>
            <label for="timeSlot" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Créneau horaire
            </label>
            @if(count($availableTimeSlots) > 0)
                <select
                    id="timeSlot"
                    wire:model="selectedTimeSlot"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Sélectionnez un horaire</option>
                    @foreach($availableTimeSlots as $slot)
                        <option value="{{ $slot['value'] }}">{{ $slot['label'] }}</option>
                    @endforeach
                </select>
            @else
                <div class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    @if($selectedPickupSlotId && $pickupDate)
                        Aucun créneau disponible pour cette date
                    @else
                        Sélectionnez un point de retrait et une date
                    @endif
                </div>
            @endif
            @error('selectedTimeSlot')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Messages d'erreur généraux --}}
    @error('selectedPickupSlotId')
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        </div>
    @enderror
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
