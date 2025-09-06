<?php

namespace App\Filament\Resources\CellLeaders\Schemas;

use App\Models\Member;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CellLeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->label('Member')
                    ->options(function () {
                        return Member::all()->mapWithKeys(function ($member) {
                            return [$member->id => $member->full_name];
                        });
                    })
                    ->searchable()
                    ->required(),
                    
                TextInput::make('user_id')
                    ->numeric(),
            ]);
    }
}
