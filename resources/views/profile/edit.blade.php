@extends('layouts.app')

@section('title', 'Modifier mon profil')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('profile.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au profil
        </a>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 mb-8">Modifier mon profil</h1>

    <div class="space-y-6">
        <!-- Update Profile Info -->
        <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Informations personnelles</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nom complet</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Adresse email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('email') border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="+262 692 XX XX XX"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('phone') border-red-500 @enderror"
                    >
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Optionnel - Format recommandé : +262 692 XX XX XX</p>
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold transition-colors">
                    Mettre à jour mes informations
                </button>
            </form>
        </div>

        <!-- Update Password (only for non-OAuth users) -->
        @if(!$user->provider)
        <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Changer de mot de passe</h2>
            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe actuel</label>
                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('current_password') border-red-500 @enderror"
                    >
                    @error('current_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('password') border-red-500 @enderror"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimum 8 caractères</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le nouveau mot de passe</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold transition-colors">
                    Modifier mon mot de passe
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
