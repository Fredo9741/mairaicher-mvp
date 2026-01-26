<x-filament-panels::page>
    {{-- Loading Overlay --}}
    <div wire:loading.delay wire:target="pickupDate, pickupSlotId"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 dark:bg-gray-900/70">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl flex items-center gap-4">
            <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 dark:text-gray-200 font-medium">Chargement...</span>
        </div>
    </div>

    {{-- Filtres globaux --}}
    <div class="mb-6">
        <x-filament::section>
            <x-slot name="heading">Filtres</x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Date de livraison --}}
                <div>
                    <label for="pickupDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Date de livraison
                    </label>
                    <input
                        type="date"
                        id="pickupDate"
                        wire:model.live.debounce.300ms="pickupDate"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    />
                </div>

                {{-- Point de collecte --}}
                <div>
                    <label for="pickupSlotId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Point de collecte
                    </label>
                    <select
                        id="pickupSlotId"
                        wire:model.live="pickupSlotId"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="">Tous les points</option>
                        @foreach($this->getPickupSlotOptions() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Statistiques de récolte (Picking List) --}}
    @php
        $stats = $this->getPickingStats();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        {{-- Total commandes --}}
        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-full bg-primary-100 dark:bg-primary-900">
                    <x-heroicon-o-shopping-cart class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total commandes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['totalOrders'] }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Commandes payées --}}
        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-full bg-success-100 dark:bg-success-900">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Payées</p>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $stats['paidOrders'] }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Commandes en attente --}}
        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-full bg-warning-100 dark:bg-warning-900">
                    <x-heroicon-o-clock class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">En attente</p>
                    <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['pendingOrders'] }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Chiffre d'affaires --}}
        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-full bg-info-100 dark:bg-info-900">
                    <x-heroicon-o-currency-euro class="w-6 h-6 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Chiffre d'affaires</p>
                    <p class="text-2xl font-bold text-info-600 dark:text-info-400">{{ number_format($stats['totalRevenue'], 2, ',', ' ') }} &euro;</p>
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Picking List (Liste de récolte) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" id="picking-list">
        {{-- Produits à récolter --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-scissors class="w-5 h-5 text-green-600" />
                    <span>Produits à récolter</span>
                </div>
            </x-slot>

            @if($stats['products']->isEmpty())
                <p class="text-gray-500 dark:text-gray-400 text-sm italic">Aucun produit à récolter</p>
            @else
                <div class="space-y-2">
                    @foreach($stats['products'] as $product)
                        @php
                            $unit = $product->unit ?? 'kg';
                            if ($unit === 'kg') {
                                $qtyDisplay = number_format($product->total_quantity, 2, ',', ' ') . ' kg';
                            } else {
                                $qtyDisplay = (int) $product->total_quantity . ' pièce(s)';
                            }
                        @endphp
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $product->item_name }}</span>
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full font-bold">
                                {{ $qtyDisplay }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>

        {{-- Paniers à préparer --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-gift class="w-5 h-5 text-purple-600" />
                    <span>Paniers à préparer</span>
                </div>
            </x-slot>

            @if($stats['bundles']->isEmpty())
                <p class="text-gray-500 dark:text-gray-400 text-sm italic">Aucun panier à préparer</p>
            @else
                <div class="space-y-2">
                    @foreach($stats['bundles'] as $bundle)
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $bundle->item_name }}</span>
                            <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded-full font-bold">
                                {{ (int) $bundle->total_quantity }} panier(s)
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    </div>

    {{-- Légende des statuts --}}
    <div class="mb-4 flex flex-wrap items-center gap-4 text-sm">
        <span class="font-medium text-gray-600 dark:text-gray-400">Légende :</span>
        <div class="flex items-center gap-2">
            <span class="w-6 h-4 rounded" style="background-color: rgb(254 252 232); border-left: 4px solid rgb(251 191 36);"></span>
            <span class="text-gray-700 dark:text-gray-300">En attente de paiement</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-6 h-4 rounded" style="background-color: rgb(240 253 244); border-left: 4px solid rgb(34 197 94);"></span>
            <span class="text-gray-700 dark:text-gray-300">Payée (à préparer)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-6 h-4 rounded" style="background-color: rgb(240 249 255); border-left: 4px solid rgb(14 165 233);"></span>
            <span class="text-gray-700 dark:text-gray-300">Prête à livrer</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-6 h-4 rounded" style="background-color: rgb(243 244 246); border-left: 4px solid rgb(107 114 128);"></span>
            <span class="text-gray-700 dark:text-gray-300">Livrée</span>
        </div>
    </div>

    {{-- Table des livraisons --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-truck class="w-5 h-5 text-blue-600" />
                    <span>Livraisons du {{ \Carbon\Carbon::parse($this->pickupDate)->format('d/m/Y') }}</span>
                </div>
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    Cliquez sur une ligne pour voir les détails
                </span>
            </div>
        </x-slot>

        {{ $this->table }}
    </x-filament::section>

    {{-- Script pour l'impression --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('print-picking-list', () => {
                window.print();
            });
        });
    </script>
    @endpush

    {{-- Styles des statuts de commande --}}
    <style>
        /* Couleurs des lignes selon le statut */
        .fi-ta-row.status-pending {
            background-color: rgb(254 252 232) !important; /* yellow-50 */
            border-left: 4px solid rgb(251 191 36) !important; /* amber-400 */
        }
        .dark .fi-ta-row.status-pending {
            background-color: rgba(120, 53, 15, 0.3) !important; /* amber-900/30 */
        }

        .fi-ta-row.status-paid {
            background-color: rgb(240 253 244) !important; /* green-50 */
            border-left: 4px solid rgb(34 197 94) !important; /* green-500 */
        }
        .dark .fi-ta-row.status-paid {
            background-color: rgba(20, 83, 45, 0.3) !important; /* green-900/30 */
        }

        .fi-ta-row.status-ready {
            background-color: rgb(240 249 255) !important; /* sky-50 */
            border-left: 4px solid rgb(14 165 233) !important; /* sky-500 */
        }
        .dark .fi-ta-row.status-ready {
            background-color: rgba(12, 74, 110, 0.3) !important; /* sky-900/30 */
        }

        .fi-ta-row.status-completed {
            background-color: rgb(243 244 246) !important; /* gray-100 */
            border-left: 4px solid rgb(107 114 128) !important; /* gray-500 */
            opacity: 0.7;
        }
        .dark .fi-ta-row.status-completed {
            background-color: rgba(55, 65, 81, 0.3) !important; /* gray-700/30 */
        }

        /* Hover states */
        .fi-ta-row.status-pending:hover {
            background-color: rgb(254 249 195) !important; /* yellow-100 */
        }
        .fi-ta-row.status-paid:hover {
            background-color: rgb(220 252 231) !important; /* green-100 */
        }
        .fi-ta-row.status-ready:hover {
            background-color: rgb(224 242 254) !important; /* sky-100 */
        }
        .fi-ta-row.status-completed:hover {
            background-color: rgb(229 231 235) !important; /* gray-200 */
            opacity: 1;
        }

        @media print {
            /* Cache les éléments non nécessaires */
            nav, aside, .fi-sidebar, .fi-topbar, .fi-header-actions, button, .fi-btn {
                display: none !important;
            }

            /* Affiche tout le contenu */
            .fi-page {
                padding: 0 !important;
            }

            /* Améliore la lisibilité */
            body {
                font-size: 12pt;
            }

            /* Force les couleurs */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</x-filament-panels::page>
