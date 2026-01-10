<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function index()
    {
        $user = Auth::user();

        // Récupérer les commandes de l'utilisateur avec les informations liées
        $orders = Order::where('email', $user->email)
            ->with(['items.product', 'items.bundle', 'pickupSlot'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.index', compact('user', 'orders'));
    }

    /**
     * Afficher le formulaire d'édition du profil
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Mettre à jour les informations du profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
        ]);

        $user->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Votre mot de passe a été modifié avec succès.');
    }
}
