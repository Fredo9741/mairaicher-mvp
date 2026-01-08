<?php

namespace App\Filament\Resources\BundleResource\Pages;

use App\Filament\Resources\BundleResource;
use App\Models\Bundle;
use App\Services\ImageOptimizer;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBundle extends EditRecord
{
    protected static string $resource = BundleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si une nouvelle image est uploadée, l'optimiser
        if (isset($data['image']) && $data['image']) {
            try {
                $optimizer = app(ImageOptimizer::class);
                $file = $data['image'];

                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $optimizedPath = $optimizer->optimize(
                        $file,
                        disk: 'r2',
                        directory: 'bundles',
                        maxWidth: 1920,
                        quality: 80
                    );

                    $data['image'] = $optimizedPath;
                }
            } catch (\Exception $e) {
                \Log::error('Bundle image optimization failed: ' . $e->getMessage());
            }
        }

        return $data;
    }

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
