<?php

namespace App\Filament\Resources\NetworkLeaders\Schemas;

use App\Models\Member;
use App\Rules\UniqueMemberAssignment;
use App\Traits\HasMemberSearch;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NetworkLeaderForm
{
    use HasMemberSearch;
    
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::memberSelect()
                    ->rules([
                        fn ($record) => new UniqueMemberAssignment('Network Leaders', $record)
                    ])
                    ->required(),
                    
                TextInput::make('user_id')
                    ->numeric(),
            ]);
    }
}
