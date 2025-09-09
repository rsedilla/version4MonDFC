<?php

namespace App\Filament\Resources\EmergingLeaders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmergingLeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->relationship('member', 'name')
                    ->label('Member')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('leadership_area')
                    ->label('Leadership Area')
                    ->placeholder('e.g., Cell Leadership, Youth Ministry, Worship')
                    ->maxLength(255),
                Textarea::make('notes')
                    ->label('Notes')
                    ->placeholder('Additional observations or training notes...')
                    ->rows(3)
                    ->columnSpanFull(),
                DatePicker::make('identified_date')
                    ->label('Date Identified')
                    ->default(now()),
                Toggle::make('is_active')
                    ->label('Active Status')
                    ->default(true)
                    ->required(),
            ]);
    }
}
