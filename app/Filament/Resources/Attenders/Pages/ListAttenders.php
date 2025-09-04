<?php

namespace App\Filament\Resources\Attenders\Pages;

use App\Filament\Resources\Attenders\AttenderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttenders extends ListRecords
{
    protected static string $resource = AttenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
