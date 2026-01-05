# 🚀 Optimisations de Performance - Environnement Local

## ✅ Optimisations APPLIQUÉES

### 1. **Cache Laravel**
```bash
✓ Configuration cachée (config:cache)
✓ Routes cachées (route:cache)
✓ Events cachés (event:cache)
✓ Views/Blade cachées (view:cache)
✓ Filament components cachés
✓ Blade icons cachés
```

### 2. **Drivers optimisés**
```env
CACHE_STORE=file        # Changé de 'database' → 'file' (plus rapide en local)
SESSION_DRIVER=file     # Changé de 'database' → 'file' (plus rapide en local)
```

---

## 🔧 Optimisations RECOMMANDÉES (à faire manuellement)

### 3. **Activer OPcache PHP (FORTEMENT RECOMMANDÉ)**

**Impact** : 🚀 **+30-50% de vitesse**

**Étapes** :

1. **Trouvez votre fichier php.ini** :
   ```bash
   php --ini
   ```
   Cherchez la ligne : `Loaded Configuration File`

2. **Éditez php.ini** et ajoutez/modifiez :
   ```ini
   [opcache]
   opcache.enable=1
   opcache.enable_cli=1
   opcache.jit=tracing
   opcache.jit_buffer_size=100M
   opcache.memory_consumption=256
   opcache.interned_strings_buffer=16
   opcache.max_accelerated_files=20000
   opcache.validate_timestamps=1
   opcache.revalidate_freq=2
   ```

3. **Redémarrez PHP** (ou votre serveur web)

4. **Vérifiez** :
   ```bash
   php -i | grep opcache.enable
   ```
   Doit afficher : `opcache.enable => On => On`

---

### 4. **Optimiser SQLite (déjà en place)**

SQLite est déjà rapide en local, mais vérifiez les indexes :
```bash
php artisan migrate:status
```

---

### 5. **Optimiser Vite (si lent au rechargement)**

Dans `vite.config.js`, ajoutez :
```js
export default defineConfig({
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: false,
        },
    },
    // ...
});
```

---

## 📊 Gains de Performance Attendus

| Optimisation | Gain estimé | Appliquée |
|-------------|-------------|-----------|
| Cache Laravel | +20-30% | ✅ OUI |
| File Cache/Session | +10-15% | ✅ OUI |
| OPcache PHP | +30-50% | ⚠️ À FAIRE |
| JIT PHP 8.4 | +15-25% | ⚠️ À FAIRE |

**Total potentiel** : **~75-120% plus rapide** 🚀

---

## 🔄 Commandes Utiles

### Clear cache (en développement)
```bash
php artisan optimize:clear
```

### Re-cacher (après modifications)
```bash
php artisan optimize
php artisan view:cache
php artisan filament:cache-components
```

### Clear spécifique
```bash
php artisan config:clear     # Clear config cache
php artisan route:clear      # Clear route cache
php artisan view:clear       # Clear view cache
```

---

## ⚡ Conseils Supplémentaires

### 1. **Désactiver Xdebug en local** - (si installé)
Xdebug ralentit énormément PHP. Désactivez-le quand vous ne debuggez pas :
```bash
php -d xdebug.mode=off artisan serve
```

### 2. **Utiliser le serveur de dev Laravel**
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### 3. **Monitoring des performances**
Installez Laravel Telescope pour analyser les requêtes lentes :
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

---

## 🎯 Résumé

**Optimisations déjà appliquées** :
- ✅ Cache Laravel complet
- ✅ File cache au lieu de database
- ✅ File sessions au lieu de database

**À faire manuellement** :
- ⚠️ Activer OPcache dans php.ini (**IMPORTANT**)
- ⚠️ Activer JIT dans php.ini

**Résultat attendu** : Site **2x plus rapide** ! 🚀
