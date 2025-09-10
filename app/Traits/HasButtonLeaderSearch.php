<?php

namespace App\Traits;

use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;

trait HasButtonLeaderSearch
{
    /**
     * Create a simple button-style leader selection with separate selects for each type
     */
    protected static function buttonLeaderSelect(string $name = 'leader_info', string $label = '👤 Select Cell Group Leader'): array
    {
        return [
            // Display current selection
            TextInput::make('selected_leader_display')
                ->label('Selected Leader')
                ->placeholder('No leader selected - use buttons below')
                ->disabled()
                ->dehydrated(false),
            
            // Cell Leader Selection
            Select::make('cell_leader_selection')
                ->label('👥 Cell Leaders')
                ->options(function () {
                    return CellLeader::with('member')
                        ->get()
                        ->mapWithKeys(function ($leader) {
                            return [
                                $leader->id => $leader->member ? 
                                    $leader->member->first_name . ' ' . $leader->member->last_name :
                                    'Unknown Member'
                            ];
                        });
                })
                ->searchable()
                ->placeholder('Select a Cell Leader')
                ->dehydrated(false)
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state) {
                        $leader = CellLeader::with('member')->find($state);
                        if ($leader && $leader->member) {
                            $displayName = $leader->member->first_name . ' ' . $leader->member->last_name . ' (Cell Leader)';
                            $compositeKey = "CellLeader:{$leader->id}";
                            
                            $set('selected_leader_display', $displayName);
                            $set('leader_info', $compositeKey);
                            $set('leader_id', $leader->id);
                            $set('leader_type', 'App\\Models\\CellLeader');
                            
                            // Clear other selections
                            $set('g12_leader_selection', null);
                            $set('network_leader_selection', null);
                        }
                    }
                }),
            
            // G12 Leader Selection
            Select::make('g12_leader_selection')
                ->label('🌟 G12 Leaders')
                ->options(function () {
                    return G12Leader::with('member')
                        ->get()
                        ->mapWithKeys(function ($leader) {
                            return [
                                $leader->id => $leader->member ? 
                                    $leader->member->first_name . ' ' . $leader->member->last_name :
                                    'Unknown Member'
                            ];
                        });
                })
                ->searchable()
                ->placeholder('Select a G12 Leader')
                ->dehydrated(false)
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state) {
                        $leader = G12Leader::with('member')->find($state);
                        if ($leader && $leader->member) {
                            $displayName = $leader->member->first_name . ' ' . $leader->member->last_name . ' (G12 Leader)';
                            $compositeKey = "G12Leader:{$leader->id}";
                            
                            $set('selected_leader_display', $displayName);
                            $set('leader_info', $compositeKey);
                            $set('leader_id', $leader->id);
                            $set('leader_type', 'App\\Models\\G12Leader');
                            
                            // Clear other selections
                            $set('cell_leader_selection', null);
                            $set('network_leader_selection', null);
                        }
                    }
                }),
            
            // Network Leader Selection
            Select::make('network_leader_selection')
                ->label('🌐 Network Leaders')
                ->options(function () {
                    return NetworkLeader::with('member')
                        ->get()
                        ->mapWithKeys(function ($leader) {
                            return [
                                $leader->id => $leader->member ? 
                                    $leader->member->first_name . ' ' . $leader->member->last_name :
                                    'Unknown Member'
                            ];
                        });
                })
                ->searchable()
                ->placeholder('Select a Network Leader')
                ->dehydrated(false)
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state) {
                        $leader = NetworkLeader::with('member')->find($state);
                        if ($leader && $leader->member) {
                            $displayName = $leader->member->first_name . ' ' . $leader->member->last_name . ' (Network Leader)';
                            $compositeKey = "NetworkLeader:{$leader->id}";
                            
                            $set('selected_leader_display', $displayName);
                            $set('leader_info', $compositeKey);
                            $set('leader_id', $leader->id);
                            $set('leader_type', 'App\\Models\\NetworkLeader');
                            
                            // Clear other selections
                            $set('cell_leader_selection', null);
                            $set('g12_leader_selection', null);
                        }
                    }
                }),
            
            // Hidden fields to store the actual leader relationship data
            TextInput::make('leader_info')
                ->hidden()
                ->dehydrated(false),
                
            TextInput::make('leader_id')
                ->hidden()
                ->dehydrated(),
                
            TextInput::make('leader_type')
                ->hidden()
                ->dehydrated(),
        ];
    }

    /**
     * Create a single unified dropdown with all leaders
     */
    protected static function unifiedLeaderSelect(string $name = 'leader_info', string $label = '👤 Select Leader'): array
    {
        return [
            Select::make('unified_leader_selection')
                ->label($label)
                ->options(function () {
                    $options = [];
                    
                    // Add Cell Leaders
                    $cellLeaders = CellLeader::with('member')->get();
                    foreach ($cellLeaders as $leader) {
                        if ($leader->member) {
                            $key = "CellLeader:{$leader->id}";
                            $label = $leader->member->first_name . ' ' . $leader->member->last_name . ' (Cell Leader)';
                            $options[$key] = $label;
                        }
                    }
                    
                    // Add G12 Leaders
                    $g12Leaders = G12Leader::with('member')->get();
                    foreach ($g12Leaders as $leader) {
                        if ($leader->member) {
                            $key = "G12Leader:{$leader->id}";
                            $label = $leader->member->first_name . ' ' . $leader->member->last_name . ' (G12 Leader)';
                            $options[$key] = $label;
                        }
                    }
                    
                    // Add Network Leaders
                    $networkLeaders = NetworkLeader::with('member')->get();
                    foreach ($networkLeaders as $leader) {
                        if ($leader->member) {
                            $key = "NetworkLeader:{$leader->id}";
                            $label = $leader->member->first_name . ' ' . $leader->member->last_name . ' (Network Leader)';
                            $options[$key] = $label;
                        }
                    }
                    
                    return $options;
                })
                ->searchable()
                ->placeholder('Search and select a leader')
                ->required()
                ->dehydrated(false)
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state && str_contains($state, ':')) {
                        [$modelType, $id] = explode(':', $state, 2);
                        
                        $leaderType = match ($modelType) {
                            'CellLeader' => 'App\\Models\\CellLeader',
                            'G12Leader' => 'App\\Models\\G12Leader',
                            'NetworkLeader' => 'App\\Models\\NetworkLeader',
                            default => null
                        };
                        
                        if ($leaderType) {
                            $set('leader_id', (int)$id);
                            $set('leader_type', $leaderType);
                        }
                    } else {
                        // Clear fields if no valid selection
                        $set('leader_id', null);
                        $set('leader_type', null);
                    }
                }),
            
            // Hidden fields to store the actual leader relationship data (only these will be saved)
            TextInput::make('leader_id')
                ->hidden()
                ->dehydrated(true)
                ->required(),
                
            TextInput::make('leader_type')
                ->hidden()  
                ->dehydrated(true)
                ->required(),
        ];
    }
}
