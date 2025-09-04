<?php

namespace App\Filament\Resources\G12Leaders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class G12LeaderForm
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
