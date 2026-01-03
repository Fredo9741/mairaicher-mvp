# Guide de déploiement - Railway + Cloudflare R2

Ce guide vous accompagne pour déployer l'application sur Railway avec Cloudflare R2 pour le stockage des images.

## 📋 Prérequis

- Compte [Railway](https://railway.app)
- Compte [Cloudflare](https://cloudflare.com) avec R2 activé
- Compte [GitHub](https://github.com) (optionnel mais recommandé)

---

## 🪣 Étape 1 : Configuration Cloudflare R2

### 1.1 Créer un bucket R2

1. Connectez-vous à votre [tableau de bord Cloudflare](https://dash.cloudflare.com)
2. Cliquez sur **R2** dans le menu latéral
3. Cliquez sur **Create bucket**
4. Nommez votre bucket (ex: `maraicher-images`)
5. Cliquez sur **Create bucket**

### 1.2 Obtenir les clés d'API R2

1. Dans R2, cliquez sur **Manage R2 API Tokens**
2. Cliquez sur **Create API token**
3. Donnez un nom (ex: `maraicher-production`)
4. Permissions : **Object Read & Write**
5. Cliquez sur **Create API Token**
6. **⚠️ IMPORTANT** : Notez ces valeurs (vous ne les reverrez plus) :
   - `Access Key ID`
   - `Secret Access Key`
   - `Endpoint URL` (format: `https://xxxxx.r2.cloudflarestorage.com`)

### 1.3 Configurer CORS (optionnel, pour upload depuis le navigateur)

1. Sélectionnez votre bucket
2. Allez dans **Settings** > **CORS policy**
3. Ajoutez cette configuration :

```json
[
  {
    "AllowedOrigins": ["*"],
    "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
    "AllowedHeaders": ["*"],
    "ExposeHeaders": ["ETag"],
    "MaxAgeSeconds": 3000
  }
]
```

---

## 🚂 Étape 2 : Déploiement sur Railway

### 2.1 Pousser le code sur GitHub (recommandé)

```bash
# Ajouter tous les fichiers
git add .

# Créer le premier commit
git commit -m "Initial commit - MVP Maraîcher"

# Créer un repo sur GitHub puis :
git remote add origin https://github.com/VOTRE-USERNAME/maraicher-mvp.git
git branch -M main
git push -u origin main
```

### 2.2 Créer un projet Railway

1. Allez sur [railway.app](https://railway.app)
2. Cliquez sur **New Project**
3. Sélectionnez **Deploy from GitHub repo**
4. Autorisez Railway à accéder à vos repos GitHub
5. Sélectionnez votre repository `maraicher-mvp`

### 2.3 Ajouter une base de données PostgreSQL

1. Dans votre projet Railway, cliquez sur **+ New**
2. Sélectionnez **Database** > **PostgreSQL**
3. Railway créera automatiquement la base de données

### 2.4 Configurer les variables d'environnement

1. Cliquez sur votre service (l'application Laravel)
2. Allez dans l'onglet **Variables**
3. Ajoutez les variables suivantes :

#### Variables Laravel essentielles

```env
APP_NAME=Domaine des Papangues
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Indian/Reunion
APP_URL=https://votre-app.up.railway.app

APP_LOCALE=fr
LOG_LEVEL=info
```

#### Générer APP_KEY

Dans votre terminal local :
```bash
php artisan key:generate --show
```

Copiez la clé générée et ajoutez-la :
```env
APP_KEY=base64:VOTRE_CLE_GENEREE_ICI
```

#### Variables Cloudflare R2

```env
FILESYSTEM_DISK=r2

AWS_ACCESS_KEY_ID=votre_access_key_id_r2
AWS_SECRET_ACCESS_KEY=votre_secret_access_key_r2
AWS_DEFAULT_REGION=auto
AWS_BUCKET=maraicher-images
AWS_ENDPOINT=https://xxxxx.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
```

#### Variables PostgreSQL (Railway les fournit automatiquement)

Railway injecte automatiquement ces variables, mais vous pouvez les vérifier :
- `DATABASE_URL` (Railway la crée automatiquement)

Ajoutez aussi pour Laravel :
```env
DB_CONNECTION=pgsql
```

### 2.5 Configurer le domaine

1. Dans Railway, allez dans **Settings** > **Networking**
2. Cliquez sur **Generate Domain**
3. Railway vous donnera une URL comme `votre-app.up.railway.app`
4. Mettez à jour `APP_URL` avec cette URL

### 2.6 Premier déploiement

Railway va automatiquement :
1. Détecter que c'est une app Laravel (via `composer.json`)
2. Installer les dépendances PHP et Node.js
3. Compiler les assets avec Vite
4. Lancer les migrations automatiquement
5. Démarrer l'application

**⚠️ Important** : Le premier déploiement prend 3-5 minutes.

---

## 🌱 Étape 3 : Peupler la base de données

### Option A : Via Railway CLI

```bash
# Installer Railway CLI
npm install -g @railway/cli

# Se connecter
railway login

# Se lier au projet
railway link

# Exécuter les seeders
railway run php artisan db:seed
```

### Option B : Via l'interface Railway

1. Allez dans **Settings** > **Build & Deploy**
2. Ajoutez dans **Custom Build Command** :
```bash
php artisan migrate --force && php artisan db:seed --force
```

**⚠️ Attention** : Les seeders vont créer des données de test. Pour la production, adaptez les seeders.

---

## ✅ Étape 4 : Vérification

### 4.1 Tester l'application

1. Visitez `https://votre-app.up.railway.app`
2. Vous devriez voir la page d'accueil avec les produits

### 4.2 Tester l'admin Filament

1. Visitez `https://votre-app.up.railway.app/admin`
2. Connectez-vous avec :
   - Email: `admin@maraicher.test`
   - Mot de passe: `password`

**⚠️ SÉCURITÉ** : Changez immédiatement ce mot de passe en production !

### 4.3 Tester l'upload d'images

1. Dans l'admin, allez dans **Produits**
2. Modifiez un produit
3. Uploadez une image
4. L'image devrait être stockée dans votre bucket R2

---

## 🔧 Configuration avancée

### Domaine personnalisé

1. Dans Railway **Settings** > **Networking**
2. Cliquez sur **Custom Domain**
3. Ajoutez votre domaine (ex: `maraicher.votredomaine.com`)
4. Configurez les DNS selon les instructions Railway

### SSL/HTTPS

Railway fournit automatiquement un certificat SSL via Let's Encrypt.

### Logs

Consultez les logs en temps réel :
```bash
railway logs
```

Ou dans l'interface Railway : **Deployments** > Cliquez sur un déploiement > **View Logs**

---

## 🐛 Dépannage

### Erreur "No application encryption key"

```bash
railway run php artisan key:generate
```

Puis copiez la clé générée dans les variables d'environnement.

### Images ne s'uploadent pas

1. Vérifiez les credentials R2 dans les variables
2. Vérifiez que `FILESYSTEM_DISK=r2`
3. Testez la connexion :
```bash
railway run php artisan tinker
```
```php
Storage::disk('r2')->put('test.txt', 'Hello R2');
```

### Base de données vide

```bash
railway run php artisan migrate:fresh --seed --force
```

**⚠️ Attention** : Cela supprime toutes les données !

---

## 📊 Monitoring

### Surveiller l'utilisation R2

1. Cloudflare Dashboard > **R2**
2. Consultez les métriques de stockage et bande passante

### Surveiller Railway

1. Railway Dashboard > **Metrics**
2. CPU, RAM, Network utilization

---

## 💰 Coûts estimés

### Cloudflare R2
- **10 GB/mois gratuits** de stockage
- Ensuite : ~$0.015/GB/mois
- Pas de frais de sortie de données 🎉

### Railway
- **$5/mois de crédit gratuit**
- Plan Hobby : $5/mois
- Plan Pro : $20/mois

---

## 🔄 Déploiements futurs

Après la configuration initiale, chaque `git push` sur la branche `main` déclenchera automatiquement un déploiement sur Railway.

```bash
git add .
git commit -m "Ajout nouvelle fonctionnalité"
git push origin main
```

Railway rebuild et redéploie automatiquement ! 🚀

---

## 🔐 Sécurité - Checklist avant production

- [ ] Changer le mot de passe admin par défaut
- [ ] Activer `APP_DEBUG=false`
- [ ] Configurer `APP_ENV=production`
- [ ] Vérifier les CORS R2
- [ ] Configurer les sauvegardes PostgreSQL (Railway)
- [ ] Activer le monitoring (Railway + Cloudflare)
- [ ] Configurer un domaine HTTPS personnalisé
- [ ] Restreindre l'accès admin si nécessaire (IP whitelisting)

---

**Félicitations ! Votre application est maintenant en production ! 🎉**
