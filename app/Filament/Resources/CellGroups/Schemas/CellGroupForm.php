<?php

namespace App\Filament\Resources\CellGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CellGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cell_group_type_id')
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('leader_id')
                    ->required()
                    ->numeric(),
                TextInput::make('leader_type')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
