<?php

namespace App\Filament\Resources\Attenders\Pages;

use App\Filament\Resources\Attenders\AttenderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttender extends EditRecord
{
    protected static string $resource = AttenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
