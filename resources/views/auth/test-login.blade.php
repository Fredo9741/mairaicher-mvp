<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Connexion Sociale</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Test - Authentification Multi-Utilisateurs
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Domaine des Papangues
                </p>
            </div>

            @if(auth()->check())
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-green-900">Connecté avec succès!</h3>
                    </div>

                    <div class="space-y-3 text-sm">
                        <p><strong class="text-gray-700">Nom:</strong> <span class="text-gray-900">{{ auth()->user()->name }}</span></p>
                        <p><strong class="text-gray-700">Email:</strong> <span class="text-gray-900">{{ auth()->user()->email }}</span></p>
                        <p><strong class="text-gray-700">Rôle:</strong>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if(auth()->user()->role === 'developer') bg-purple-100 text-purple-800
                                @elseif(auth()->user()->role === 'maraicher') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </p>
                        @if(auth()->user()->provider)
                            <p><strong class="text-gray-700">Fournisseur:</strong> <span class="text-gray-900">{{ ucfirst(auth()->user()->provider) }}</span></p>
                        @endif
                        <p><strong class="text-gray-700">Accès admin:</strong>
                            <span class="text-gray-900">{{ auth()->user()->canAccessPanel(null) ? 'Oui' : 'Non' }}</span>
                        </p>
                    </div>

                    <div class="mt-4 space-y-2">
                        @if(auth()->user()->canAccessPanel(null))
                            <a href="/admin" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Accéder au panneau admin
                            </a>
                        @endif
                        <a href="/" class="block w-full text-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                            Retour à l'accueil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Se déconnecter
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white shadow-xl rounded-lg p-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Comptes de test classiques:</h3>
                        <div class="space-y-2 text-sm bg-gray-50 p-4 rounded-lg">
                            <div>
                                <p class="font-medium text-purple-700">👨‍💻 Developer (Super Admin)</p>
                                <p class="text-gray-600">developer@maraicher.test / Dev@2026!Secure</p>
                            </div>
                            <div>
                                <p class="font-medium text-blue-700">👨‍🌾 Maraicher (Admin)</p>
                                <p class="text-gray-600">admin@maraicher.test / Admin@2026!Secure</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-700">👤 Customer</p>
                                <p class="text-gray-600">customer@maraicher.test / Customer@2026!Secure</p>
                            </div>
                        </div>
                    </div>

                    <a href="/admin/login" class="block w-full text-center px-4 py-2 mb-4 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                        Se connecter (classique)
                    </a>

                    @include('auth.social-buttons')

                    <div class="mt-6 text-center">
                        <a href="/" class="text-sm text-emerald-600 hover:text-emerald-500">
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
