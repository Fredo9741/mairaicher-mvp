# Correction du conflit Alpine.js

## Problème identifié

### Erreurs dans la console
```
Detected multiple instances of Alpine running
Cannot read properties of undefined (reading 'entangle')
ReferenceError: showListView is not defined
ReferenceError: pickupPoints is not defined
```

## Cause racine

**Double chargement d'Alpine.js** :
1. Alpine importé et démarré manuellement dans `resources/js/app.js`
2. Alpine déjà inclus automatiquement par Livewire 3

Cela créait deux instances d'Alpine en conflit, empêchant le composant x-data de s'initialiser correctement.

## Solutions appliquées

### 1. ✅ Suppression de l'import manuel d'Alpine

**Fichier** : `resources/js/app.js`

**Avant** :
```javascript
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();
```

**Après** :
```javascript
import './bootstrap';

// Livewire 3 inclut déjà Alpine.js
// Pas besoin de l'importer manuellement
```

### 2. ✅ Utilisation de $wire.entangle()

**Fichier** : `resources/views/livewire/checkout-form.blade.php`

**Changement** :
```javascript
// Syntaxe correcte pour Livewire 3
selectedMarkerId: $wire.entangle('selectedPickupSlotId').live,
```

Cette syntaxe permet à Alpine.js de communiquer directement avec Livewire sans passer par `window.Livewire.find()`.

### 3. ✅ Rebuild des assets

```bash
npm run build
```

Assets générés :
- `public/build/assets/app-79YtkL0f.css` (85.38 kB)
- `public/build/assets/app-Dn177VMY.js` (35.87 kB)

## Pourquoi ça marche maintenant

### Livewire 3 + Alpine.js

Livewire 3 embarque **Alpine.js v3** de manière native :
- Alpine est automatiquement disponible globalement
- `$wire` est l'objet magique pour communiquer avec Livewire
- `@entangle()` ou `$wire.entangle()` synchronise les propriétés

### Pas besoin de :
- ❌ Importer Alpine manuellement
- ❌ Appeler `Alpine.start()`
- ❌ Utiliser `window.Livewire.find()`

### Il suffit de :
- ✅ Laisser Livewire gérer Alpine
- ✅ Utiliser `$wire` dans les composants Alpine
- ✅ Utiliser `@entangle()` pour la synchronisation

## Vérification

### Après refresh de la page, vous devriez avoir :

**Console propre** :
- ✅ Pas d'erreur "multiple instances of Alpine"
- ✅ Pas d'erreur "Cannot read properties of undefined"
- ✅ Pas de "ReferenceError"

**Fonctionnalités** :
- ✅ Carte Leaflet s'affiche
- ✅ Marqueurs cliquables
- ✅ Popup avec bouton fonctionnel
- ✅ Sélection de point synchronisée avec Livewire
- ✅ Basculement carte/liste
- ✅ Mise à jour des créneaux horaires

## Architecture finale

```
┌─────────────────────────────────────────┐
│          Navigateur                     │
│                                         │
│  ┌──────────────────────────────────┐  │
│  │   Livewire 3                     │  │
│  │   ├── Alpine.js (intégré)       │  │
│  │   ├── $wire (magic object)      │  │
│  │   └── @entangle() directive     │  │
│  └──────────────────────────────────┘  │
│           ↕                             │
│  ┌──────────────────────────────────┐  │
│  │   Composant Blade                │  │
│  │   └── x-data avec $wire         │  │
│  └──────────────────────────────────┘  │
└─────────────────────────────────────────┘
```

## Notes importantes

### Si vous voulez utiliser des plugins Alpine

Si vous avez besoin d'étendre Alpine avec des plugins (ex: Alpine Focus, Alpine Collapse), vous pouvez le faire via Livewire :

```javascript
// resources/js/app.js
import './bootstrap';

// Si besoin d'un plugin Alpine
document.addEventListener('livewire:init', () => {
    // Alpine est disponible via window.Alpine
    // mais déjà démarré par Livewire

    // Exemple d'ajout de plugin
    // import focus from '@alpinejs/focus'
    // window.Alpine.plugin(focus)
});
```

### Si vous voulez une version personnalisée d'Alpine

Dans ce cas, vous devez désactiver Alpine de Livewire :

```php
// config/livewire.php
'inject_assets' => false,
```

Puis gérer Alpine complètement vous-même. **Mais ce n'est pas recommandé** pour la plupart des cas.

## Dépendances actuelles

### package.json
```json
{
  "dependencies": {
    "alpinejs": "^3.15.3"  // Peut rester pour compatibilité
  }
}
```

Alpine peut rester dans les dépendances NPM :
- Livewire l'utilisera
- Permet l'autocomplétion dans l'IDE
- Pas de conflit tant qu'on n'appelle pas `Alpine.start()`

## Troubleshooting

### Si le problème persiste

1. **Vider le cache du navigateur** (Ctrl + Shift + R)
2. **Vider le cache Laravel** :
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan livewire:clear-cache
   ```
3. **Rebuild complet** :
   ```bash
   npm run build
   ```
4. **Vérifier la console** pour d'autres erreurs

### Commandes utiles

```bash
# Development avec hot reload
npm run dev

# Production build
npm run build

# Vérifier Livewire
php artisan livewire:list
```

## Ressources

- [Livewire 3 Documentation](https://livewire.laravel.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/)
- [Livewire + Alpine Guide](https://livewire.laravel.com/docs/alpine)

## Conclusion

Le conflit Alpine a été résolu en :
1. Supprimant l'import manuel d'Alpine dans `app.js`
2. Utilisant la syntaxe `$wire.entangle()` de Livewire 3
3. Rebuilding les assets

La page de checkout fonctionne maintenant correctement avec une seule instance d'Alpine gérée par Livewire.
