<?php

namespace App\Filament\Resources\PickupSlotResource\Pages;

use App\Filament\Resources\PickupSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPickupSlot extends EditRecord
{
    protected static string $resource = PickupSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
