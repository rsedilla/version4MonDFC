<?php

namespace App\Filament\Resources\CgAttendanceRecords\Pages;

use App\Filament\Resources\CgAttendanceRecords\CgAttendanceRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ListCgAttendanceRecords extends ListRecords
{
    protected static string $resource = CgAttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        // Default behavior - show attendance records
        return parent::getTableQuery();
    }
}
