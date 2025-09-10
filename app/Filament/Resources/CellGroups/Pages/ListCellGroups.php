<?php

namespace App\Filament\Resources\CellGroups\Pages;

use App\Filament\Resources\CellGroups\CellGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCellGroups extends ListRecords
{
    protected static string $resource = CellGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Optimize queries with eager loading for better performance
     */
    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery()
            ->with([
                'info:cell_group_id,cell_group_idnum,day,time,location',
                'cellGroupType:id,name'
            ]);
    }
}
