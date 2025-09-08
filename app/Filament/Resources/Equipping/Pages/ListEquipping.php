<?php

namespace App\Filament\Resources\Equipping\Pages;

use App\Filament\Resources\Equipping\EquippingResource;
use Filament\Resources\Pages\ListRecords;

class ListEquipping extends ListRecords
{
    protected static string $resource = EquippingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
