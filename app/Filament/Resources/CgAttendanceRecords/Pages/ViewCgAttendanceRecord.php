<?php

namespace App\Filament\Resources\CgAttendanceRecords\Pages;

use App\Filament\Resources\CgAttendanceRecords\CgAttendanceRecordResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCgAttendanceRecord extends ViewRecord
{
    protected static string $resource = CgAttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
