<?php

namespace App\Filament\Resources\CellGroups\Schemas;

use App\Filament\Helpers\CellGroupMemberActionHelper;
use App\Filament\Helpers\DirectLeaderActionHelper;
use App\Models\CellGroupType;
use App\Models\Member;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class CellGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Cell Group Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter cell group name'),

                Select::make('leader_info')
                    ->label('Select Leader')
                    ->searchable()
                    ->required()
                    ->placeholder('Search for a leader...')
                    ->options(function () {
                        return DirectLeaderActionHelper::getDirectLeaderOptions();
                    }),

                Select::make('cell_group_type_id')
                    ->label('Cell Group Type')
                    ->relationship('type', 'name')
                    ->required()
                    ->placeholder('Select cell group type'),

                Select::make('attendees')
                    ->label('Assigned Members')
                    ->multiple()
                    ->options(function () {
                        return CellGroupMemberActionHelper::getAllAvailableMembers();
                    })
                    ->searchable()
                    ->placeholder('Search and select members from all leadership levels')
                    ->helperText('Select members including regular members, cell leaders, G12 leaders, network leaders, and emerging leaders'),

                Select::make('info.day')
                    ->label('Meeting Day')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ])
                    ->required()
                    ->placeholder('Select meeting day'),

                TimePicker::make('info.time')
                    ->label('Meeting Time')
                    ->required()
                    ->seconds(false)
                    ->placeholder('Select meeting time'),

                TextInput::make('info.location')
                    ->label('Meeting Location')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter meeting location address'),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->placeholder('Enter additional details about this cell group')
                    ->columnSpanFull(),
            ]);
    }
}
