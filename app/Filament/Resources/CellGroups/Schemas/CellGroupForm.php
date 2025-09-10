<?php

namespace App\Filament\Resources\CellGroups\Schemas;

use App\Models\CellGroupType;
use App\Services\CellGroupIdService;
use App\Traits\HasLeaderSearch;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CellGroupForm
{
    use HasLeaderSearch;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // Cell Group Name - Let users provide a friendly name
                TextInput::make('name')
                    ->label('📝 Cell Group Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., "Victory Warriors", "Faith Builders", "Young Professionals"')
                    ->helperText('Give your cell group a meaningful name that reflects its identity')
                    ->columnSpan(2),

                // Unified Leader Selection - Searches all leader types in one field!
                ...self::leaderSelect('leader_info', '👤 Select Cell Group Leader'),
                
                // Cell Group Type
                Select::make('cell_group_type_id')
                    ->label('📋 Cell Group Type')
                    ->required()
                    ->options(CellGroupType::all()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select group type')
                    ->columnSpan(2),

                // Meeting Day
                Select::make('info.day')
                    ->label('📅 Meeting Day')
                    ->required()
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday', 
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ])
                    ->placeholder('Select meeting day')
                    ->columnSpan(1),

                // Meeting Time - Time only input
                TextInput::make('info.time')
                    ->label('🕐 Meeting Time')
                    ->required()
                    ->type('time')
                    ->placeholder('Select meeting time')
                    ->columnSpan(1),

                // Meeting Location
                TextInput::make('info.location')
                    ->label('📍 Meeting Location')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter meeting location')
                    ->columnSpan(2),

                // Description
                Textarea::make('description')
                    ->label('📄 Description')
                    ->rows(3)
                    ->placeholder('Optional notes or description about this cell group')
                    ->columnSpan(1),

                // Active Status
                Toggle::make('is_active')
                    ->label('✅ Active Status')
                    ->helperText('Toggle to mark the group as active or inactive')
                    ->default(true)
                    ->columnSpan(1),
            ]);
    }
}
