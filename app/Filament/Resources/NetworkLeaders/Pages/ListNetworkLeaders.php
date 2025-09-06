<?php

namespace App\Filament\Resources\NetworkLeaders\Pages;

use App\Filament\Resources\NetworkLeaders\NetworkLeaderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNetworkLeaders extends ListRecords
{
    protected static string $resource = NetworkLeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
