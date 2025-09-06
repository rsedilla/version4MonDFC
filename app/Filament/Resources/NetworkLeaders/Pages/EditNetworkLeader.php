<?php

namespace App\Filament\Resources\NetworkLeaders\Pages;

use App\Filament\Resources\NetworkLeaders\NetworkLeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNetworkLeader extends EditRecord
{
    protected static string $resource = NetworkLeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
