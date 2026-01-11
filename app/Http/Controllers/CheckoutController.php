<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickupSlot;
use App\Models\Product;
use App\Models\Bundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Votre panier est vide.');
        }

        $pickupSlots = PickupSlot::where('is_active', true)
        ->orderBy('name') // On remplace 'start_time' par 'name'
        ->get();

        // Récupérer l'utilisateur connecté pour pré-remplir le formulaire
        $user = auth()->user();

        return view('checkout.index', compact('cart', 'pickupSlots', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_slot_id' => 'required|exists:pickup_slots,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Votre panier est vide.');
        }

        try {
            DB::beginTransaction();

            // Vérifier le stock avant de créer la commande
            foreach ($cart as $item) {
                if ($item['type'] === 'product') {
                    $product = Product::find($item['id']);
                    if (!$product) {
                        throw new \Exception("Le produit '{$item['name']}' n'existe plus.");
                    }
                    if (!$product->isAvailable($item['quantity'])) {
                        throw new \Exception($product->getStockErrorMessage());
                    }
                }

                if ($item['type'] === 'bundle') {
                    $bundle = Bundle::with('products')->find($item['id']);
                    if (!$bundle) {
                        throw new \Exception("Le panier '{$item['name']}' n'existe plus.");
                    }
                    if (!$bundle->isAvailable($item['quantity'])) {
                        throw new \Exception($bundle->getStockErrorMessage($item['quantity']));
                    }
                }
            }

            // Calculer le total
            $totalCents = 0;
            foreach ($cart as $item) {
                $totalCents += $item['price_cents'] * $item['quantity'];
            }

            // Créer la commande
            $order = Order::create([
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'total_price_cents' => $totalCents,
                'pickup_date' => $validated['pickup_date'],
                'pickup_slot_id' => $validated['pickup_slot_id'],
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]);

            // Créer les items et réserver le stock
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item['type'],
                    'item_id' => $item['id'],
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price_cents' => $item['price_cents'],
                    'total_price_cents' => $item['price_cents'] * $item['quantity'],
                ]);

                // Réserver le stock pour les produits
                if ($item['type'] === 'product') {
                    $product = Product::find($item['id']);
                    if ($product) {
                        $product->decrementStock($item['quantity']);
                    }
                }

                // Réserver le stock pour les paniers
                if ($item['type'] === 'bundle') {
                    $bundle = Bundle::with('products')->find($item['id']);
                    if ($bundle) {
                        foreach ($bundle->products as $product) {
                            $quantityNeeded = $product->pivot->quantity_included * $item['quantity'];
                            $product->decrementStock($quantityNeeded);
                        }
                    }
                }
            }

            DB::commit();

            // Vider le panier
            session()->forget('cart');

            return redirect()->route('checkout.confirmation', $order)->with('success', 'Commande créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de commande: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()->with('error', 'Une erreur est survenue lors de la création de la commande: ' . $e->getMessage());
        }
    }

    public function confirmation(Order $order)
    {
        $order->load(['items', 'pickupSlot']);

        return view('checkout.confirmation', compact('order'));
    }
}
