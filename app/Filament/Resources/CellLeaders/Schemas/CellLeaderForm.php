<?php

namespace App\Filament\Resources\CellLeaders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CellLeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('member_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->numeric(),
            ]);
    }
}
