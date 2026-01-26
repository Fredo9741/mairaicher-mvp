<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Crée une session Stripe Checkout pour une commande
     */
    public function checkout(Order $order)
    {
        // Vérifie que la commande appartient bien à l'utilisateur ou est en attente
        if ($order->status !== 'pending') {
            return redirect()->route('home')->with('error', 'Cette commande a déjà été traitée.');
        }

        try {
            // Prépare les line items pour Stripe
            $lineItems = [];

            foreach ($order->items as $item) {
                // Pour les produits au kg, on utilise le total (prix * quantité)
                // Pour les bundles, on utilise le prix unitaire * quantité commandée
                if ($item->item_type === 'product') {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => config('cashier.currency', 'eur'),
                            'product_data' => [
                                'name' => $item->item_name . ' (' . number_format($item->quantity, 2) . ' kg)',
                            ],
                            'unit_amount' => (int) $item->total_price_cents, // Prix total pour ce produit
                        ],
                        'quantity' => 1,
                    ];
                } else {
                    // Bundle
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => config('cashier.currency', 'eur'),
                            'product_data' => [
                                'name' => $item->item_name,
                                'description' => 'Panier composé',
                            ],
                            'unit_amount' => (int) $item->unit_price_cents,
                        ],
                        'quantity' => (int) $item->quantity,
                    ];
                }
            }

            // Crée la session Stripe Checkout
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel', ['order' => $order->id]),
                'customer_email' => $order->customer_email,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                'locale' => 'fr',
            ]);

            // Sauvegarde l'ID de la session Stripe sur la commande
            $order->update(['stripe_payment_intent_id' => $session->id]);

            Log::info('Stripe Checkout session created', [
                'order_id' => $order->id,
                'session_id' => $session->id,
            ]);

            // Redirige vers Stripe Checkout
            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Stripe Checkout error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('checkout.confirmation', $order)
                ->with('error', 'Erreur lors de la redirection vers le paiement. Veuillez réessayer.');
        }
    }

    /**
     * Page de succès après paiement Stripe
     */
    public function success(Order $order, Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId) {
            try {
                // Vérifie le statut de la session Stripe
                $session = StripeSession::retrieve($sessionId);

                if ($session->payment_status === 'paid') {
                    // Met à jour le statut de la commande
                    $order->update([
                        'status' => 'paid',
                        'stripe_payment_intent_id' => $session->payment_intent,
                    ]);

                    Log::info('Payment successful', [
                        'order_id' => $order->id,
                        'payment_intent' => $session->payment_intent,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error verifying Stripe session', [
                    'order_id' => $order->id,
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('checkout.confirmation', $order)
            ->with('success', 'Paiement effectué avec succès ! Votre commande est confirmée.');
    }

    /**
     * Page d'annulation du paiement
     */
    public function cancel(Order $order)
    {
        Log::info('Payment cancelled by user', ['order_id' => $order->id]);

        return redirect()->route('checkout.confirmation', $order)
            ->with('warning', 'Paiement annulé. Vous pouvez réessayer ou payer lors du retrait.');
    }
}
