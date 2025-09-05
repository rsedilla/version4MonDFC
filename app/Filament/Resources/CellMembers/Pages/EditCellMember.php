<?php

namespace App\Filament\Resources\CellMembers\Pages;

use App\Filament\Resources\CellMembers\CellMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCellMember extends EditRecord
{
    protected static string $resource = CellMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
