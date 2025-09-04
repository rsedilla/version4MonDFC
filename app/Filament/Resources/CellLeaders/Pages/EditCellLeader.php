<?php

namespace App\Filament\Resources\CellLeaders\Pages;

use App\Filament\Resources\CellLeaders\CellLeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCellLeader extends EditRecord
{
    protected static string $resource = CellLeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
