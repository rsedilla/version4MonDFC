<?php

namespace App\Filament\Resources\G12Leaders\Schemas;

use App\Models\Member;
use App\Traits\HasMemberSearch;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class G12LeaderForm
{
    use HasMemberSearch;
    
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::memberSelect()
                    ->required(),
                    
                TextInput::make('user_id')
                    ->numeric(),
            ]);
    }
}
