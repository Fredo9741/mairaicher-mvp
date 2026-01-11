# 🎯 Livewire 3 + Alpine.js - Bonnes Pratiques

## Vue d'ensemble

Ce document compile les bonnes pratiques pour intégrer **Livewire 3** et **Alpine.js** sans erreurs ni conflits, basé sur les corrections appliquées dans le projet.

---

## ⚠️ Problèmes courants (et comment les éviter)

### 1. Erreur "Livewire property cannot be found"

**❌ Mauvaise pratique** :
```javascript
// Utiliser une propriété computed sans la déclarer publiquement
availableDays: @entangle('availableDays').live
```

```php
// Dans le composant Livewire
public function getAvailableDaysProperty() {
    return [...];
}
```

**✅ Bonne pratique** :
```javascript
// Déclarer comme propriété publique
availableDays: @entangle('availableDays').live
```

```php
// Dans le composant Livewire
public $availableDays = [];

public function updateAvailableDays() {
    $this->availableDays = [...];
}
```

**Pourquoi ?**
- `@entangle()` nécessite une **propriété publique**, pas une computed property
- Les getters `getXProperty()` ne sont pas accessibles via `@entangle`

---

### 2. Erreur "Cannot read properties of undefined"

**❌ Mauvaise pratique** :
```javascript
init() {
    this.initFlatpickr();  // Livewire peut ne pas être prêt
}

initFlatpickr() {
    const days = this.availableDays;  // undefined si Livewire pas encore chargé
    console.log(days.length);  // 💥 ERREUR !
}
```

**✅ Bonne pratique** :
```javascript
init() {
    this.$nextTick(() => {
        this.initFlatpickr();  // Attend que Livewire soit prêt
    });
}

initFlatpickr() {
    // Protection : vérifier que c'est bien un tableau
    const days = Array.isArray(this.availableDays) ? this.availableDays : [];
    console.log(days.length);  // ✅ Pas d'erreur
}
```

**Pourquoi ?**
- `$nextTick()` attend le prochain cycle de rendu
- `Array.isArray()` protège contre les valeurs `null` ou `undefined`
- **Défensive programming** : toujours vérifier avant d'utiliser

---

### 3. Erreur "Multiple instances of Alpine running"

**❌ Mauvaise pratique** :
```javascript
// resources/js/app.js
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();  // 💥 Conflit avec Livewire !
```

**✅ Bonne pratique** :
```javascript
// resources/js/app.js
import './bootstrap';

// Livewire 3 inclut déjà Alpine.js
// Pas besoin de l'importer manuellement
```

**Pourquoi ?**
- Livewire 3 embarque **Alpine.js nativement**
- Importer Alpine manuellement crée **2 instances**
- Résultat : conflits, erreurs, comportements imprévisibles

---

### 4. Utilisation inconsistante de `$wire`

**❌ Mauvaise pratique (mélange de syntaxes)** :
```javascript
// Mélange dangereux !
availableDays: @entangle('availableDays').live,
defaultDate: '{{ $pickupDate }}',  // ⚠️ Valeur fixe au render

onChange: (selectedDates, dateStr) => {
    @this.set('pickupDate', dateStr);  // ⚠️ Ancienne syntaxe
}
```

**✅ Bonne pratique (cohérence)** :
```javascript
// Utiliser $wire partout
availableDays: @entangle('availableDays').live,
defaultDate: this.$wire.pickupDate,  // ✅ Valeur dynamique

onChange: (selectedDates, dateStr) => {
    this.$wire.set('pickupDate', dateStr);  // ✅ Syntaxe moderne
}
```

**Pourquoi ?**
- `$wire` est l'API moderne de Livewire 3
- `@this` est l'ancienne syntaxe (Livewire 2)
- `{{ $variable }}` est rendu **côté serveur**, pas réactif

---

### 5. Erreur avec `window.Livewire.find()`

**❌ Mauvaise pratique** :
```javascript
onChange: (selectedDates, dateStr) => {
    // ID statique qui change à chaque render !
    window.Livewire.find('tLySh5joMP38JsEyUplp').set('pickupDate', dateStr);
}
```

**✅ Bonne pratique** :
```javascript
onChange: (selectedDates, dateStr) => {
    // Utilise le contexte Alpine automatique
    this.$wire.set('pickupDate', dateStr);
}
```

**Pourquoi ?**
- L'ID du composant **change dynamiquement**
- `window.Livewire.find()` est fragile et déprécié
- `$wire` fonctionne **toujours** car il est contextuel

---

## ✅ Checklist des bonnes pratiques

### Côté Livewire (PHP)

- [ ] Déclarer les propriétés utilisées dans Alpine comme `public`
- [ ] Éviter les computed properties pour `@entangle`
- [ ] Mettre à jour les propriétés via des méthodes explicites
- [ ] Valider les données avec `$rules` et `$messages`

**Exemple** :
```php
class CheckoutForm extends Component
{
    // ✅ Propriétés publiques pour Alpine
    public $availableDays = [];
    public $pickupDate;
    public $selectedPickupSlotId;

    // ✅ Méthode explicite de mise à jour
    public function updateAvailableDays()
    {
        $this->availableDays = [...];
    }

    // ✅ Appelée quand on sélectionne un point
    public function selectPickupPoint($id)
    {
        $this->selectedPickupSlotId = $id;
        $this->updateAvailableDays();  // Met à jour
    }
}
```

---

### Côté Alpine (JavaScript)

- [ ] Utiliser `$nextTick()` dans `init()` pour attendre Livewire
- [ ] Protéger avec `Array.isArray()`, `typeof`, etc.
- [ ] Utiliser `$wire` au lieu de `@this` ou `window.Livewire`
- [ ] Utiliser `@entangle().live` pour la synchronisation bidirectionnelle
- [ ] Nettoyer les ressources dans `destroy()` si nécessaire

**Exemple** :
```javascript
x-data="{
    myProperty: @entangle('myProperty').live,

    init() {
        this.$nextTick(() => {
            this.initSomething();
        });

        this.$watch('myProperty', () => {
            this.handleChange();
        });
    },

    initSomething() {
        const data = Array.isArray(this.myProperty) ? this.myProperty : [];
        // Utiliser data en toute sécurité
    },

    handleChange() {
        this.$wire.set('someOtherProperty', newValue);
    },

    destroy() {
        // Nettoyer si nécessaire (ex: instance Flatpickr)
    }
}"
```

---

## 🔧 Patterns recommandés

### Pattern 1 : Synchronisation bidirectionnelle

```javascript
// Alpine
selectedValue: @entangle('selectedValue').live
```

```php
// Livewire
public $selectedValue;

public function updatedSelectedValue($value)
{
    // Se déclenche automatiquement à chaque changement
}
```

**Utilisation** : Pour les inputs, selects, checkboxes

---

### Pattern 2 : Mise à jour manuelle

```javascript
// Alpine
@click="$wire.set('status', 'active')"
```

```php
// Livewire
public $status;
```

**Utilisation** : Pour les boutons, actions ponctuelles

---

### Pattern 3 : Appel de méthode Livewire

```javascript
// Alpine
@click="$wire.submitForm()"
```

```php
// Livewire
public function submitForm()
{
    $this->validate();
    // Logique métier
}
```

**Utilisation** : Pour les actions complexes avec logique serveur

---

### Pattern 4 : Récupération de données computed

```javascript
// Alpine
init() {
    const points = this.$wire.pickupPoints;  // Propriété publique
}
```

```php
// Livewire
public function getPickupPointsProperty()
{
    return PickupSlot::all();
}
```

**⚠️ Attention** : Fonctionne en lecture, pas avec `@entangle`

---

## 🐛 Debugging

### Vérifier l'état Livewire

```javascript
// Dans la console du navigateur
console.log(this.$wire);  // Depuis un composant Alpine
console.log(this.$wire.get('myProperty'));
```

### Vérifier l'ID du composant

```javascript
console.log(this.$wire.__instance.id);  // ID actuel
```

### Logger les changements

```javascript
this.$watch('myProperty', (newValue, oldValue) => {
    console.log('Changed:', oldValue, '->', newValue);
});
```

---

## 📚 Ressources

### Documentation officielle

- [Livewire 3 Docs](https://livewire.laravel.com/docs)
- [Alpine.js Docs](https://alpinejs.dev/)
- [Livewire + Alpine Guide](https://livewire.laravel.com/docs/alpine)

### Syntaxes clés

| Syntaxe | Description | Exemple |
|---------|-------------|---------|
| `@entangle('prop')` | Sync bidirectionnelle | `value: @entangle('value')` |
| `@entangle('prop').live` | Sync temps réel | `search: @entangle('search').live` |
| `$wire.set('prop', val)` | Mise à jour manuelle | `$wire.set('status', 'done')` |
| `$wire.myMethod()` | Appel de méthode | `$wire.submitForm()` |
| `$wire.myProperty` | Lecture de propriété | `const val = $wire.count` |
| `$nextTick()` | Attend le prochain cycle | `$nextTick(() => init())` |

---

## 🎯 Résumé

### À faire ✅

1. Utiliser `$wire` partout (cohérence)
2. Déclarer les propriétés publiquement dans Livewire
3. Protéger avec `Array.isArray()`, `typeof`, etc.
4. Attendre Livewire avec `$nextTick()`
5. Nettoyer avec `destroy()` si nécessaire

### À éviter ❌

1. Mélanger `@this`, `$wire`, `window.Livewire.find()`
2. Utiliser computed properties avec `@entangle`
3. Importer Alpine manuellement (déjà dans Livewire)
4. Accéder aux propriétés sans vérification
5. Utiliser `{{ $variable }}` pour des valeurs dynamiques

---

## 🚀 Résultat

En suivant ces pratiques :
- ✅ Zéro erreur console
- ✅ Code maintenable
- ✅ Performance optimale
- ✅ Expérience développeur agréable

**Principe** : "Keep it simple, keep it consistent" (KISS + Cohérence)
