<?php

namespace App\Filament\Resources\EmergingLeaders\Pages;

use App\Filament\Resources\EmergingLeaders\EmergingLeaderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmergingLeader extends ViewRecord
{
    protected static string $resource = EmergingLeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
