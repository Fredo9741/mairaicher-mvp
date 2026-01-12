<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Bundle;
use App\Models\HeroSection;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $hero = HeroSection::getActive();

        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        // Récupérer les paniers actifs avec leurs produits
        $bundles = Bundle::where('is_active', true)
            ->with(['products' => function ($query) {
                // S'assurer que les produits existent et sont actifs
                $query->where('is_active', true);
            }])
            ->get()
            ->filter(function ($bundle) {
                // Vérifier que le panier a encore des produits associés
                if ($bundle->products->isEmpty()) {
                    return false;
                }
                // Vérifier la disponibilité du panier
                return $bundle->isAvailable();
            });

        return view('home', compact('hero', 'products', 'bundles'));
    }

    public function showProduct(Product $product)
    {
        if (!$product->is_active || $product->stock <= 0) {
            abort(404);
        }

        return view('products.show', compact('product'));
    }

    public function showBundle(Bundle $bundle)
    {
        $bundle->load('products');

        if (!$bundle->is_active || !$bundle->isAvailable()) {
            abort(404);
        }

        return view('bundles.show', compact('bundle'));
    }
}
