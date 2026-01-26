# Intégration Stripe - Domaine des Papangues

## Résumé

Cette branche `integration-stripe` ajoute le paiement en ligne via Stripe Checkout à la marketplace. Les clients peuvent désormais payer leur commande en ligne par carte bancaire, ou choisir de payer lors du retrait.

---

## Modifications effectuées

### 1. Installation de Laravel Cashier

**Fichier modifié :** `composer.json`

- Ajout de `laravel/cashier` v16.2.0
- Mise à jour de `stripe/stripe-php` vers ^17.3 (compatibilité Cashier)
- Mise à jour de `openspout/openspout` vers ^4.23 || ^5.0 (compatibilité PHP 8.4)

```bash
composer require laravel/cashier
```

### 2. Migrations de base de données

**Fichiers créés :**

| Fichier | Description |
|---------|-------------|
| `database/migrations/2026_01_18_080411_add_stripe_columns_to_users_table.php` | Ajoute les colonnes Stripe au modèle User |
| `database/migrations/2026_01_18_080436_create_subscriptions_table.php` | Table pour les futurs abonnements |
| `database/migrations/2026_01_18_080500_create_subscription_items_table.php` | Table pour les items d'abonnement |

**Colonnes ajoutées à `users` :**
- `stripe_id` - Identifiant client Stripe
- `pm_type` - Type de moyen de paiement (visa, mastercard, etc.)
- `pm_last_four` - 4 derniers chiffres de la carte
- `trial_ends_at` - Date de fin de période d'essai (pour abonnements)

### 3. Modèle User

**Fichier modifié :** `app/Models/User.php`

- Ajout du trait `Billable` de Laravel Cashier

```php
use Laravel\Cashier\Billable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, Billable;
    // ...
}
```

### 4. Contrôleurs Stripe

**Fichiers créés :**

#### `app/Http/Controllers/StripeController.php`

Gère le flux de paiement :

| Méthode | Route | Description |
|---------|-------|-------------|
| `checkout($order)` | `GET /paiement/{order}` | Crée une session Stripe Checkout et redirige |
| `success($order)` | `GET /paiement/{order}/succes` | Page de retour après paiement réussi |
| `cancel($order)` | `GET /paiement/{order}/annule` | Page de retour après annulation |

#### `app/Http/Controllers/StripeWebhookController.php`

Gère les webhooks Stripe :

| Événement | Action |
|-----------|--------|
| `checkout.session.completed` | Met à jour la commande en status `paid` |
| `payment_intent.succeeded` | Confirme le paiement |
| `payment_intent.payment_failed` | Log l'échec (commande reste en `pending`) |

### 5. Routes

**Fichier modifié :** `routes/web.php`

```php
// STRIPE PAYMENT
Route::get('/paiement/{order}', [StripeController::class, 'checkout'])->name('stripe.checkout');
Route::get('/paiement/{order}/succes', [StripeController::class, 'success'])->name('stripe.success');
Route::get('/paiement/{order}/annule', [StripeController::class, 'cancel'])->name('stripe.cancel');

// Webhook Stripe
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');
```

### 6. Exclusion CSRF pour le webhook

**Fichier modifié :** `bootstrap/app.php`

```php
$middleware->validateCsrfTokens(except: [
    'stripe/webhook',
]);
```

### 7. Configuration

**Fichier modifié :** `config/services.php`

```php
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
],
```

**Fichier modifié :** `.env.example`

```env
# Stripe Payment (https://dashboard.stripe.com/)
STRIPE_KEY=pk_test_your_stripe_publishable_key
STRIPE_SECRET=sk_test_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
CASHIER_CURRENCY=eur
CASHIER_CURRENCY_LOCALE=fr_FR
```

### 8. Flux de checkout modifié

**Fichier modifié :** `app/Livewire/CheckoutForm.php`

Après la création de la commande, redirection vers Stripe Checkout au lieu de la page de confirmation :

```php
// Avant : redirect()->route('checkout.confirmation', $order)
// Après :
return redirect()->route('stripe.checkout', $order);
```

### 9. Vue de confirmation mise à jour

**Fichier modifié :** `resources/views/checkout/confirmation.blade.php`

- Affichage dynamique selon le statut de paiement
- Bouton "Payer maintenant" si la commande est en `pending`
- Message de confirmation si la commande est `paid`
- Gestion des messages flash (success, warning, error)

---

## Nouveau flux de commande

```
┌─────────────────┐
│  Panier client  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│    Checkout     │
│  (formulaire)   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Commande créée  │
│ status: pending │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Stripe Checkout │◄──── Page hébergée par Stripe
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌───────┐ ┌───────┐
│Succès │ │Annulé │
└───┬───┘ └───┬───┘
    │         │
    ▼         ▼
┌─────────────────┐
│  Confirmation   │
│  (avec statut)  │
└─────────────────┘
         │
         ▼
┌─────────────────┐
│    Webhook      │──── Met à jour status → paid
└─────────────────┘
```

---

## Configuration sur Railway

Ajouter ces variables d'environnement :

```
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
CASHIER_CURRENCY=eur
CASHIER_CURRENCY_LOCALE=fr_FR
```

---

## Configuration du Webhook Stripe

### En production (Railway)

1. Aller sur [Stripe Dashboard > Webhooks](https://dashboard.stripe.com/webhooks)
2. Cliquer sur "Add endpoint"
3. URL : `https://votre-app.up.railway.app/stripe/webhook`
4. Événements à écouter :
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
5. Copier le `Signing secret` (whsec_...) dans `STRIPE_WEBHOOK_SECRET`

### En développement local

```bash
# Installer Stripe CLI
# https://stripe.com/docs/stripe-cli

# Écouter les webhooks et les rediriger vers localhost
stripe listen --forward-to localhost:8000/stripe/webhook

# Copier le webhook secret affiché dans .env
```

---

## Fonctionnalités prêtes pour le futur

Grâce à Laravel Cashier, ces fonctionnalités sont prêtes à être implémentées :

- **Abonnements** : Paniers récurrents (hebdomadaires, mensuels)
- **Factures** : Génération automatique de factures PDF
- **Portal client** : Gestion des moyens de paiement par le client
- **Coupons** : Codes promo Stripe

---

## Tests

### Cartes de test Stripe

| Numéro | Résultat |
|--------|----------|
| `4242 4242 4242 4242` | Paiement réussi |
| `4000 0000 0000 9995` | Paiement refusé |
| `4000 0025 0000 3155` | Authentification 3D Secure requise |

**Expiration :** N'importe quelle date future
**CVC :** N'importe quels 3 chiffres

---

## Fichiers créés/modifiés (résumé)

### Créés
- `app/Http/Controllers/StripeController.php`
- `app/Http/Controllers/StripeWebhookController.php`
- `database/migrations/2026_01_18_080411_add_stripe_columns_to_users_table.php`
- `database/migrations/2026_01_18_080436_create_subscriptions_table.php`
- `database/migrations/2026_01_18_080500_create_subscription_items_table.php`
- `docs/STRIPE_INTEGRATION.md` (ce fichier)

### Modifiés
- `composer.json`
- `app/Models/User.php`
- `app/Livewire/CheckoutForm.php`
- `bootstrap/app.php`
- `config/services.php`
- `routes/web.php`
- `.env.example`
- `resources/views/checkout/confirmation.blade.php`
