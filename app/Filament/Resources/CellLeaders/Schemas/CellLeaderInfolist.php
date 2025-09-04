<?php

namespace App\Filament\Resources\CellLeaders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CellLeaderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('member_id')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('user_id')
                    ->numeric(),
            ]);
    }
}
