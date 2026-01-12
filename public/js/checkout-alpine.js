/**
 * Alpine.js Data Functions pour le Checkout
 * Ce fichier contient toute la logique Alpine pour la page de checkout
 */

/**
 * Composant principal du Checkout
 * Gère la navigation entre les étapes et la validation
 */
function checkoutSteps() {
    return {
        currentStep: 1,
        stepCompleted: {
            1: false,
            2: false,
            3: false
        },

        init() {
            // Écoute les changements de propriétés Livewire pour mettre à jour l'état des étapes
            this.checkStepCompletion();
        },

        /**
         * Valide l'étape 1 (informations client)
         */
        validateStep1() {
            // Vérifie simplement que les champs sont remplis côté client
            const name = this.$wire.get('customer_name');
            const email = this.$wire.get('customer_email');
            const phone = this.$wire.get('customer_phone');

            // Validation basique côté client
            if (name && name.trim() && email && email.trim() && phone && phone.trim()) {
                this.stepCompleted[1] = true;
                this.currentStep = 2;
                // Pas de scroll - l'utilisateur reste au même niveau
            } else {
                // Affiche un message si les champs ne sont pas remplis
                alert('Veuillez remplir tous les champs obligatoires (Nom, Email, Téléphone)');
            }
        },

        /**
         * Valide l'étape 2 (point de retrait)
         */
        validateStep2() {
            // Vérifie que tous les champs requis sont remplis
            const pickupSlotId = this.$wire.get('selectedPickupSlotId');
            const pickupDate = this.$wire.get('pickupDate');
            const timeSlot = this.$wire.get('selectedTimeSlot');

            if (pickupSlotId && pickupDate && timeSlot) {
                this.stepCompleted[2] = true;
                this.currentStep = 3;
                // Pas de scroll - l'utilisateur reste au même niveau
            } else {
                // Affiche un message si les champs ne sont pas remplis
                alert('Veuillez sélectionner un point de retrait, une date et un créneau horaire');
            }
        },

        /**
         * Vérifie si les étapes sont complétées
         */
        checkStepCompletion() {
            // Étape 1
            const name = this.$wire.get('customer_name');
            const email = this.$wire.get('customer_email');
            const phone = this.$wire.get('customer_phone');
            this.stepCompleted[1] = !!(name && email && phone);

            // Étape 2
            const pickupSlotId = this.$wire.get('selectedPickupSlotId');
            const pickupDate = this.$wire.get('pickupDate');
            const timeSlot = this.$wire.get('selectedTimeSlot');
            this.stepCompleted[2] = !!(pickupSlotId && pickupDate && timeSlot);
        }
    };
}

/**
 * Composant pour la carte Leaflet
 * Gère l'affichage des points de retrait sur la carte
 */
function checkoutMap(pickupPointsData) {
    return {
        map: null,
        markers: {},
        selectedMarkerId: null,
        pickupPoints: pickupPointsData,
        showListView: true, // Liste affichée par défaut

        init() {
            // Entangle avec Livewire
            this.selectedMarkerId = this.$wire.entangle('selectedPickupSlotId').live;

            // Watch pour appeler la méthode Livewire quand on change de point
            this.$watch('selectedMarkerId', (value) => {
                if (value) {
                    this.$wire.call('selectPickupPoint', value);
                }
            });
        },

        /**
         * Nettoie la carte lors de la destruction du composant
         */
        destroy() {
            if (this.map) {
                this.map.remove();
                this.map = null;
            }
        },

        /**
         * Formate les horaires de manière lisible
         */
        formatHours(hoursString) {
            if (!hoursString || hoursString === 'Horaires non définis') {
                return 'Horaires non définis';
            }
            return hoursString;
        },

        /**
         * Initialise la carte Leaflet
         */
        initMap() {
            // Initialise la carte centrée sur Saint-Leu
            this.map = L.map(this.$refs.mapContainer).setView([-21.1705, 55.2886], 13);

            // Ajoute la couche de tuiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19
            }).addTo(this.map);

            // Ajoute tous les points de retrait comme marqueurs
            this.addMarkers();
        },

        /**
         * Ajoute les marqueurs pour chaque point de retrait
         */
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
                    .bindPopup(popupContainer, { maxWidth: 300 });

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

        /**
         * Met en surbrillance le marqueur sélectionné
         */
        selectMarker(pointId) {
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
    };
}

/**
 * Composant pour le date picker Flatpickr
 */
function checkoutDatePicker(availableDaysWire, pickupDateWire) {
    return {
        flatpickrInstance: null,
        availableDays: availableDaysWire,
        pickupDate: pickupDateWire,

        init() {
            // On attend que Livewire soit totalement prêt
            this.$nextTick(() => {
                this.initFlatpickr();
            });

            // On surveille les changements de jours disponibles
            this.$watch('availableDays', () => {
                this.initFlatpickr();
            });
        },

        /**
         * Initialise Flatpickr
         */
        initFlatpickr() {
            // Vérification ultra-sécurisée de l'instance
            if (this.flatpickrInstance && typeof this.flatpickrInstance.destroy === 'function') {
                this.flatpickrInstance.destroy();
            }

            // Sécurité : on s'assure que availableDays est un tableau utilisable
            const days = Array.isArray(this.availableDays) ? this.availableDays : [];

            // On stocke la NOUVELLE instance
            this.flatpickrInstance = flatpickr(this.$refs.dateInput, {
                locale: typeof flatpickrFrench !== 'undefined' ? flatpickrFrench : 'fr',
                dateFormat: 'Y-m-d',
                minDate: 'today',
                defaultDate: this.pickupDate,
                enable: days.length > 0 ? [
                    (date) => days.includes(date.getDay())
                ] : undefined,
                onChange: (_selectedDates, dateStr) => {
                    this.pickupDate = dateStr;
                }
            });
        }
    };
}

// Rendre les fonctions disponibles globalement pour Alpine.js
window.checkoutSteps = checkoutSteps;
window.checkoutMap = checkoutMap;
window.checkoutDatePicker = checkoutDatePicker;
