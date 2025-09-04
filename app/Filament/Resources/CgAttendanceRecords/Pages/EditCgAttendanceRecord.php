<?php

namespace App\Filament\Resources\CgAttendanceRecords\Pages;

use App\Filament\Resources\CgAttendanceRecords\CgAttendanceRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCgAttendanceRecord extends EditRecord
{
    protected static string $resource = CgAttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
