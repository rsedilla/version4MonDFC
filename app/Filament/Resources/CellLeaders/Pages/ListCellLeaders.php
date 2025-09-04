<?php

namespace App\Filament\Resources\CellLeaders\Pages;

use App\Filament\Resources\CellLeaders\CellLeaderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCellLeaders extends ListRecords
{
    protected static string $resource = CellLeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
