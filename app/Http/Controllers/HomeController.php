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

        $bundles = Bundle::where('is_active', true)
            ->with('products')
            ->get()
            ->filter(function ($bundle) {
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
