# Fix critique : wire:ignore pour Leaflet

## Problème identifié

### Symptôme
La carte Leaflet disparaissait lors de la sélection d'un point de retrait.

### Cause racine
Lorsque Livewire met à jour le composant (via `$wire.selectPickupPoint()`), il redessine le DOM. Comme la carte Leaflet est créée **en JavaScript** et injectée dans le DOM, elle n'existe pas dans le HTML renvoyé par le serveur. Livewire la détruit donc sans la recréer.

### Schéma du problème

```
┌─────────────────────────────────────────────────────┐
│ 1. Utilisateur sélectionne un point                │
│    → Alpine appelle this.$wire.selectPickupPoint()  │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 2. Livewire met à jour le composant côté serveur   │
│    → Génère du nouveau HTML                         │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 3. Livewire remplace le DOM avec le nouveau HTML   │
│    → La carte Leaflet (JavaScript) est DÉTRUITE ❌  │
└─────────────────────────────────────────────────────┘
```

## Solution : `wire:ignore`

### Directive Livewire

`wire:ignore` indique à Livewire de **ne jamais toucher** au contenu de cet élément lors des mises à jour.

### Implémentation

**Fichier** : `resources/views/livewire/checkout-form.blade.php`

**Avant** :
```html
<div x-ref="mapContainer" style="height: 400px;"></div>
```

**Après** :
```html
<div wire:ignore>
    <div x-ref="mapContainer" style="height: 400px;"></div>
</div>
```

### Emplacement exact
Ligne 252-258 de `checkout-form.blade.php`

## Autres corrections appliquées

### 1. Syntaxe `$wire` corrigée

**Avant** :
```javascript
$wire.call('selectPickupPoint', value);
```

**Après** :
```javascript
this.$wire.selectPickupPoint(value);
```

**Raison** :
- `$wire.call()` est obsolète
- `this.$wire.method()` est la syntaxe moderne et plus directe

**Emplacement** : Ligne 109

### 2. Conservation de `@entangle`

La directive `@entangle` est correctement utilisée :
```javascript
selectedMarkerId: $wire.entangle('selectedPickupSlotId').live
```

Cela synchronise automatiquement `selectedMarkerId` (Alpine) avec `selectedPickupSlotId` (Livewire).

## Comment `wire:ignore` fonctionne

### Comportement normal de Livewire

```html
<div id="container">
    <p>{{ $text }}</p>
</div>
```

Quand `$text` change :
1. Livewire génère `<div id="container"><p>Nouveau texte</p></div>`
2. Morphdom compare et met à jour le DOM
3. ✅ Fonctionne car tout est dans le HTML

### Avec JavaScript (Leaflet)

```html
<div id="map"></div>
<script>
    L.map('map').setView([...]);
</script>
```

Quand Livewire met à jour :
1. Livewire voit `<div id="map"></div>` (vide dans le HTML)
2. Morphdom détruit le contenu (la carte JavaScript)
3. ❌ La carte disparaît

### Avec `wire:ignore`

```html
<div wire:ignore>
    <div id="map"></div>
</div>
<script>
    L.map('map').setView([...]);
</script>
```

Quand Livewire met à jour :
1. Livewire voit `wire:ignore`
2. Morphdom **ignore complètement** ce bloc
3. ✅ La carte reste intacte

## Cas d'usage de `wire:ignore`

### Utilisez `wire:ignore` pour :

1. **Cartes interactives** (Leaflet, Google Maps, Mapbox)
2. **Éditeurs WYSIWYG** (TinyMCE, Quill, CodeMirror)
3. **Graphiques** (Chart.js, D3.js, Plotly)
4. **Players média** (Video.js, Plyr)
5. **Composants tiers** qui manipulent le DOM en JavaScript

### Ne l'utilisez PAS pour :

- Éléments gérés par Livewire ou Alpine uniquement
- Formulaires simples
- Listes dynamiques (utilisez `wire:key` à la place)

## Vérification

### Avant le fix

```
1. Sélectionner un point
2. ❌ La carte disparaît
3. ❌ Marqueurs perdus
4. ❌ Impossible de re-sélectionner
```

### Après le fix

```
1. Sélectionner un point
2. ✅ La carte reste visible
3. ✅ Marqueur devient vert
4. ✅ Peut sélectionner d'autres points
5. ✅ Bascule liste ↔ carte fonctionne
```

## Code complet de la section carte

```blade
{{-- Carte --}}
<div x-show="!showListView">
    <!-- wire:ignore protège la carte des mises à jour Livewire -->
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
            class="text-sm text-blue-600 hover:text-blue-800 underline"
        >
            Afficher la liste
        </button>
    </div>
</div>
```

## Performance et optimisation

### Impact sur les performances

`wire:ignore` améliore les performances car :
- Morphdom n'analyse pas ce bloc
- Pas de comparaison DOM
- Pas de mise à jour inutile

### Attention

Si vous utilisez `wire:ignore`, vous devez gérer manuellement les mises à jour de cet élément :
- Utilisez Alpine.js (`x-data`, `x-bind`, etc.)
- Ou écoutez les événements Livewire

```javascript
// Exemple : écouter un événement Livewire
Livewire.on('pointUpdated', (pointId) => {
    // Mettre à jour manuellement la carte
    map.panTo(newLatLng);
});
```

Dans notre cas, `@entangle` gère déjà la synchronisation automatiquement.

## Debugging

### Si la carte ne s'affiche toujours pas

1. **Vérifier la console** :
   ```javascript
   console.log('Map container:', this.$refs.mapContainer);
   console.log('Map instance:', this.map);
   ```

2. **Vérifier que Leaflet est chargé** :
   ```javascript
   console.log('Leaflet loaded:', typeof L !== 'undefined');
   ```

3. **Vérifier wire:ignore** :
   - Inspectez le DOM dans les DevTools
   - Cherchez l'attribut `wire:ignore` sur l'élément parent

4. **Vérifier que $refs existe** :
   ```javascript
   init() {
       this.$nextTick(() => {
           if (!this.$refs.mapContainer) {
               console.error('Map container not found!');
               return;
           }
           this.initMap();
       });
   }
   ```

## Résumé des changements

### Fichier : `checkout-form.blade.php`

| Ligne | Changement | Raison |
|-------|-----------|--------|
| 252-258 | Ajout de `wire:ignore` | Protège Leaflet des mises à jour Livewire |
| 109 | `$wire.call()` → `this.$wire.method()` | Syntaxe moderne Livewire 3 |
| 97 | `$wire.entangle().live` conservé | Synchronisation automatique ✅ |

## Références

- [Livewire Documentation - wire:ignore](https://livewire.laravel.com/docs/wire-ignore)
- [Livewire + JavaScript Libs](https://livewire.laravel.com/docs/javascript)
- [Morphdom (moteur de diff DOM)](https://github.com/patrick-steele-idem/morphdom)

## Conclusion

`wire:ignore` est **essentiel** pour intégrer des bibliothèques JavaScript tierces avec Livewire. Sans cette directive, tout contenu généré en JavaScript sera détruit lors des mises à jour Livewire.

Notre carte Leaflet fonctionne maintenant parfaitement grâce à :
1. ✅ `wire:ignore` protège la carte
2. ✅ `@entangle` synchronise la sélection
3. ✅ `this.$wire.method()` communique avec Livewire
4. ✅ Alpine gère l'interactivité locale

Le système de checkout est maintenant robuste et production-ready ! 🎉
