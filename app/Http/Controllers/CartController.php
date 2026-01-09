<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Bundle;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function addProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $quantity = (float) $validated['quantity'];
        $cart = session()->get('cart', []);
        $itemKey = 'product_' . $product->id;

        // Calculer la quantité totale demandée (panier + nouvelle quantité)
        $currentQuantity = isset($cart[$itemKey]) ? $cart[$itemKey]['quantity'] : 0;
        $totalQuantity = $currentQuantity + $quantity;

        // Vérifier le stock avec la quantité totale
        if (!$product->isAvailable($totalQuantity)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $product->getStockErrorMessage()
                ], 400);
            }
            return back()->with('error', $product->getStockErrorMessage());
        }

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] = $totalQuantity;
        } else {
            $cart[$itemKey] = [
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'price_cents' => $product->price_cents,
                'unit' => $product->unit,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        // Réponse JSON pour AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté au panier !',
                'cartCount' => count($cart),
                'product' => $product->name
            ]);
        }

        return back()->with('success', 'Produit ajouté au panier !');
    }

    public function addBundle(Request $request, Bundle $bundle)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = (int) $validated['quantity'];
        $cart = session()->get('cart', []);
        $itemKey = 'bundle_' . $bundle->id;

        // Calculer la quantité totale demandée (panier + nouvelle quantité)
        $currentQuantity = isset($cart[$itemKey]) ? $cart[$itemKey]['quantity'] : 0;
        $totalQuantity = $currentQuantity + $quantity;

        // Charger les produits avec leurs relations pour la vérification de stock
        $bundle->load('products');

        // Vérifier le stock avec la quantité totale de bundles
        if (!$bundle->isAvailable($totalQuantity)) {
            $errorMessage = $bundle->getStockErrorMessage($totalQuantity);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            return back()->with('error', $errorMessage);
        }

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] = $totalQuantity;
        } else {
            $cart[$itemKey] = [
                'type' => 'bundle',
                'id' => $bundle->id,
                'name' => $bundle->name,
                'price_cents' => $bundle->price_cents,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        // Réponse JSON pour AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Panier ajouté !',
                'cartCount' => count($cart),
                'product' => $bundle->name
            ]);
        }

        return back()->with('success', 'Panier ajouté au panier !');
    }

    public function updateQuantity(Request $request, string $itemKey)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$itemKey])) {
            if ($validated['quantity'] == 0) {
                unset($cart[$itemKey]);
            } else {
                $newQuantity = (float) $validated['quantity'];

                // Vérifier le stock pour les produits
                if ($cart[$itemKey]['type'] === 'product') {
                    $product = Product::find($cart[$itemKey]['id']);

                    if ($product && !$product->isAvailable($newQuantity)) {
                        return back()->with('error', $product->getStockErrorMessage());
                    }
                }

                // Vérifier le stock pour les bundles
                if ($cart[$itemKey]['type'] === 'bundle') {
                    $bundle = Bundle::with('products')->find($cart[$itemKey]['id']);

                    if ($bundle && !$bundle->isAvailable((int) $newQuantity)) {
                        return back()->with('error', $bundle->getStockErrorMessage((int) $newQuantity));
                    }
                }

                $cart[$itemKey]['quantity'] = $newQuantity;
            }

            session()->put('cart', $cart);
        }

        return back()->with('success', 'Panier mis à jour !');
    }

    public function remove(string $itemKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Article retiré du panier !');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Panier vidé !');
    }
}
