<?php

namespace App\Filament\Resources\CgAttendanceRecords\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CgAttendanceRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cell_group_id')
                    ->required()
                    ->numeric(),
                TextInput::make('attendee_id')
                    ->required()
                    ->numeric(),
                TextInput::make('attendee_type')
                    ->required(),
                TextInput::make('year')
                    ->required(),
                TextInput::make('month')
                    ->required()
                    ->numeric(),
                TextInput::make('week_number')
                    ->required()
                    ->numeric(),
                Toggle::make('present')
                    ->required(),
            ]);
    }
}
