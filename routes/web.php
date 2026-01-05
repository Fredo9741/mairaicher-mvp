<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// Authentification - Redirection vers Filament
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/register', function () {
    return redirect('/admin/register');
})->name('register');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Produits
Route::get('/produits/{product:slug}', [HomeController::class, 'showProduct'])->name('products.show');

// Paniers de saison
Route::get('/paniers/{bundle:slug}', [HomeController::class, 'showBundle'])->name('bundles.show');

// Panier d'achat
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/produit/{product}', [CartController::class, 'addProduct'])->name('cart.add.product');
Route::post('/panier/bundle/{bundle}', [CartController::class, 'addBundle'])->name('cart.add.bundle');
Route::patch('/panier/{itemKey}', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::delete('/panier/{itemKey}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/panier', [CartController::class, 'clear'])->name('cart.clear');

// Checkout
Route::get('/commander', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/commander', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/commande/{order}/confirmation', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
