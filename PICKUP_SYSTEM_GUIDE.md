# 📍 Guide du Système de Points de Retrait

## Vue d'ensemble

Ce système permet aux clients de sélectionner un point de retrait géographique (parking de covoiturage) sur une carte interactive, ainsi qu'un créneau horaire pour récupérer leur commande.

---

## 🎯 Fonctionnalités implémentées

### 1. Administration Filament

#### Gestion des Points de Retrait
- **Localisation** : Interface avec carte Leaflet interactive
  - Clic sur la carte pour placer un marqueur
  - Drag & drop du marqueur pour ajuster la position
  - Coordonnées GPS (lat/lng) synchronisées automatiquement
  - Carte centrée sur Saint-Leu par défaut

- **Informations** :
  - Nom du point (ex: "Parking Covoiturage Saint-Leu Centre")
  - Adresse complète
  - Statut actif/inactif

- **Horaires d'ouverture** :
  - Repeater pour définir les horaires par jour
  - Sélection du jour (Lundi-Dimanche)
  - Heure d'ouverture et de fermeture
  - Option "Fermé ce jour"
  - Labels dynamiques dans le repeater

#### Vue en liste
- Affichage du nom et de l'adresse
- Colonnes lat/lng (masquées par défaut)
- Résumé des jours d'ouverture
- Compteur de commandes associées
- Toggle actif/inactif direct

---

### 2. Interface Client (Livewire)

#### Composant `PickupPointSelector`

**Localisation** : `app/Livewire/PickupPointSelector.php`

**Propriétés** :
- `$selectedPickupSlotId` : ID du point sélectionné
- `$pickupDate` : Date de retrait
- `$selectedTimeSlot` : Créneau horaire choisi
- `$availableTimeSlots` : Créneaux disponibles pour la date/point

**Méthodes principales** :
```php
selectPickupPoint($pickupSlotId)    // Sélection d'un point
updatedPickupDate()                  // Changement de date
updateAvailableTimeSlots()           // Calcul des créneaux disponibles
getPickupPointsProperty()            // Récupère tous les points actifs
```

#### Vue Blade

**Localisation** : `resources/views/livewire/pickup-point-selector.blade.php`

**Composants** :
1. **Carte interactive** :
   - Marqueurs bleus pour les points disponibles
   - Marqueur vert pour le point sélectionné
   - Popup avec nom, adresse, horaires
   - Bouton "Sélectionner ce point" dans la popup

2. **Panneau de sélection** :
   - Confirmation visuelle du point sélectionné (badge vert)
   - Sélecteur de date (min: aujourd'hui)
   - Sélecteur d'horaire (dynamique selon point + date)

3. **Validation** :
   - Messages d'erreur pour champs obligatoires
   - Vérification de la disponibilité des horaires

---

## 📂 Structure de la base de données

### Table `pickup_slots`

```sql
id                  BIGINT UNSIGNED
name                VARCHAR(255)        -- Nom du point
lat                 DECIMAL(10,8)       -- Latitude GPS
lng                 DECIMAL(11,8)       -- Longitude GPS
address             VARCHAR(255)        -- Adresse complète
working_hours       JSON                -- Horaires par jour
is_active           BOOLEAN             -- Statut
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Format JSON `working_hours`

```json
[
  {
    "day": "monday",
    "open": "09:00:00",
    "close": "18:00:00",
    "closed": false
  },
  {
    "day": "tuesday",
    "open": "09:00:00",
    "close": "18:00:00",
    "closed": false
  },
  {
    "day": "sunday",
    "closed": true
  }
]
```

---

## 🚀 Utilisation

### 1. Administration (Filament)

#### Créer un nouveau point de retrait :

1. Accédez à **Créneaux de retrait** dans le menu Filament
2. Cliquez sur **Nouveau**
3. Remplissez les informations :
   - Nom du point
   - Adresse (optionnel mais recommandé)
4. **Localisation GPS** :
   - Cliquez sur la carte pour placer le marqueur
   - Ou saisissez manuellement lat/lng
   - Le marqueur peut être déplacé en le faisant glisser
5. **Horaires d'ouverture** :
   - Cliquez sur "Ajouter un horaire"
   - Sélectionnez le jour
   - Définissez l'heure d'ouverture et de fermeture
   - Ou cochez "Fermé ce jour"
   - Répétez pour chaque jour d'ouverture
6. Cochez "Point actif"
7. Enregistrez

#### Modifier un point existant :

1. Cliquez sur l'icône "Modifier" dans la liste
2. La carte affichera le marqueur actuel
3. Modifiez les informations nécessaires
4. Enregistrez

---

### 2. Intégration Frontend

#### Afficher le sélecteur dans une vue :

```blade
<div>
    <h2>Choisissez votre mode de retrait</h2>

    @livewire('pickup-point-selector')

    <button wire:click="proceedToCheckout">
        Continuer vers le paiement
    </button>
</div>
```

#### Récupérer les données sélectionnées :

Dans votre composant parent :

```php
public function proceedToCheckout()
{
    $pickupData = [
        'pickup_slot_id' => $this->selectedPickupSlotId,
        'pickup_date' => $this->pickupDate,
        'pickup_time' => $this->selectedTimeSlot,
    ];

    // Validation
    $this->validate([
        'selectedPickupSlotId' => 'required',
        'pickupDate' => 'required|date',
        'selectedTimeSlot' => 'required',
    ]);

    // Créer la commande
    Order::create([
        'pickup_slot_id' => $this->selectedPickupSlotId,
        'pickup_date' => $this->pickupDate,
        // ... autres champs
    ]);
}
```

---

## 🔧 Configuration

### Coordonnées par défaut (Saint-Leu)

**Admin Filament** : [resources/views/filament/forms/components/map-picker.blade.php](resources/views/filament/forms/components/map-picker.blade.php:11-13)
```javascript
defaultLat: -21.1705,
defaultLng: 55.2886,
defaultZoom: 13,
```

**Frontend Client** : [resources/views/livewire/pickup-point-selector.blade.php](resources/views/livewire/pickup-point-selector.blade.php:28)
```javascript
this.map = L.map(this.$refs.mapContainer).setView([-21.1705, 55.2886], 13);
```

### Personnaliser les marqueurs

Couleurs disponibles (CDN Leaflet Color Markers) :
- `marker-icon-2x-blue.png` (par défaut)
- `marker-icon-2x-green.png` (sélectionné)
- `marker-icon-2x-red.png`
- `marker-icon-2x-orange.png`
- `marker-icon-2x-yellow.png`
- etc.

---

## 🧪 Tests

### Seeder de données de test

**Commande** :
```bash
php artisan db:seed --class=PickupSlotSeeder
```

**Données créées** :
- 4 points de retrait autour de Saint-Leu
- Horaires variés (matin, après-midi, samedi)
- Coordonnées GPS réelles de parkings de covoiturage

### Vérifier les données :

1. Accédez à Filament → Créneaux de retrait
2. Vérifiez que les 4 points apparaissent sur la carte
3. Testez l'édition d'un point
4. Vérifiez que les horaires s'affichent correctement

---

## 🎨 Personnalisation

### Modifier la hauteur de la carte

**Admin** : [resources/views/filament/forms/components/map-picker.blade.php](resources/views/filament/forms/components/map-picker.blade.php:99)
```html
style="height: 400px;"
```

**Frontend** : [resources/views/livewire/pickup-point-selector.blade.php](resources/views/livewire/pickup-point-selector.blade.php:124)
```html
style="height: 500px;"
```

### Changer les tuiles de la carte

Remplacez l'URL OpenStreetMap par une autre :
```javascript
// Exemple : Mapbox (nécessite une clé API)
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: '© Mapbox',
    id: 'mapbox/streets-v11',
    accessToken: 'VOTRE_CLE_API'
}).addTo(this.map);
```

---

## 📝 Fichiers modifiés/créés

### Migrations
- [database/migrations/2026_01_11_061641_add_location_fields_to_pickup_slots_table.php](database/migrations/2026_01_11_061641_add_location_fields_to_pickup_slots_table.php)

### Modèles
- [app/Models/PickupSlot.php](app/Models/PickupSlot.php) (modifié)

### Resources Filament
- [app/Filament/Resources/PickupSlotResource.php](app/Filament/Resources/PickupSlotResource.php) (modifié)

### Vues Filament
- [resources/views/filament/forms/components/map-picker.blade.php](resources/views/filament/forms/components/map-picker.blade.php) (créé)

### Composants Livewire
- [app/Livewire/PickupPointSelector.php](app/Livewire/PickupPointSelector.php) (créé)
- [resources/views/livewire/pickup-point-selector.blade.php](resources/views/livewire/pickup-point-selector.blade.php) (créé)

### Seeders
- [database/seeders/PickupSlotSeeder.php](database/seeders/PickupSlotSeeder.php) (modifié)

---

## 🐛 Dépannage

### La carte ne s'affiche pas

1. Vérifiez que Leaflet CSS/JS sont chargés :
```html
<!-- Dans le <head> -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<!-- Avant </body> -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

2. Vérifiez la console JavaScript (F12) pour les erreurs

3. Vérifiez que Alpine.js est chargé (requis pour Filament et Livewire)

### Les coordonnées ne se mettent pas à jour

1. Vérifiez les IDs des champs dans le formulaire Filament
2. Le script cherche les inputs avec `id*='lat'` et `id*='lng'`
3. Si les IDs Filament changent, ajustez le sélecteur dans `map-picker.blade.php`

### Les horaires ne s'affichent pas

1. Vérifiez le format JSON dans la base de données
2. Le cast `'working_hours' => 'array'` doit être dans le modèle
3. Vérifiez que les jours sont en anglais (`monday`, `tuesday`, etc.)

---

## 🚀 Prochaines améliorations possibles

- [ ] Géolocalisation automatique du client
- [ ] Calcul de distance depuis la position du client
- [ ] Filtrage des points par distance maximale
- [ ] Notifications aux clients quand commande prête
- [ ] QR Code pour valider le retrait
- [ ] Historique des retraits par point
- [ ] Capacité maximale par créneau horaire
- [ ] Intégration avec un système de routage (Google Maps)

---

## 📞 Support

Pour toute question ou problème :
1. Vérifiez les logs Laravel : `storage/logs/laravel.log`
2. Activez le mode debug : `.env` → `APP_DEBUG=true`
3. Consultez la documentation Leaflet : https://leafletjs.com
4. Consultez la documentation Livewire : https://livewire.laravel.com

---

**Dernière mise à jour** : 2026-01-11
**Version** : 1.0.0
