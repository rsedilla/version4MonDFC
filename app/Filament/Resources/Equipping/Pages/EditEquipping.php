<?php

namespace App\Filament\Resources\Equipping\Pages;

use App\Filament\Resources\Equipping\EquippingResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEquipping extends EditRecord
{
    protected static string $resource = EquippingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
