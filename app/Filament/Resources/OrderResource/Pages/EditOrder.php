<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Navigation vers la commande précédente
        $previousOrder = Order::where('id', '<', $this->record->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($previousOrder) {
            $actions[] = Actions\Action::make('previous')
                ->label('← Commande précédente')
                ->url(OrderResource::getUrl('edit', ['record' => $previousOrder]))
                ->color('gray')
                ->icon('heroicon-o-arrow-left');
        }

        // Navigation vers la commande suivante
        $nextOrder = Order::where('id', '>', $this->record->id)
            ->orderBy('id', 'asc')
            ->first();

        if ($nextOrder) {
            $actions[] = Actions\Action::make('next')
                ->label('Commande suivante →')
                ->url(OrderResource::getUrl('edit', ['record' => $nextOrder]))
                ->color('gray')
                ->icon('heroicon-o-arrow-right')
                ->iconPosition('after');
        }

        $actions[] = Actions\DeleteAction::make();

        return $actions;
    }
}
