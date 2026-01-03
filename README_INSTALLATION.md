# MVP E-commerce Maraîcher - Guide d'installation

Application Laravel 11 pour la vente de légumes, volaille et paniers de saison avec interface d'administration Filament PHP.

## 🚀 Stack Technique

- **Backend**: Laravel 11 / PHP 8.3
- **Base de données**: PostgreSQL
- **Admin**: Filament PHP 3.2
- **Frontend**: Livewire + Tailwind CSS
- **Stockage**: Cloudflare R2
- **Paiement**: Stripe

## 📋 Prérequis

- PHP 8.3 ou supérieur
- Composer
- Node.js & NPM
- PostgreSQL (ou utilisez SQLite pour tester en local)
- Extension PHP : `pdo_pgsql`, `gd`, `zip`, `xml`, `mbstring`, `curl`

## 🛠️ Installation

### 1. Cloner le projet

```bash
cd DomainedesPapangues
```

### 2. Installer les dépendances

```bash
# Dépendances PHP
composer install

# Dépendances NPM
npm install
```

### 3. Configuration de l'environnement

Le fichier `.env` est déjà configuré. Vous devez modifier les valeurs suivantes :

#### Base de données PostgreSQL

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=maraicher_mvp
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe
```

**Alternative SQLite pour tester localement :**

```env
DB_CONNECTION=sqlite
# Commentez les autres lignes DB_*
```

Puis créez le fichier database :
```bash
touch database/database.sqlite
```

#### Cloudflare R2 (Stockage des images)

```env
AWS_ACCESS_KEY_ID=votre_r2_access_key_id
AWS_SECRET_ACCESS_KEY=votre_r2_secret_access_key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=votre_r2_bucket_name
AWS_ENDPOINT=https://your-account-id.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
```

#### Stripe (Paiements)

```env
STRIPE_KEY=pk_test_votre_cle_publique
STRIPE_SECRET=sk_test_votre_cle_secrete
STRIPE_WEBHOOK_SECRET=whsec_votre_webhook_secret
```

### 4. Générer la clé d'application

```bash
php artisan key:generate
```

### 5. Créer la base de données

**PostgreSQL :**
```bash
# Connectez-vous à PostgreSQL
psql -U postgres

# Créez la base de données
CREATE DATABASE maraicher_mvp;

# Quittez
\q
```

**SQLite :**
Le fichier est déjà créé à l'étape 3.

### 6. Exécuter les migrations

```bash
php artisan migrate
```

### 7. Peupler la base de données (Seeders)

```bash
php artisan db:seed
```

Cela va créer :
- Un utilisateur admin (email: `admin@maraicher.test`, mot de passe: `password`)
- 3 créneaux horaires de retrait
- 12 produits (8 légumes, 3 volailles, 1 autre)
- 4 paniers de saison prédéfinis

### 8. Compiler les assets

```bash
# Développement
npm run dev

# Production
npm run build
```

### 9. Lancer le serveur

```bash
php artisan serve
```

L'application sera accessible sur : `http://localhost:8000`

## 🔐 Accès à l'administration

**URL** : `http://localhost:8000/admin`

**Identifiants par défaut :**
- Email : `admin@maraicher.test`
- Mot de passe : `password`

## 📦 Fonctionnalités de l'administration

### Produits
- Gestion complète des produits (légumes/volaille/autre)
- Upload d'images vers Cloudflare R2
- Gestion du stock en temps réel
- Prix en centimes
- Unités : kg ou pièce
- Activation/désactivation

### Paniers de saison
- Composition de paniers avec plusieurs produits
- Quantités personnalisables par produit
- Prix forfaitaire
- Vérification automatique de la disponibilité des produits

### Créneaux de retrait
- Gestion des horaires de retrait
- Créneaux configurables (nom, heure début/fin)
- Compteur de commandes par créneau
- Activation/désactivation

### Commandes
- Vue complète des commandes clients
- Gestion des statuts (pending, paid, ready, completed, cancelled)
- Détails client et articles commandés
- Filtres par statut et date de retrait
- Notes internes

## 🗂️ Structure de la base de données

### Tables principales

- **products** : Produits (légumes, volaille, etc.)
- **bundles** : Paniers de saison
- **bundle_product** : Table pivot (composition des paniers)
- **pickup_slots** : Créneaux horaires de retrait
- **orders** : Commandes clients
- **order_items** : Articles des commandes
- **users** : Utilisateurs admin

## 🎨 Données de test

### Produits créés
- 8 légumes : Tomates, Carottes, Salades, Courgettes, Pommes de terre, Haricots verts, Concombres, Poivrons
- 3 volailles : Poulet fermier (15€), Pintade (18€), Canard (22€)
- 1 autre : Œufs frais (boîte de 6)

### Paniers créés
1. **Panier du Marché** (12€) - Légumes pour la semaine
2. **Panier Famille** (25€) - Panier généreux
3. **Panier Volaille Complète** (28€) - Poulet + légumes
4. **Panier Découverte** (15€) - Mix de produits

### Créneaux de retrait
- Matin 9h-12h
- Après-midi 14h-18h
- Fin de journée 18h-20h

## 🚨 Dépannage

### Erreur "could not find driver"

Installez l'extension PostgreSQL pour PHP :

```bash
# Ubuntu/Debian
sudo apt-get install php8.3-pgsql

# macOS (avec Homebrew)
brew install php@8.3-pgsql

# Windows
# Décommentez extension=pdo_pgsql dans php.ini
```

### Erreur de permission sur storage/

```bash
chmod -R 775 storage bootstrap/cache
```

### Les images ne s'uploadent pas

Vérifiez votre configuration Cloudflare R2 dans le `.env` et assurez-vous que le bucket existe.

Pour tester en local sans R2, changez temporairement le disk dans les Resources :
```php
->disk('public') // au lieu de 'r2'
```

Et exécutez :
```bash
php artisan storage:link
```

## 📝 Prochaines étapes

Pour compléter le MVP, il reste à implémenter :
1. Interface publique (front-end) avec liste des produits
2. Panier d'achat en session
3. Formulaire de checkout
4. Intégration Stripe pour les paiements
5. Confirmation de commande

## 📞 Support

Pour toute question ou problème, vérifiez :
- Les logs Laravel : `storage/logs/laravel.log`
- La console du navigateur pour les erreurs JavaScript
- La configuration PHP avec `php -i | grep pdo`

---

**Développé avec Laravel 11 + Filament PHP + Livewire + Tailwind CSS**
