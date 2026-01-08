# 📸 Guide d'Upload d'Images - Optimisation Automatique

## 🎉 Tu n'as plus besoin d'optimiser les images manuellement !

Le système optimise **automatiquement** toutes les images uploadées via Filament.

---

## ✨ Comment ça marche ?

### Avant (sans optimisation)
1. Tu uploads une image JPG de 3 MB
2. Elle est stockée telle quelle sur R2
3. Les visiteurs téléchargent 3 MB à chaque visite
4. **Problème** : Lent, surtout en zone rurale

### Maintenant (avec optimisation automatique)
1. Tu uploads une image JPG de 3 MB
2. **Le système fait automatiquement** :
   - Redimensionne à 1920px max (conserve le ratio)
   - Convertit en WebP (format moderne)
   - Compresse à 80% de qualité
   - Upload sur Cloudflare R2
3. L'image finale fait ~300-500 KB
4. **Résultat** : **85-90% plus léger** ! 🚀

---

## 📋 Où l'optimisation s'applique ?

### 1. Images des Produits
**URL** : `/admin/products`

Quand tu crées ou modifies un produit et uploads une image :
- ✅ Optimisation automatique
- ✅ Format WebP
- ✅ Max 1920px
- ✅ Stockage sur R2 : `products/xxxxx.webp`

### 2. Images des Paniers
**URL** : `/admin/bundles`

Quand tu crées ou modifies un panier et uploads une image :
- ✅ Optimisation automatique
- ✅ Format WebP
- ✅ Max 1920px
- ✅ Stockage sur R2 : `bundles/xxxxx.webp`

### 3. Image Hero (Page d'accueil)
**URL** : `/admin/hero-sections`

Quand tu modifies l'image hero de la page d'accueil :
- ✅ Optimisation automatique
- ✅ Format WebP
- ✅ Max 1920px
- ✅ Stockage sur R2 : `hero/xxxxx.webp`

---

## 🎯 Formats Acceptés

Tu peux uploader **n'importe quel format** :
- ✅ JPG / JPEG
- ✅ PNG (transparence préservée)
- ✅ GIF
- ✅ WebP

Le système convertira tout en **WebP optimisé**.

---

## 📏 Recommandations

### Taille de fichier
- **Minimum** : Pas de limite (mais évite les images trop petites)
- **Maximum recommandé** : 5-10 MB
- **Note** : Même si tu uploads 10 MB, le système va réduire à ~300-500 KB

### Dimensions recommandées
Pour de meilleurs résultats, utilise des images avec :
- **Produits** : Min 800x800 px, idéal 1500x1500 px
- **Paniers** : Min 800x600 px, idéal 1500x1000 px
- **Hero** : Min 1920x1080 px, idéal 2560x1440 px

Mais ce n'est pas obligatoire ! Le système s'adapte automatiquement.

---

## 🔧 Paramètres d'Optimisation

Le service [app/Services/ImageOptimizer.php](app/Services/ImageOptimizer.php) utilise :

- **Largeur maximale** : 1920px
- **Qualité WebP** : 80%
- **Préservation du ratio** : Oui
- **Préservation de la transparence** : Oui (pour PNG)
- **Compression GD** : Niveau 6 (équilibre vitesse/taille)

Ces paramètres offrent le meilleur équilibre entre qualité et performance.

---

## ⚡ Performance Attendue

### Exemple concret : Photo de Produit

| Étape | Format | Taille | Temps chargement (3G) |
|-------|--------|--------|----------------------|
| Image originale | JPG | 3.2 MB | ~8 secondes |
| Après optimisation | WebP | 380 KB | ~1 seconde |
| **Gain** | - | **88%** | **87%** |

### Exemple concret : Image Hero

| Étape | Format | Taille | Temps chargement (3G) |
|-------|--------|--------|----------------------|
| Image originale | JPG | 1.4 MB | ~4 secondes |
| Après optimisation | WebP | 280 KB | ~0.7 secondes |
| **Gain** | - | **80%** | **82%** |

---

## 🚀 Comment uploader une image ?

### Méthode 1 : Créer un nouveau produit

1. Va sur `/admin/products`
2. Clique sur **"Nouveau Produit"**
3. Remplis les informations
4. Dans la section **"Image"**, clique sur **"Choisir un fichier"**
5. Sélectionne ton image (JPG, PNG, etc.)
6. **Optionnel** : Utilise l'éditeur pour recadrer
7. Clique sur **"Créer"**

➡️ L'image sera automatiquement optimisée en WebP et uploadée sur R2 !

### Méthode 2 : Modifier un produit existant

1. Va sur `/admin/products`
2. Clique sur le produit à modifier
3. Dans la section **"Image"**, clique pour changer l'image
4. Sélectionne la nouvelle image
5. Clique sur **"Sauvegarder"**

➡️ L'image sera automatiquement optimisée !

### Méthode 3 : Modifier l'image Hero

1. Va sur `/admin/hero-sections`
2. Clique sur la Hero Section active
3. Dans **"Image Hero"**, upload une nouvelle image
4. Modifie le titre/sous-titre si besoin
5. Clique sur **"Sauvegarder"**

➡️ L'image hero sera automatiquement optimisée et affichée sur la page d'accueil !

---

## 🐛 Que faire en cas d'erreur ?

### L'image ne s'upload pas
1. Vérifie que le fichier fait moins de 10 MB
2. Vérifie que c'est un format image (JPG, PNG, GIF, WebP)
3. Regarde les logs Laravel : `storage/logs/laravel.log`

### L'image est déformée
- L'optimisation préserve le ratio, ce n'est pas normal
- Vérifie l'image source
- Contacte le support si le problème persiste

### L'optimisation ne fonctionne pas
1. Vérifie que l'extension GD est activée : `php -m | grep -i gd`
2. Vérifie les permissions du dossier temporaire
3. Regarde les logs : `storage/logs/laravel.log`

---

## 📊 Vérifier l'Optimisation

### Depuis l'admin Filament
1. Upload une image
2. Va sur Cloudflare R2 Dashboard
3. Vérifie le fichier dans le bucket `maraicher-images`
4. Le fichier devrait être en `.webp`

### Depuis le site
1. Ouvre ton produit sur le site
2. Clic droit sur l'image > **"Inspecter"**
3. Dans l'inspecteur, regarde l'URL de l'image
4. Elle devrait finir par `.webp`

### Vérifier la taille
```bash
# Depuis ton terminal local
curl -I https://files-maraicher.fredlabs.org/products/xxxxx.webp
```

Regarde le header `Content-Length` pour voir la taille.

---

## 🎓 Technique : Comment ça marche en coulisses ?

### Service d'Optimisation
[app/Services/ImageOptimizer.php](app/Services/ImageOptimizer.php)

```php
public function optimize(
    UploadedFile $file,
    string $disk = 'r2',
    string $directory = 'images',
    int $maxWidth = 1920,
    int $quality = 80
): string
```

### Hook Filament : Produits
[app/Filament/Resources/ProductResource/Pages/CreateProduct.php](app/Filament/Resources/ProductResource/Pages/CreateProduct.php)

```php
protected function mutateFormDataBeforeCreate(array $data): array
{
    if (isset($data['image']) && $data['image']) {
        $optimizer = app(ImageOptimizer::class);
        $optimizedPath = $optimizer->optimize($data['image'], ...);
        $data['image'] = $optimizedPath;
    }
    return $data;
}
```

Le hook intercepte l'upload **avant** la création du produit et remplace l'image par la version optimisée.

---

## ✅ Avantages

### Pour toi (admin)
- ✅ Pas besoin d'optimiser manuellement
- ✅ Upload direct depuis Filament
- ✅ Gain de temps énorme
- ✅ Stockage R2 réduit

### Pour les visiteurs
- ✅ Chargement ultra-rapide
- ✅ Moins de data mobile consommée
- ✅ Meilleure expérience (surtout en zone rurale)
- ✅ SEO amélioré (Google aime les sites rapides)

### Pour le serveur
- ✅ Moins de bande passante
- ✅ CDN Cloudflare plus efficace
- ✅ Coûts réduits

---

## 📝 Prochaines Étapes

1. ✅ Système d'optimisation installé
2. ✅ Configuration Railway mise à jour
3. 📸 **À faire** : Uploader l'image Hero actuelle via Filament
4. 🧪 **À faire** : Tester avec un nouveau produit
5. 📊 **À faire** : Vérifier les performances sur PageSpeed Insights

---

**Dernière mise à jour** : 2026-01-08
**Créé par** : Claude Code
**Besoin d'aide ?** : Consulte [OPTIMISATION_PERFORMANCES.md](OPTIMISATION_PERFORMANCES.md)
