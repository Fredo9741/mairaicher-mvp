<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Navigation vers le produit précédent
        $previousProduct = Product::where('id', '<', $this->record->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($previousProduct) {
            $actions[] = Actions\Action::make('previous')
                ->label('← Produit précédent')
                ->url(ProductResource::getUrl('edit', ['record' => $previousProduct]))
                ->color('gray')
                ->icon('heroicon-o-arrow-left');
        }

        // Navigation vers le produit suivant
        $nextProduct = Product::where('id', '>', $this->record->id)
            ->orderBy('id', 'asc')
            ->first();

        if ($nextProduct) {
            $actions[] = Actions\Action::make('next')
                ->label('Produit suivant →')
                ->url(ProductResource::getUrl('edit', ['record' => $nextProduct]))
                ->color('gray')
                ->icon('heroicon-o-arrow-right')
                ->iconPosition('after');
        }

        $actions[] = Actions\DeleteAction::make();

        return $actions;
    }
}
