<?php

namespace App\Filament\Resources\CgAttendanceRecords\Pages;

use App\Filament\Resources\CgAttendanceRecords\CgAttendanceRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCgAttendanceRecords extends ListRecords
{
    protected static string $resource = CgAttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
