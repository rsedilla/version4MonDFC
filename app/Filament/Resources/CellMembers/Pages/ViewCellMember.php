<?php

namespace App\Filament\Resources\CellMembers\Pages;

use App\Filament\Resources\CellMembers\CellMemberResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCellMember extends ViewRecord
{
    protected static string $resource = CellMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
