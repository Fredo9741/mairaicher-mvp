<div class="space-y-6">
    {{-- En-tête avec statut --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'ready' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    'completed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                ];
                $statusLabels = [
                    'pending' => 'En attente de paiement',
                    'paid' => 'Payée',
                    'ready' => 'Prête à retirer',
                    'completed' => 'Terminée',
                ];
            @endphp
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                {{ $statusLabels[$order->status] ?? $order->status }}
            </span>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            Créée le {{ $order->created_at->format('d/m/Y à H:i') }}
        </span>
    </div>

    {{-- Informations client --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
            <x-heroicon-o-user class="w-4 h-4 inline mr-1" />
            Client
        </h4>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $order->customer_email }}</p>
            </div>
            <a href="tel:{{ $order->customer_phone }}"
               class="inline-flex items-center justify-center gap-3 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors shadow-lg hover:shadow-xl font-semibold text-lg">
                <x-heroicon-s-phone class="w-6 h-6" />
                <span>Appeler {{ $order->customer_phone }}</span>
            </a>
        </div>
    </div>

    {{-- Informations de retrait --}}
    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
        <h4 class="text-sm font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-3">
            <x-heroicon-o-map-pin class="w-4 h-4 inline mr-1" />
            Retrait
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Point de collecte</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $order->pickupSlot?->name ?? 'Non défini' }}</p>
                @if($order->pickupSlot?->address)
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $order->pickupSlot->address }}</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Date et heure</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                    {{ \Carbon\Carbon::parse($order->pickup_date)->format('l d F Y') }}
                </p>
                @if($order->pickup_time_slot)
                    @php
                        $parts = explode('-', $order->pickup_time_slot);
                        $timeFormatted = count($parts) === 2
                            ? substr($parts[0], 0, 5) . ' - ' . substr($parts[1], 0, 5)
                            : $order->pickup_time_slot;
                    @endphp
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $timeFormatted }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Contenu de la commande --}}
    <div>
        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
            <x-heroicon-o-shopping-bag class="w-4 h-4 inline mr-1" />
            Articles ({{ $order->items->count() }})
        </h4>
        <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Article</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Quantité</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Prix</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($order->items as $item)
                        @php
                            $product = $item->item_type === 'product' ? \App\Models\Product::find($item->item_id) : null;
                            $unit = $product?->unit ?? 'piece';

                            if ($item->item_type === 'bundle') {
                                $qtyDisplay = (int) $item->quantity . ' panier(s)';
                            } elseif ($unit === 'kg') {
                                $qtyDisplay = number_format($item->quantity, 2, ',', '') . ' kg';
                            } else {
                                $qtyDisplay = (int) $item->quantity . ' pièce(s)';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($item->item_type === 'bundle')
                                        <span class="px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300 rounded">Panier</span>
                                    @endif
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $item->item_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                {{ $qtyDisplay }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                {{ number_format($item->total_price_cents / 100, 2, ',', ' ') }} €
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">Total</td>
                        <td class="px-4 py-3 text-right font-bold text-lg text-green-600 dark:text-green-400">
                            {{ number_format($order->total_price_cents / 100, 2, ',', ' ') }} €
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Notes --}}
    @if($order->notes)
        <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-4">
            <h4 class="text-sm font-semibold text-yellow-700 dark:text-yellow-400 uppercase tracking-wide mb-2">
                <x-heroicon-o-exclamation-triangle class="w-4 h-4 inline mr-1" />
                Notes
            </h4>
            <p class="text-gray-700 dark:text-gray-300">{{ $order->notes }}</p>
        </div>
    @endif
</div>
