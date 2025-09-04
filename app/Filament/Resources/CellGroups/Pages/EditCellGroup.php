<?php

namespace App\Filament\Resources\CellGroups\Pages;

use App\Filament\Resources\CellGroups\CellGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCellGroup extends EditRecord
{
    protected static string $resource = CellGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
