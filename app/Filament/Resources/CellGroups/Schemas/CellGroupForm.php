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
use Illuminate\Support\Facades\DB;

class CellGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Cell Leader Selection - Optimized with all leader types
                Select::make('leader_info')
                    ->label('👤 Select Cell Leader')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        $results = [];
                        
                        // Search Cell Leaders
                        $cellLeaders = CellLeader::with('member')
                            ->whereHas('member', function ($query) use ($search) {
                                $query->where(DB::raw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)"), 'LIKE', "%{$search}%");
                            })
                            ->limit(10)
                            ->get();
                        
                        foreach ($cellLeaders as $leader) {
                            $results["CellLeader:{$leader->id}"] = $leader->member->full_name . ' (Cell Leader)';
                        }
                        
                        // Search G12 Leaders
                        $g12Leaders = G12Leader::with('member')
                            ->whereHas('member', function ($query) use ($search) {
                                $query->where(DB::raw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)"), 'LIKE', "%{$search}%");
                            })
                            ->limit(10)
                            ->get();
                        
                        foreach ($g12Leaders as $leader) {
                            $results["G12Leader:{$leader->id}"] = $leader->member->full_name . ' (G12 Leader)';
                        }
                        
                        // Search Network Leaders
                        $networkLeaders = NetworkLeader::with('member')
                            ->whereHas('member', function ($query) use ($search) {
                                $query->where(DB::raw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)"), 'LIKE', "%{$search}%");
                            })
                            ->limit(10)
                            ->get();
                        
                        foreach ($networkLeaders as $leader) {
                            $results["NetworkLeader:{$leader->id}"] = $leader->member->full_name . ' (Network Leader)';
                        }
                        
                        return $results;
                    })
                    ->getOptionLabelUsing(function ($value) {
                        if (!$value || !str_contains($value, ':')) {
                            return $value;
                        }
                        
                        [$modelType, $id] = explode(':', $value, 2);
                        
                        $model = match ($modelType) {
                            'CellLeader' => CellLeader::with('member')->find($id),
                            'G12Leader' => G12Leader::with('member')->find($id),
                            'NetworkLeader' => NetworkLeader::with('member')->find($id),
                            default => null
                        };
                        
                        if ($model && $model->member) {
                            $leaderTypeLabel = match ($modelType) {
                                'CellLeader' => 'Cell Leader',
                                'G12Leader' => 'G12 Leader', 
                                'NetworkLeader' => 'Network Leader',
                                default => 'Leader'
                            };
                            return $model->member->full_name . " ({$leaderTypeLabel})";
                        }
                        
                        return $value;
                    })
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state && str_contains($state, ':')) {
                            [$modelType, $id] = explode(':', $state, 2);
                            
                            $leaderType = match ($modelType) {
                                'CellLeader' => 'App\Models\CellLeader',
                                'G12Leader' => 'App\Models\G12Leader',
                                'NetworkLeader' => 'App\Models\NetworkLeader',
                                default => null
                            };
                            
                            if ($leaderType) {
                                $set('leader_type', $leaderType);
                                $set('leader_id', $id);
                            }
                        }
                    })
                    ->placeholder('Type to search for leaders...')
                    ->noSearchResultsMessage('No leaders found. Try a different search term.')
                    ->loadingMessage('Searching leaders...')
                    ->dehydrated(false)
                    ->columnSpan(2),

                // Hidden fields to store the actual leader relationship data
                TextInput::make('leader_id')
                    ->hidden()
                    ->dehydrated(),
                    
                TextInput::make('leader_type')
                    ->hidden()
                    ->dehydrated(),

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
