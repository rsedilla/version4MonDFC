<?php

namespace App\Filament\Resources\CellGroups\Schemas;

use App\Models\CellGroupType;
use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CellGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Cell Group Information Header
                TextInput::make('cg_info_header')
                    ->label('')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('👥 CELL GROUP INFORMATION')
                    ->extraAttributes([
                        'style' => 'text-align: center; font-weight: bold; background: #f0f9ff; color: #0c4a6e; border: 1px solid #bae6fd; border-radius: 8px; padding: 8px;'
                    ])
                    ->columnSpanFull(),

                // Cell Leader Selection
                Select::make('leader_type')
                    ->label('👤 Cell Leader Type')
                    ->required()
                    ->reactive()
                    ->options([
                        'App\Models\CellLeader' => 'Cell Leader',
                        'App\Models\G12Leader' => 'G12 Leader',
                        'App\Models\NetworkLeader' => 'Network Leader',
                    ])
                    ->placeholder('Select leader type first')
                    ->afterStateUpdated(fn (callable $set) => $set('leader_id', null))
                    ->columnSpan(1),

                Select::make('leader_id')
                    ->label('Select Leader')
                    ->required()
                    ->options(function (callable $get) {
                        $leaderType = $get('leader_type');
                        if (!$leaderType) {
                            return [];
                        }

                        return match ($leaderType) {
                            'App\Models\CellLeader' => CellLeader::with('member')
                                ->get()
                                ->pluck('member.full_name', 'id')
                                ->toArray(),
                            'App\Models\G12Leader' => G12Leader::with('member')
                                ->get()
                                ->pluck('member.full_name', 'id')
                                ->toArray(),
                            'App\Models\NetworkLeader' => NetworkLeader::with('member')
                                ->get()
                                ->pluck('member.full_name', 'id')
                                ->toArray(),
                            default => [],
                        };
                    })
                    ->searchable()
                    ->placeholder('Select a leader')
                    ->visible(fn (callable $get) => filled($get('leader_type')))
                    ->columnSpan(1),

                // Cell Group Type
                Select::make('cell_group_type_id')
                    ->label('📋 Cell Group Type')
                    ->required()
                    ->options(CellGroupType::all()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select group type')
                    ->columnSpan(1),

                // Cell Group Name
                TextInput::make('name')
                    ->label('🏷️ Cell Group Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter cell group name')
                    ->columnSpan(1),

                // Meeting Schedule Header
                TextInput::make('meeting_schedule_header')
                    ->label('')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('📅 MEETING SCHEDULE')
                    ->extraAttributes([
                        'style' => 'text-align: center; font-weight: bold; background: #f0fdf4; color: #14532d; border: 1px solid #bbf7d0; border-radius: 8px; padding: 8px;'
                    ])
                    ->columnSpanFull(),

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

                // Meeting Time - Using DateTimePicker with time format
                DateTimePicker::make('info.time')
                    ->label('🕐 Meeting Time')
                    ->required()
                    ->time()
                    ->seconds(false)
                    ->placeholder('Select meeting time')
                    ->columnSpan(1),

                // Meeting Location
                TextInput::make('info.location')
                    ->label('📍 Meeting Location')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter meeting location')
                    ->columnSpan(2),

                // Additional Information Header
                TextInput::make('additional_info_header')
                    ->label('')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('📝 ADDITIONAL INFORMATION')
                    ->extraAttributes([
                        'style' => 'text-align: center; font-weight: bold; background: #fefbeb; color: #92400e; border: 1px solid #fde68a; border-radius: 8px; padding: 8px;'
                    ])
                    ->columnSpanFull(),

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
