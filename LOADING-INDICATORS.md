# 🔄 Système Unifié d'Indicateurs de Chargement

Ce document explique comment utiliser le système d'indicateurs de chargement dans l'application.

## 📦 Composants Disponibles

### 1. **Loading Spinner** (`<x-loading-spinner />`)

Un spinner réutilisable pour tous les cas d'usage.

```blade
{{-- Tailles disponibles --}}
<x-loading-spinner size="small" />
<x-loading-spinner size="default" />
<x-loading-spinner size="large" />

{{-- Couleurs disponibles --}}
<x-loading-spinner color="white" />
<x-loading-spinner color="emerald" />
<x-loading-spinner color="blue" />
<x-loading-spinner color="gray" />
```

### 2. **Button Loading** (`<x-button-loading />`)

Bouton avec indicateur de chargement intégré (pour Livewire).

```blade
{{-- Bouton primaire --}}
<x-button-loading wire="submitForm" loadingText="Envoi en cours...">
    <svg>...</svg>
    <span>Soumettre</span>
</x-button-loading>

{{-- Variants disponibles --}}
<x-button-loading variant="primary">...</x-button-loading>
<x-button-loading variant="secondary">...</x-button-loading>
<x-button-loading variant="danger">...</x-button-loading>

{{-- Tailles disponibles --}}
<x-button-loading size="small">...</x-button-loading>
<x-button-loading size="default">...</x-button-loading>
<x-button-loading size="large">...</x-button-loading>
```

### 3. **Page Loading** (`<x-page-loading />`)

Indicateur global pour les pages Livewire (barre de progression + overlay).

```blade
<div>
    {{-- Ajouter en haut de votre composant Livewire --}}
    <x-page-loading />

    {{-- Votre contenu --}}
</div>
```

## 🚀 Fonctionnement Automatique

### Pour les formulaires classiques (non-Livewire)

**Aucune configuration nécessaire !** Le système détecte automatiquement tous les formulaires et :

1. Désactive le bouton submit lors de la soumission
2. Affiche un spinner avec "Chargement..."
3. Empêche les soumissions multiples
4. Restaure automatiquement le bouton après 10 secondes (sécurité)

**Exemple :**
```blade
{{-- Ce formulaire aura automatiquement des indicateurs de chargement --}}
<form action="{{ route('cart.add.product', $product) }}" method="POST">
    @csrf
    <input type="number" name="quantity" value="1">
    <button type="submit">Ajouter au panier</button>
</form>
```

### Pour les composants Livewire

Utilisez les directives `wire:loading` :

```blade
{{-- Bouton avec indicateur inline --}}
<button wire:click="submitOrder">
    <span wire:loading.remove>Valider la commande</span>
    <span wire:loading class="flex items-center gap-2">
        <x-loading-spinner />
        Traitement...
    </span>
</button>

{{-- Ou utilisez le composant button-loading --}}
<x-button-loading wire="submitOrder" loadingText="Validation...">
    Valider la commande
</x-button-loading>
```

## 🎨 Styles Personnalisés

Les spinners utilisent Tailwind CSS et sont entièrement personnalisables :

```blade
<x-loading-spinner class="w-8 h-8 text-red-500" />
```

## 📝 Exemples Concrets

### Formulaire d'ajout au panier
```blade
<form action="{{ route('cart.add.bundle', $bundle) }}" method="POST">
    @csrf
    <input type="number" name="quantity" value="1">
    {{-- Le bouton aura automatiquement un spinner --}}
    <button type="submit" class="bg-emerald-600 text-white px-6 py-3 rounded-lg">
        <svg>...</svg>
        <span>Ajouter au panier</span>
    </button>
</form>
```

### Action Livewire
```blade
<div>
    {{-- Indicateur global --}}
    <x-page-loading />

    {{-- Formulaire --}}
    <form wire:submit="save">
        <input wire:model="name" type="text">

        {{-- Bouton avec chargement --}}
        <x-button-loading wire="save" loadingText="Enregistrement...">
            Enregistrer
        </x-button-loading>
    </form>

    {{-- Indicateur sur une section spécifique --}}
    <div wire:loading wire:target="loadMore">
        <x-loading-spinner color="emerald" />
        Chargement de plus de résultats...
    </div>
</div>
```

## 🛠️ Fichiers du Système

- **Composants Blade :**
  - `resources/views/components/loading-spinner.blade.php`
  - `resources/views/components/button-loading.blade.php`
  - `resources/views/components/page-loading.blade.php`

- **JavaScript :**
  - `public/js/form-loading.js` - Gestion automatique des formulaires classiques

- **Intégration :**
  - `resources/views/layouts/app.blade.php` - Inclusion du script

## ✅ Checklist d'Utilisation

- [ ] Formulaires classiques : Rien à faire, c'est automatique !
- [ ] Pages Livewire : Ajouter `<x-page-loading />` en haut
- [ ] Boutons Livewire : Utiliser `<x-button-loading wire="method">`
- [ ] Sections spécifiques : Utiliser `wire:loading` avec `<x-loading-spinner />`

## 🎯 Avantages

1. **Cohérence** : Design unifié sur toute l'application
2. **Automatique** : Fonctionne sans configuration pour les formulaires classiques
3. **Accessibilité** : Attributs ARIA inclus
4. **Performance** : Optimisé avec `wire:loading.delay` pour éviter les flashs
5. **Réutilisable** : Composants Blade modulaires

---

**Note :** Le système gère automatiquement la restauration des boutons en cas de navigation arrière (back button) du navigateur.
