# Variables d'environnement Railway - Configuration complète

## Instructions
Copie ces variables dans Railway : **Variables** tab de ton service Laravel

---

## Variables à configurer

```env
# === Application ===
APP_NAME=Domaine des Papangues
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Indian/Reunion
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
LOG_LEVEL=info
LOG_CHANNEL=stack

# === Clé d'application ===
APP_KEY=base64:Q/eNwtn7up/NaKj8VtNmsNMjrS2FeWtpIPeJPEjnc98=

# === Base de données MySQL ===
# IMPORTANT : Utilise la référence au service MySQL Railway
DB_CONNECTION=mysql
DATABASE_URL=${{MySQL.DATABASE_URL}}

# === Session et Cache ===
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

# === Filesystem (Cloudflare R2) ===
FILESYSTEM_DISK=r2
AWS_ACCESS_KEY_ID=71d9a0d829d82fa3e3a8d20cd910343a
AWS_SECRET_ACCESS_KEY=629029545c3f0d2d35b18b6422f2ec91bb50114898ecd3526b15c4d003926aeb
AWS_DEFAULT_REGION=auto
AWS_BUCKET=maraicher-images
AWS_ENDPOINT=https://898047b4c422ffe9966cc1cb7493ceed.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true

# === Mail (log en production pour l'instant) ===
MAIL_MAILER=log

# === Stripe (clés de test) ===
STRIPE_KEY=pk_test_your_stripe_publishable_key
STRIPE_SECRET=sk_test_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

---

## Notes importantes

1. **DATABASE_URL** : La valeur `${{MySQL.DATABASE_URL}}` est une référence Railway qui sera automatiquement remplacée par l'URL de connexion MySQL
   - Si ton service MySQL s'appelle autrement que "MySQL", remplace "MySQL" par le nom exact

2. **NE PAS AJOUTER** ces variables (elles entrent en conflit avec DATABASE_URL) :
   - ❌ DB_HOST
   - ❌ DB_PORT
   - ❌ DB_DATABASE
   - ❌ DB_USERNAME
   - ❌ DB_PASSWORD

3. **APP_URL** : Railway va générer une URL automatiquement, tu n'as pas besoin de la configurer maintenant

4. **Cloudflare R2** : Tu devras créer le bucket "maraicher-images" dans Cloudflare R2

---

## Custom Start Command (à ajouter dans Railway)

Va dans **Settings** → **Deploy** → **Custom Start Command** et ajoute :

```bash
chmod +x ./railway/init-app.sh && sh ./railway/init-app.sh && php artisan serve --host=0.0.0.0 --port=$PORT
```

Cette commande va :
1. Rendre le script exécutable
2. Lancer les migrations
3. Lancer les seeders (uniquement si la base est vide)
4. Démarrer le serveur Laravel
