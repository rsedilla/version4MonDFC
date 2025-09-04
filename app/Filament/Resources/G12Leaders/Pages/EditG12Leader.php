<?php

namespace App\Filament\Resources\G12Leaders\Pages;

use App\Filament\Resources\G12Leaders\G12LeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditG12Leader extends EditRecord
{
    protected static string $resource = G12LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
