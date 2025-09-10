<?php

namespace App\Filament\Resources\Attenders\Pages;

use App\Filament\Resources\Attenders\AttenderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAttenders extends ListRecords
{
    protected static string $resource = AttenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Optimize queries with eager loading for member and consolidator relationships
     */
    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery()
            ->with([
                'member:id,first_name,middle_name,last_name',
                'consolidator:id,first_name,middle_name,last_name'
            ]);
    }
}
