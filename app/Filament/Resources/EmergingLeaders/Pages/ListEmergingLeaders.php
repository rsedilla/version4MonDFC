<?php

namespace App\Filament\Resources\EmergingLeaders\Pages;

use App\Filament\Resources\EmergingLeaders\EmergingLeaderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmergingLeaders extends ListRecords
{
    protected static string $resource = EmergingLeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
