<?php

namespace App\Filament\Resources\CellGroups\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CellGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('cell_group_type_id')
                    ->numeric(),
                TextEntry::make('name'),
                TextEntry::make('leader_id')
                    ->numeric(),
                TextEntry::make('leader_type'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
