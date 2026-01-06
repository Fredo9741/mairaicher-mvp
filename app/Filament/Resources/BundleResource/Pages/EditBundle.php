<?php

namespace App\Filament\Resources\BundleResource\Pages;

use App\Filament\Resources\BundleResource;
use App\Models\Bundle;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBundle extends EditRecord
{
    protected static string $resource = BundleResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Navigation vers le panier précédent
        $previousBundle = Bundle::where('id', '<', $this->record->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($previousBundle) {
            $actions[] = Actions\Action::make('previous')
                ->label('← Panier précédent')
                ->url(BundleResource::getUrl('edit', ['record' => $previousBundle]))
                ->color('gray')
                ->icon('heroicon-o-arrow-left');
        }

        // Navigation vers le panier suivant
        $nextBundle = Bundle::where('id', '>', $this->record->id)
            ->orderBy('id', 'asc')
            ->first();

        if ($nextBundle) {
            $actions[] = Actions\Action::make('next')
                ->label('Panier suivant →')
                ->url(BundleResource::getUrl('edit', ['record' => $nextBundle]))
                ->color('gray')
                ->icon('heroicon-o-arrow-right')
                ->iconPosition('after');
        }

        $actions[] = Actions\DeleteAction::make();

        return $actions;
    }
}
