<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_leader_type')
                    ->label('Member Type')
                    ->options([
                    
                        'App\\Models\\Attender' => 'Attender',
                        'App\\Models\\CellMember' => 'Cell Member',
                        'App\\Models\\CellLeader' => 'Cell Leader',
                        'App\\Models\\G12Leader' => 'G12 Leader',
                        'App\\Models\\NetworkLeader' => 'Network Leader',
                        'App\\Models\\SeniorPastor' => 'Senior Pastor'
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
                Select::make('civil_status_id')
                    ->label('Civil Status')
                    ->relationship('civilStatus', 'name')
                    ->required(),
                Select::make('sex_id')
                    ->label('Sex')
                    ->relationship('sex', 'name')
                    ->required(),
            ]);
    }
}
