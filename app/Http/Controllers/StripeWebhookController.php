<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Gère les webhooks Stripe
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            // Vérifie la signature du webhook
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe Webhook received', [
            'type' => $event->type,
            'id' => $event->id,
        ]);

        // Gère les différents types d'événements
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            default:
                Log::info('Stripe Webhook: Unhandled event type', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Gère l'événement checkout.session.completed
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            Log::error('Stripe Webhook: No order_id in session metadata', [
                'session_id' => $session->id,
            ]);
            return;
        }

        $order = Order::find($orderId);

        if (!$order) {
            Log::error('Stripe Webhook: Order not found', ['order_id' => $orderId]);
            return;
        }

        // Met à jour la commande si le paiement est réussi
        if ($session->payment_status === 'paid') {
            $order->update([
                'status' => 'paid',
                'stripe_payment_intent_id' => $session->payment_intent,
            ]);

            Log::info('Order marked as paid via webhook', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent' => $session->payment_intent,
            ]);

            // Ici tu peux ajouter l'envoi d'email de confirmation
            // Mail::to($order->customer_email)->send(new OrderConfirmation($order));
        }
    }

    /**
     * Gère l'événement payment_intent.succeeded
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Cherche la commande par payment_intent_id
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if (!$order) {
            // Peut arriver si la commande a été créée avec le session_id
            Log::info('Stripe Webhook: No order found for payment_intent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        // S'assure que la commande est marquée comme payée
        if ($order->status !== 'paid') {
            $order->update(['status' => 'paid']);

            Log::info('Order status updated to paid', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        }
    }

    /**
     * Gère l'événement payment_intent.payment_failed
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            Log::warning('Payment failed for order', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
                'error' => $paymentIntent->last_payment_error->message ?? 'Unknown error',
            ]);

            // On ne change pas le statut, la commande reste en "pending"
            // Le client pourra réessayer
        }
    }
}
