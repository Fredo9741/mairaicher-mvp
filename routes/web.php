<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\LoginController;

// Universal Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Social Authentication
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');

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
