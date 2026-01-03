# 🚂 Déploiement Railway - Guide Rapide

## ✅ Prérequis - Déjà configuré

- ✅ Code sur GitHub : https://github.com/Fredo9741/mairaicher-mvp
- ✅ Cloudflare R2 configuré (bucket + clés API)
- ✅ APP_KEY générée

---

## 🚀 Étape 1 : Créer un bucket R2

1. Va sur https://dash.cloudflare.com
2. Clique sur **R2** dans le menu latéral
3. Clique sur **Create bucket**
4. Nom du bucket : `maraicher-images` (ou autre nom)
5. Clique sur **Create bucket**

> ✅ Les clés API R2 sont déjà générées et configurées dans `.env.railway`

---

## 🚂 Étape 2 : Déployer sur Railway

### 2.1 Créer le projet

1. Va sur https://railway.app
2. Connecte-toi avec GitHub
3. Clique sur **New Project**
4. Choisis **Deploy from GitHub repo**
5. Sélectionne `Fredo9741/mairaicher-mvp`

### 2.2 Ajouter PostgreSQL

1. Dans ton projet Railway, clique sur **+ New**
2. Sélectionne **Database** > **PostgreSQL**
3. Railway créera la base de données automatiquement

### 2.3 Configurer les variables d'environnement

Clique sur ton service Laravel (pas la base de données) > **Variables**

**Copie-colle toutes ces variables :**

```env
APP_NAME=Maraîcher MVP
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Indian/Reunion
APP_LOCALE=fr
LOG_LEVEL=info

# Clé d'application (IMPORTANT !)
APP_KEY=base64:Q/eNwtn7up/NaKj8VtNmsNMjrS2FeWtpIPeJPEjnc98=

# Base de données
DB_CONNECTION=pgsql

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Cloudflare R2 Storage
FILESYSTEM_DISK=r2
AWS_ACCESS_KEY_ID=71d9a0d829d82fa3e3a8d20cd910343a
AWS_SECRET_ACCESS_KEY=629029545c3f0d2d35b18b6422f2ec91bb50114898ecd3526b15c4d003926aeb
AWS_DEFAULT_REGION=auto
AWS_BUCKET=maraicher-images
AWS_ENDPOINT=https://898047b4c422ffe9966cc1cb7493ceed.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
```

> ⚠️ **IMPORTANT** : Railway injecte automatiquement `DATABASE_URL` pour PostgreSQL, pas besoin de l'ajouter.

### 2.4 Obtenir l'URL de l'application

1. Dans Railway, va dans **Settings** > **Networking**
2. Clique sur **Generate Domain**
3. Railway te donnera une URL comme : `https://ton-app-xxx.up.railway.app`
4. Copie cette URL

### 2.5 Mettre à jour APP_URL

1. Retourne dans **Variables**
2. Ajoute une nouvelle variable :

```env
APP_URL=https://ton-app-xxx.up.railway.app
```

(Remplace par l'URL que Railway t'a donnée)

### 2.6 Déploiement automatique

Railway va maintenant :
- ✅ Installer les dépendances PHP (Composer)
- ✅ Installer les dépendances Node.js (NPM)
- ✅ Compiler les assets Vite
- ✅ Publier les assets Filament
- ✅ Lancer les migrations
- ✅ Démarrer l'application

**⏱️ Le premier déploiement prend environ 3-5 minutes.**

Tu peux suivre la progression dans **Deployments** > Clique sur le déploiement en cours > **View Logs**

---

## 🌱 Étape 3 : Peupler la base de données

Une fois le déploiement terminé, tu dois lancer les seeders pour créer les données de test.

### Option A : Via Railway CLI (Recommandé)

```bash
# Installer Railway CLI
npm install -g @railway/cli

# Se connecter
railway login

# Se lier au projet
railway link

# Lancer les seeders
railway run php artisan db:seed --force
```

### Option B : Via l'interface Railway

1. Va dans **Settings** > **Build & Deploy**
2. Dans **Custom Start Command**, change en :
```bash
php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

3. Redéploie l'application (Settings > Redeploy)

> ⚠️ **Attention** : Cette option va seeder à chaque déploiement. À utiliser seulement pour le premier déploiement, puis retire `&& php artisan db:seed --force`.

---

## ✅ Étape 4 : Tester l'application

### 4.1 Accéder à l'application

Visite : `https://ton-app-xxx.up.railway.app`

Tu devrais voir la page d'accueil avec les produits et paniers.

### 4.2 Accéder à l'admin Filament

1. Visite : `https://ton-app-xxx.up.railway.app/admin`
2. Connecte-toi avec :
   - **Email** : `admin@maraicher.test`
   - **Mot de passe** : `password`

### 4.3 Tester l'upload d'images R2

1. Dans l'admin, va dans **Produits**
2. Clique sur un produit pour le modifier
3. Upload une image
4. L'image devrait être stockée dans ton bucket R2 Cloudflare

---

## 🔧 Dépannage

### Erreur "No application encryption key"

Si tu vois cette erreur, vérifie que `APP_KEY` est bien configuré dans les variables Railway.

### Erreur de connexion à la base de données

Railway injecte automatiquement `DATABASE_URL`. Assure-toi que :
- PostgreSQL est bien créé dans ton projet
- `DB_CONNECTION=pgsql` est défini dans les variables

### Images ne s'uploadent pas

1. Vérifie que `FILESYSTEM_DISK=r2` est dans les variables
2. Vérifie les credentials R2
3. Vérifie que le bucket `maraicher-images` existe bien dans Cloudflare R2

### Voir les logs

Dans Railway : **Deployments** > Clique sur le déploiement > **View Logs**

Ou via CLI :
```bash
railway logs
```

---

## 📊 Après le déploiement

### Changer le mot de passe admin

**IMPORTANT** : Change le mot de passe par défaut en production !

1. Va dans l'admin : `/admin`
2. Clique sur ton nom (en haut à droite) > **Profile**
3. Change le mot de passe

### Désactiver les données de test (optionnel)

Les seeders créent des données de test. Pour la production :

1. Supprime les données de test via l'admin Filament
2. Ou modifie les seeders dans `database/seeders/` pour créer de vraies données

### Déploiements futurs

Chaque fois que tu push sur GitHub :

```bash
git add .
git commit -m "Nouvelle fonctionnalité"
git push origin main
```

Railway redéploiera automatiquement ! 🚀

---

## 📝 Checklist finale

- [ ] Bucket R2 `maraicher-images` créé
- [ ] Projet Railway créé avec GitHub
- [ ] PostgreSQL ajouté
- [ ] Toutes les variables d'environnement configurées
- [ ] APP_URL mis à jour avec l'URL Railway
- [ ] Seeders lancés
- [ ] Application accessible
- [ ] Admin accessible (`/admin`)
- [ ] Upload d'images fonctionne
- [ ] Mot de passe admin changé

---

## 💰 Coûts

**Cloudflare R2**
- 10 GB/mois gratuit
- Pas de frais de sortie de données

**Railway**
- $5/mois de crédit gratuit (plan Hobby)
- Largement suffisant pour ce MVP

---

**Félicitations ! Ton application est en production ! 🎉**

URL de ton app : https://ton-app-xxx.up.railway.app
URL admin : https://ton-app-xxx.up.railway.app/admin
