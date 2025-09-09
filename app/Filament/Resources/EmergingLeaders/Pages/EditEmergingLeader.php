<?php

namespace App\Filament\Resources\EmergingLeaders\Pages;

use App\Filament\Resources\EmergingLeaders\EmergingLeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEmergingLeader extends EditRecord
{
    protected static string $resource = EmergingLeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
