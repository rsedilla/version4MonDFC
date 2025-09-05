<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('leader_id')
                    ->numeric(),
                \Filament\Forms\Components\Select::make('leader_type')
                    ->label('Leader Type')
                    ->options([
                        'App\\Models\\NetworkLeader' => 'Network Leader',
                        'App\\Models\\G12Leader' => 'G12 Leader',
                        'App\\Models\\CellLeader' => 'Cell Leader',
                    ])
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('middle_name'),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone_number')
                    ->tel(),
                DatePicker::make('birthday'),
                TextInput::make('address'),
                TextInput::make('civil_status_id')
                    ->numeric(),
                \Filament\Forms\Components\Select::make('sex_id')
                    ->label('Sex')
                    ->relationship('sex', 'name')
                    ->required(),
            ]);
    }
}
