{{-- Composant de sélection de carte Leaflet pour Filament --}}
<div
    x-data="{
        map: null,
        marker: null,
        // Coordonnées par défaut : Saint-Leu, La Réunion
        defaultLat: -21.1705,
        defaultLng: 55.2886,
        defaultZoom: 13,

        init() {
            this.$nextTick(() => {
                this.initMap();
            });
        },

        initMap() {
            // Initialise la carte Leaflet centrée sur Saint-Leu
            this.map = L.map(this.$refs.mapContainer).setView([this.defaultLat, this.defaultLng], this.defaultZoom);

            // Ajoute la couche de tuiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(this.map);

            // Récupère les coordonnées existantes depuis les champs lat/lng
            const currentLat = parseFloat(document.querySelector('input[id*=\'lat\']')?.value);
            const currentLng = parseFloat(document.querySelector('input[id*=\'lng\']')?.value);

            // Si des coordonnées existent déjà, centre la carte et ajoute un marqueur
            if (!isNaN(currentLat) && !isNaN(currentLng)) {
                this.map.setView([currentLat, currentLng], 15);
                this.addMarker(currentLat, currentLng);
            }

            // Événement de clic sur la carte : ajoute/déplace le marqueur
            this.map.on('click', (e) => {
                const { lat, lng } = e.latlng;
                this.addMarker(lat, lng);
                this.updateCoordinates(lat, lng);
            });
        },

        addMarker(lat, lng) {
            // Supprime le marqueur existant s'il y en a un
            if (this.marker) {
                this.map.removeLayer(this.marker);
            }

            // Ajoute un nouveau marqueur
            this.marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(this.map);

            // Permet de déplacer le marqueur et met à jour les coordonnées
            this.marker.on('dragend', (e) => {
                const position = e.target.getLatLng();
                this.updateCoordinates(position.lat, position.lng);
            });

            // Popup avec les coordonnées
            this.marker.bindPopup(`
                <strong>Point de retrait</strong><br>
                Lat: ${lat.toFixed(6)}<br>
                Lng: ${lng.toFixed(6)}
            `).openPopup();
        },

        updateCoordinates(lat, lng) {
            // Met à jour les champs de formulaire Filament avec les nouvelles coordonnées
            const latInput = document.querySelector('input[id*=\'lat\']');
            const lngInput = document.querySelector('input[id*=\'lng\']');

            if (latInput) {
                latInput.value = lat.toFixed(8);
                latInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            if (lngInput) {
                lngInput.value = lng.toFixed(8);
                lngInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    }"
    class="w-full"
>
    {{-- Conteneur de la carte --}}
    <div
        x-ref="mapContainer"
        class="w-full rounded-lg border border-gray-300 dark:border-gray-600"
        style="height: 400px; z-index: 0;"
    ></div>

    {{-- Aide pour l'utilisateur --}}
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
        Cliquez sur la carte pour placer le marqueur du point de retrait. Vous pouvez ensuite le déplacer en le faisant glisser.
    </p>
</div>

{{-- Chargement de Leaflet CSS et JS depuis CDN --}}
@once
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
@endonce
