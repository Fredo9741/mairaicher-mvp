<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\UniversalLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;

// ============================================
// AUTHENTICATION UNIVERSELLE
// ============================================
// Une seule page de login pour tous les utilisateurs
// Redirection automatique selon le rôle après authentification
Route::get('/login', [UniversalLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UniversalLoginController::class, 'login'])->name('login.post');
Route::post('/logout', [UniversalLoginController::class, 'logout'])->name('logout');

// Inscription
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// OAuth Social Authentication (Google & Facebook)
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');

// ============================================
// PROFIL UTILISATEUR (Authentification requise)
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profil/modifier', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profil/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

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
