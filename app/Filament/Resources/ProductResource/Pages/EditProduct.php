<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Services\ImageOptimizer;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si une nouvelle image est uploadée, l'optimiser
        if (isset($data['image']) && $data['image']) {
            try {
                $optimizer = app(ImageOptimizer::class);
                $file = $data['image'];

                // L'image est un UploadedFile temporaire à ce stade
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $optimizedPath = $optimizer->optimize(
                        $file,
                        disk: 'r2',
                        directory: 'products',
                        maxWidth: 1920,
                        quality: 80
                    );

                    $data['image'] = $optimizedPath;
                }
            } catch (\Exception $e) {
                \Log::error('Product image optimization failed: ' . $e->getMessage());
                // Continue sans optimisation en cas d'erreur
            }
        }

        return $data;
    }

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
