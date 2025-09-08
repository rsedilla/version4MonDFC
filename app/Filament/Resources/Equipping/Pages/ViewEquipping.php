<?php

namespace App\Filament\Resources\Equipping\Pages;

use App\Filament\Resources\Equipping\EquippingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEquipping extends ViewRecord
{
    protected static string $resource = EquippingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
