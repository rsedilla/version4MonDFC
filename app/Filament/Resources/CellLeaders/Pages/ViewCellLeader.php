<?php

namespace App\Filament\Resources\CellLeaders\Pages;

use App\Filament\Resources\CellLeaders\CellLeaderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCellLeader extends ViewRecord
{
    protected static string $resource = CellLeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
