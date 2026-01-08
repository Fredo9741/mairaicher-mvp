<?php

namespace App\Filament\Resources\HeroSectionResource\Pages;

use App\Filament\Resources\HeroSectionResource;
use App\Services\ImageOptimizer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHeroSection extends CreateRecord
{
    protected static string $resource = HeroSectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si une image est uploadée, l'optimiser
        if (isset($data['image']) && $data['image']) {
            try {
                $optimizer = app(ImageOptimizer::class);
                $file = $data['image'];

                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $optimizedPath = $optimizer->optimize(
                        $file,
                        disk: 'r2',
                        directory: 'hero',
                        maxWidth: 1920,
                        quality: 80
                    );

                    $data['image'] = $optimizedPath;
                }
            } catch (\Exception $e) {
                \Log::error('Hero image optimization failed: ' . $e->getMessage());
            }
        }

        return $data;
    }
}
