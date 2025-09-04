<?php

namespace App\Filament\Resources\G12Leaders\Pages;

use App\Filament\Resources\G12Leaders\G12LeaderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewG12Leader extends ViewRecord
{
    protected static string $resource = G12LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
