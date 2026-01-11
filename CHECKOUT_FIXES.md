# Corrections du système de checkout

## Problèmes identifiés et résolus

### 1. ✅ Carte qui disparaît lors de la sélection

**Problème** : Quand on cliquait sur un point dans la vue liste, la carte disparaissait complètement.

**Cause** : Ligne 279 de `checkout-form.blade.php`, l'appel à `selectMarker(point.id)` dans la vue liste n'était pas dans le bon contexte Alpine.

**Solution** :
```javascript
// Avant (problématique)
@click="selectedMarkerId = point.id; selectMarker(point.id)"

// Après (corrigé)
@click="selectedMarkerId = point.id"
```

**Améliorations ajoutées** :
- Indicateur visuel "✓ Point sélectionné" dans la liste
- Message "Point sélectionné" en bas de la liste quand un point est choisi
- Synchronisation du marqueur sur la carte quand on revient de la vue liste

### 2. ✅ Plusieurs horaires par jour non affichés

**Problème** : Si un point de retrait avait plusieurs créneaux horaires le même jour (ex: matin + après-midi), seul le premier était affiché.

**Cause** : Ligne 125 de `CheckoutForm.php`, utilisation de `firstWhere()` qui ne retourne que le premier résultat.

**Solution** :
```php
// Avant (ne prend que le premier)
$daySchedule = collect($pickupSlot->working_hours)->firstWhere('day', $dayOfWeek);

if ($daySchedule && (!isset($daySchedule['closed']) || !$daySchedule['closed'])) {
    $this->availableTimeSlots = [
        [
            'value' => $daySchedule['open'] . '-' . $daySchedule['close'],
            'label' => substr($daySchedule['open'], 0, 5) . ' - ' . substr($daySchedule['close'], 0, 5),
        ]
    ];
}

// Après (prend tous les créneaux)
$daySchedules = collect($pickupSlot->working_hours)->filter(function($schedule) use ($dayOfWeek) {
    return $schedule['day'] === $dayOfWeek && (!isset($schedule['closed']) || !$schedule['closed']);
});

foreach ($daySchedules as $schedule) {
    $this->availableTimeSlots[] = [
        'value' => $schedule['open'] . '-' . $schedule['close'],
        'label' => substr($schedule['open'], 0, 5) . ' - ' . substr($schedule['close'], 0, 5),
    ];
}
```

**Résultat** : Maintenant, tous les créneaux horaires du jour sélectionné sont affichés dans le select.

## Exemple de données supportées

### Un point avec plusieurs créneaux le même jour

```json
{
  "working_hours": [
    {
      "day": "tuesday",
      "open": "08:00:00",
      "close": "12:00:00",
      "closed": false
    },
    {
      "day": "tuesday",
      "open": "14:00:00",
      "close": "18:00:00",
      "closed": false
    }
  ]
}
```

**Affichage dans le select** :
- 08:00 - 12:00
- 14:00 - 18:00

## Fichiers modifiés

1. **app/Livewire/CheckoutForm.php** (ligne 107-136)
   - Méthode `updateAvailableTimeSlots()` refactorisée
   - Support de plusieurs créneaux par jour

2. **resources/views/livewire/checkout-form.blade.php** (ligne 273-306)
   - Vue liste : suppression de l'appel problématique à `selectMarker()`
   - Ajout d'indicateurs visuels de sélection
   - Synchronisation améliorée entre liste et carte

## Tests à effectuer

### Test 1 : Sélection depuis la liste
1. Cliquer sur "Afficher la liste"
2. Sélectionner un point dans la liste
3. ✅ Le point doit être surligné en vert
4. ✅ Message "✓ Point sélectionné" visible
5. ✅ Cliquer sur "Afficher la carte"
6. ✅ La carte réapparaît avec le marqueur vert sur le bon point

### Test 2 : Plusieurs horaires
1. Créer un point avec plusieurs créneaux le même jour :
   ```sql
   UPDATE pickup_slots
   SET working_hours = '[
     {"day": "monday", "open": "08:00:00", "close": "12:00:00"},
     {"day": "monday", "open": "14:00:00", "close": "18:00:00"}
   ]'
   WHERE id = 1;
   ```
2. Sélectionner ce point
3. Choisir lundi comme date
4. ✅ Le select doit montrer 2 options :
   - 08:00 - 12:00
   - 14:00 - 18:00

### Test 3 : Navigation liste ↔ carte
1. Sélectionner un point sur la carte
2. ✅ Marqueur devient vert
3. Cliquer sur "Afficher la liste"
4. ✅ Le point sélectionné est surligné dans la liste
5. Revenir à la carte
6. ✅ Le marqueur est toujours vert

## Comportement attendu

### Vue Carte
- Marqueurs bleus par défaut
- Marqueur vert quand sélectionné
- Popup avec bouton "Sélectionner ce point"
- Fermeture de la popup après sélection

### Vue Liste
- Points cliquables
- Bordure verte + fond vert clair pour le sélectionné
- Indicateur "✓ Point sélectionné"
- Message en bas "Point sélectionné" si un point choisi

### Synchronisation
- La sélection est synchronisée entre :
  - Vue carte
  - Vue liste
  - Bloc "Point sélectionné" sous la carte
  - Composant Livewire (pour les créneaux)

### Créneaux horaires
- Affichage de **tous** les créneaux du jour
- Mise à jour automatique quand on change de date
- Spinner de chargement pendant la mise à jour
- Message approprié si aucun créneau disponible

## Améliorations futures possibles

1. **Tri des créneaux** : Trier par heure de début
2. **Indication de disponibilité** : Afficher le nombre de places disponibles par créneau
3. **Créneaux complets** : Griser les créneaux déjà pleins
4. **Recherche dans la liste** : Ajouter un champ de recherche pour filtrer les points
5. **Géolocalisation** : Trier la liste par distance depuis la position de l'utilisateur

## Notes techniques

### Performance
- `filter()` au lieu de `firstWhere()` : légère augmentation de complexité mais négligeable (max 10-20 horaires)
- Pas de N+1 query : `PickupSlot::find()` est appelé une seule fois

### Compatibilité
- Compatible avec tous les formats de `working_hours` existants
- Gère correctement les jours fermés (`closed: true`)
- Supporte les points sans horaires définis

### Sécurité
- Validation Laravel maintenue
- Pas de changement dans les règles de validation
- `exists:pickup_slots,id` assure que le point existe

## Conclusion

Les deux problèmes sont maintenant résolus :
- ✅ La carte ne disparaît plus lors de la sélection
- ✅ Tous les créneaux horaires d'un jour sont affichés

Le système est plus robuste et offre une meilleure expérience utilisateur.
