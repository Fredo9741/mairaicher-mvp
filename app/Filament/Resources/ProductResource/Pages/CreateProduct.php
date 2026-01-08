<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\ImageOptimizer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si une image est uploadée, l'optimiser
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
}
