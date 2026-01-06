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

        if (!$product->isAvailable($quantity)) {
            return back()->with('error', $product->getStockErrorMessage());
        }

        $cart = session()->get('cart', []);

        $itemKey = 'product_' . $product->id;

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $quantity;
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

        return back()->with('success', 'Produit ajouté au panier !');
    }

    public function addBundle(Request $request, Bundle $bundle)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = (int) $validated['quantity'];

        if (!$bundle->isAvailable()) {
            return back()->with('error', 'Ce panier n\'est plus disponible.');
        }

        $cart = session()->get('cart', []);

        $itemKey = 'bundle_' . $bundle->id;

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $quantity;
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
                $cart[$itemKey]['quantity'] = (float) $validated['quantity'];
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
