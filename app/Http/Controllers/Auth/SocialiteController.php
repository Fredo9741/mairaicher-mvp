<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from provider.
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Échec de l\'authentification avec ' . ucfirst($provider));
        }

        // Find or create user
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Update provider info if user exists
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);
        } else {
            // Create new user with customer role
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(24)), // Random password for social users
                'role' => 'customer',
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'email_verified_at' => now(),
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Redirect based on role
        if ($user->canAccessPanel(null)) {
            return redirect('/admin');
        }

        return redirect('/')->with('success', 'Connexion réussie !');
    }

    /**
     * Validate the provider.
     */
    protected function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }
    }
}
