<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Optimize queries with eager loading
     */
    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery()
            ->with([
                'trainingTypes:id,name',
                'directLeader.member:id,first_name,middle_name,last_name'
            ]);
    }
}
