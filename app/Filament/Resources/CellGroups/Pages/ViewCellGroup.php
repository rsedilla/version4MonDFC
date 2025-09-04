<?php

namespace App\Filament\Resources\CellGroups\Pages;

use App\Filament\Resources\CellGroups\CellGroupResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCellGroup extends ViewRecord
{
    protected static string $resource = CellGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
