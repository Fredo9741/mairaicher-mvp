<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Contrôleur de connexion universel
 *
 * Gère l'authentification pour tous les types d'utilisateurs
 * et redirige automatiquement selon leur rôle.
 */
class UniversalLoginController extends Controller
{
    /**
     * Afficher le formulaire de connexion universel
     */
    public function showLoginForm()
    {
        // Si déjà connecté, rediriger selon le rôle
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Traiter la demande de connexion
     *
     * Authentifie l'utilisateur et le redirige selon son rôle :
     * - Admin (developer/maraicher) -> /admin
     * - Customer -> /
     */
    public function login(Request $request)
    {
        // Validation des champs
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Tentative d'authentification
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Log pour debugging (optionnel)
            \Log::info('User logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);

            // Redirection intelligente basée sur le rôle
            return $this->redirectBasedOnRole($user);
        }

        // Échec de l'authentification
        throw ValidationException::withMessages([
            'email' => ['Les identifiants fournis ne correspondent pas à nos enregistrements.'],
        ]);
    }

    /**
     * Rediriger l'utilisateur en fonction de son rôle
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole($user)
    {
        // Admin (developer ou maraicher) -> Panel Filament
        if ($user->isAdmin()) {
            return redirect()->intended('/admin');
        }

        // Customer -> Page d'accueil
        return redirect()->intended('/');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $wasAdmin = $user ? $user->isAdmin() : false;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirection différenciée selon le type d'utilisateur déconnecté
        if ($wasAdmin) {
            return redirect('/login')->with('success', 'Vous avez été déconnecté avec succès.');
        }

        return redirect('/')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
