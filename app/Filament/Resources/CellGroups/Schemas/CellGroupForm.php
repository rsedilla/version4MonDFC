<?php

namespace App\Filament\Resources\CellGroups\Schemas;

use App\Models\CellGroupType;
use App\Services\CellGroupIdService;
use App\Traits\HasLeaderSearch;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class CellGroupForm
{
    use HasLeaderSearch;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Cell Group ID Number - Auto-generated info
                Placeholder::make('cell_group_id_info')
                    ->label('🆔 Cell Group ID Number')
                    ->content(function () {
                        $availableSlots = CellGroupIdService::getAvailableSlots();
                        $currentMonth = date('F Y');
                        $nextId = date('Ym') . str_pad(CellGroupIdService::getCurrentMonthCount() + 1, 3, '0', STR_PAD_LEFT);
                        
                        return "
                            <div class='text-sm'>
                                <p><strong>Next ID:</strong> {$nextId}</p>
                                <p><strong>Available slots this month ({$currentMonth}):</strong> {$availableSlots}/300</p>
                                <p class='text-gray-600 mt-1'>
                                    <em>The Cell Group ID will be automatically generated when you create this group.</em>
                                </p>
                            </div>
                        ";
                    })
                    ->columnSpan(2),

                // Cell Group Name
                TextInput::make('name')
                    ->label('📝 Cell Group Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter cell group name')
                    ->columnSpan(2),

                // Optimized leader search using the trait and service
                ...self::leaderSelect('leader_info', '👤 Select Cell Leader'),

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
