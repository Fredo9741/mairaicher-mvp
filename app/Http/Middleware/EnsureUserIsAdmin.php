<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * Vérifie que l'utilisateur authentifié a un rôle admin (developer ou maraicher).
     * Si non, redirige vers la page d'accueil avec un message d'erreur.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur a un rôle admin
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Accès refusé. Cette section est réservée aux administrateurs.');
        }

        return $next($request);
    }
}
