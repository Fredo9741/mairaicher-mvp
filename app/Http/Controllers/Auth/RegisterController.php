<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegistrationForm()
    {
        // Si déjà connecté, rediriger vers l'accueil
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth.register');
    }

    /**
     * Traiter l'inscription d'un nouveau customer
     */
    public function register(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        // Créer le nouvel utilisateur avec le rôle customer
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'email_verified_at' => now(), // Auto-vérifié pour simplifier
        ]);

        // Log pour debugging
        \Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);

        // Connecter automatiquement l'utilisateur
        Auth::login($user);

        // Rediriger vers l'accueil avec message de succès
        return redirect('/')
            ->with('success', 'Votre compte a été créé avec succès ! Bienvenue sur ' . config('app.name') . ' 🌿');
    }
}
