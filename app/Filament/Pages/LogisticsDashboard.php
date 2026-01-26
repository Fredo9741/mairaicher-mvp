<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickupSlot;
use App\Models\Product;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Grouping\Group;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Carbon\Carbon;

class LogisticsDashboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Logistique';

    protected static ?string $title = 'Tableau de Bord Logistique';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.logistics-dashboard';

    #[Url]
    public ?string $pickupDate = null;

    #[Url]
    public ?string $pickupSlotId = null;

    public function mount(): void
    {
        $this->pickupDate = $this->pickupDate ?? now()->format('Y-m-d');
    }

    /**
     * Met à jour la date et rafraîchit le tableau
     */
    public function updatedPickupDate(): void
    {
        $this->resetTable();
    }

    /**
     * Met à jour le point de collecte et rafraîchit le tableau
     */
    public function updatedPickupSlotId(): void
    {
        $this->resetTable();
    }

    /**
     * Retourne les options de points de collecte
     */
    public function getPickupSlotOptions(): array
    {
        return PickupSlot::where('is_active', true)->pluck('name', 'id')->toArray();
    }

    /**
     * Récupère les statistiques de récolte (picking list)
     */
    public function getPickingStats(): array
    {
        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['pending', 'paid', 'ready'])
            ->whereDate('orders.pickup_date', $this->pickupDate);

        if ($this->pickupSlotId) {
            $query->where('orders.pickup_slot_id', $this->pickupSlotId);
        }

        // Produits groupés par nom avec leur unité
        $products = (clone $query)
            ->where('order_items.item_type', 'product')
            ->leftJoin('products', 'order_items.item_id', '=', 'products.id')
            ->select(
                'order_items.item_name',
                'products.unit',
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->groupBy('order_items.item_name', 'products.unit')
            ->orderBy('order_items.item_name')
            ->get();

        // Paniers groupés par nom
        $bundles = (clone $query)
            ->where('order_items.item_type', 'bundle')
            ->select(
                'order_items.item_name',
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->groupBy('order_items.item_name')
            ->orderBy('order_items.item_name')
            ->get();

        // Statistiques globales
        $ordersQuery = Order::query()
            ->whereIn('status', ['pending', 'paid', 'ready'])
            ->whereDate('pickup_date', $this->pickupDate);

        if ($this->pickupSlotId) {
            $ordersQuery->where('pickup_slot_id', $this->pickupSlotId);
        }

        $totalOrders = $ordersQuery->count();
        $paidOrders = (clone $ordersQuery)->where('status', 'paid')->count();
        $pendingOrders = (clone $ordersQuery)->where('status', 'pending')->count();
        $totalRevenue = $ordersQuery->sum('total_price_cents') / 100;

        return [
            'products' => $products,
            'bundles' => $bundles,
            'totalOrders' => $totalOrders,
            'paidOrders' => $paidOrders,
            'pendingOrders' => $pendingOrders,
            'totalRevenue' => $totalRevenue,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->whereIn('status', ['pending', 'paid', 'ready', 'completed'])
                    ->whereDate('pickup_date', $this->pickupDate)
                    ->when($this->pickupSlotId, fn ($q) => $q->where('pickup_slot_id', $this->pickupSlotId))
                    ->with(['items', 'pickupSlot'])
            )
            ->groups([
                Group::make('pickupSlot.name')
                    ->label('Point de collecte')
                    ->collapsible(),
            ])
            ->defaultGroup('pickupSlot.name')
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer_phone')
                    ->label('Téléphone')
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                TextColumn::make('items_summary')
                    ->label('Contenu')
                    ->state(function (Order $record): string {
                        return $record->items->map(function ($item) {
                            if ($item->item_type === 'bundle') {
                                return (int) $item->quantity . 'x ' . $item->item_name;
                            }
                            // Récupérer l'unité du produit
                            $product = Product::find($item->item_id);
                            $unit = $product?->unit ?? 'kg';
                            if ($unit === 'kg') {
                                return number_format($item->quantity, 1, ',', '') . ' kg ' . $item->item_name;
                            }
                            return (int) $item->quantity . ' ' . $item->item_name;
                        })->join(', ');
                    })
                    ->wrap()
                    ->limit(50),

                TextColumn::make('total_price_cents')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', ' ') . ' €')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'ready' => 'info',
                        'completed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'En attente',
                        'paid' => 'Payée',
                        'ready' => 'Prête à livrer',
                        'completed' => 'Livrée',
                        default => $state,
                    }),

                TextColumn::make('pickup_time_slot')
                    ->label('Horaire')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $parts = explode('-', $state);
                        if (count($parts) !== 2) return $state;
                        return substr($parts[0], 0, 5) . '-' . substr($parts[1], 0, 5);
                    }),
            ])
            ->recordAction('preview')
            ->recordUrl(null)
            ->actions([
                // Action cachée pour le clic sur la ligne
                Action::make('preview')
                    ->hiddenLabel()
                    ->hidden()
                    ->modalHeading(fn (Order $record) => "Commande {$record->order_number}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->modalWidth('lg')
                    ->modalContent(fn (Order $record) => view('filament.pages.partials.order-preview', [
                        'order' => $record->load(['items', 'pickupSlot']),
                    ])),

                // Marquer comme payé (pour paiement sur place)
                Action::make('markPaid')
                    ->label('Encaisser')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmer le paiement')
                    ->modalDescription('Marquer cette commande comme payée (paiement sur place) ?')
                    ->action(fn (Order $record) => $record->update(['status' => 'paid'])),

                // Marquer comme prête
                Action::make('markReady')
                    ->label('Prête')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->visible(fn (Order $record) => $record->status === 'paid')
                    ->action(fn (Order $record) => $record->update(['status' => 'ready'])),

                // Marquer comme livrée
                Action::make('markCompleted')
                    ->label('Livrée')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Order $record) => in_array($record->status, ['paid', 'ready']))
                    ->requiresConfirmation()
                    ->modalHeading('Confirmer la livraison')
                    ->modalDescription('Le client a bien récupéré sa commande ?')
                    ->action(fn (Order $record) => $record->update(['status' => 'completed'])),
            ])
            ->bulkActions([
                BulkAction::make('markAsReady')
                    ->label('Marquer comme Prêtes')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'ready']))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('markAsCompleted')
                    ->label('Marquer comme Livrées')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'completed']))
                    ->deselectRecordsAfterCompletion(),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Exporter Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn () => $this->exportToExcel()),

                Action::make('exportPdf')
                    ->label('Exporter PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(fn () => $this->exportToPdf()),

                Action::make('print')
                    ->label('Imprimer')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->action(fn () => $this->dispatch('print-picking-list')),
            ])
            ->emptyStateHeading('Aucune commande')
            ->emptyStateDescription('Aucune commande à livrer pour cette date.')
            ->emptyStateIcon('heroicon-o-truck')
            ->recordClasses(fn (Order $record) => match($record->status) {
                'pending' => 'status-pending',
                'paid' => 'status-paid',
                'ready' => 'status-ready',
                'completed' => 'status-completed',
                default => '',
            });
    }

    public function exportToExcel()
    {
        $orders = $this->getFilteredOrders();

        $filename = 'livraisons_' . $this->pickupDate . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            // BOM pour Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // En-têtes
            fputcsv($file, ['Point de collecte', 'Client', 'Téléphone', 'Contenu', 'Montant', 'Statut', 'Horaire'], ';');

            foreach ($orders as $order) {
                $content = $order->items->map(function ($item) {
                    $qty = $item->item_type === 'product'
                        ? number_format($item->quantity, 1) . 'kg'
                        : (int) $item->quantity . 'x';
                    return "{$qty} {$item->item_name}";
                })->join(' | ');

                $status = match($order->status) {
                    'pending' => 'En attente',
                    'paid' => 'Payée',
                    'ready' => 'Prête',
                    default => $order->status,
                };

                fputcsv($file, [
                    $order->pickupSlot->name ?? 'N/A',
                    $order->customer_name,
                    $order->customer_phone,
                    $content,
                    number_format($order->total_price_cents / 100, 2, ',', ' ') . ' €',
                    $status,
                    $order->pickup_time_slot ? substr($order->pickup_time_slot, 0, 5) . '-' . substr(explode('-', $order->pickup_time_slot)[1] ?? '', 0, 5) : '-',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportToPdf()
    {
        // Pour l'instant, on génère une version imprimable
        // Tu peux intégrer dompdf ou snappy plus tard
        $this->dispatch('print-picking-list');
    }

    protected function getFilteredOrders()
    {
        return Order::query()
            ->whereIn('status', ['pending', 'paid', 'ready'])
            ->whereDate('pickup_date', $this->pickupDate)
            ->when($this->pickupSlotId, fn ($q) => $q->where('pickup_slot_id', $this->pickupSlotId))
            ->with(['items', 'pickupSlot'])
            ->orderBy('pickup_slot_id')
            ->orderBy('customer_name')
            ->get();
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
