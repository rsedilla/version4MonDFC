<?php

namespace App\Filament\Resources\Attenders\Pages;

use App\Filament\Resources\Attenders\AttenderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttender extends EditRecord
{
    protected static string $resource = AttenderResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->member_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
