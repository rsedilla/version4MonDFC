<?php

namespace App\Filament\Helpers;

use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use App\Models\SeniorPastor;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class DirectLeaderActionHelper
{
    /**
     * Create the assign direct leader action
     */
    public static function makeAssignDirectLeaderAction(): Action
    {
        return Action::make('assignDirectLeader')
            ->label('Assign Direct Leader')
            ->icon('heroicon-o-user-plus')
            ->color('success')
            ->form([
                Select::make('direct_leader')
                    ->label('Select Direct Leader')
                    ->options(function () {
                        return self::getDirectLeaderOptions();
                    })
                    ->searchable()
                    ->placeholder('Choose a direct leader')
                    ->required()
            ])
            ->action(function (array $data, $record) {
                if (!empty($data['direct_leader']) && str_contains($data['direct_leader'], ':')) {
                    [$leaderType, $leaderId] = explode(':', $data['direct_leader']);
                    
                    $record->leader_id = (int) $leaderId;
                    $record->leader_type = $leaderType;
                    $record->save();
                    
                    // Get leader name for notification
                    $leaderModel = app($leaderType)->find($leaderId);
                    $leaderName = $leaderModel->member->full_name ?? 'Unknown';
                    $leaderTypeLabel = self::getLeaderTypeLabel($leaderType);
                    
                    Notification::make()
                        ->title('Direct Leader Assigned')
                        ->body("Successfully assigned {$leaderTypeLabel}: {$leaderName} to {$record->full_name}")
                        ->success()
                        ->send();
                } else {
                    // Clear assignment
                    $record->leader_id = null;
                    $record->leader_type = null;
                    $record->save();
                    
                    Notification::make()
                        ->title('Direct Leader Removed')
                        ->body("Removed direct leader from {$record->full_name}")
                        ->success()
                        ->send();
                }
            })
            ->fillForm(function ($record) {
                if ($record->leader_type && $record->leader_id) {
                    // Find the user-friendly label for the current leader assignment
                    $options = self::getDirectLeaderOptions();
                    $key = $record->leader_type . ':' . $record->leader_id;
                    return [
                        'direct_leader' => array_key_exists($key, $options) ? $key : ''
                    ];
                }
                return [];
            });
    }

    /**
     * Create the bulk assign direct leader action
     */
    public static function makeBulkAssignDirectLeaderAction(): \Filament\Actions\BulkAction
    {
        return \Filament\Actions\BulkAction::make('bulkAssignDirectLeader')
            ->label('Assign Direct Leader')
            ->icon('heroicon-o-user-plus')
            ->color('success')
            ->form([
                Select::make('direct_leader')
                    ->label('Select Direct Leader')
                    ->options(function () {
                        return self::getDirectLeaderOptions();
                    })
                    ->searchable()
                    ->placeholder('Choose a direct leader')
                    ->required()
            ])
            ->action(function (array $data, $records) {
                $count = 0;
                foreach ($records as $record) {
                    if (!empty($data['direct_leader']) && str_contains($data['direct_leader'], ':')) {
                        [$leaderType, $leaderId] = explode(':', $data['direct_leader']);
                        
                        $record->leader_id = (int) $leaderId;
                        $record->leader_type = $leaderType;
                        $record->save();
                        $count++;
                    } else {
                        // Clear assignment
                        $record->leader_id = null;
                        $record->leader_type = null;
                        $record->save();
                        $count++;
                    }
                }
                
                Notification::make()
                    ->title('Bulk Assignment Complete')
                    ->body("Successfully updated direct leader for {$count} members")
                    ->success()
                    ->send();
            });
    }

    /**
     * Get all available direct leader options
     */
    public static function getDirectLeaderOptions(): array
    {
    $options = [];

        // Get all Cell Leaders
        $cellLeaders = CellLeader::with('member')->get();
        foreach ($cellLeaders as $leader) {
            $options[CellLeader::class . ':' . $leader->id] = 'Cell Leader: ' . ($leader->member->full_name ?? 'Unknown');
        }
        // Get all G12 Leaders
        $g12Leaders = G12Leader::with('member')->get();
        foreach ($g12Leaders as $leader) {
            $options[G12Leader::class . ':' . $leader->id] = 'G12 Leader: ' . ($leader->member->full_name ?? 'Unknown');
        }
        // Get all Network Leaders
        $networkLeaders = NetworkLeader::with('member')->get();
        foreach ($networkLeaders as $leader) {
            $options[NetworkLeader::class . ':' . $leader->id] = 'Network Leader: ' . ($leader->member->full_name ?? 'Unknown');
        }
        // Get all Senior Pastors
        $seniorPastors = SeniorPastor::with('member')->get();
        foreach ($seniorPastors as $leader) {
            $options[SeniorPastor::class . ':' . $leader->id] = 'Senior Pastor: ' . ($leader->member->full_name ?? 'Unknown');
        }
        return $options;
    }

    /**
     * Get leader type label
     */
    public static function getLeaderTypeLabel(string $leaderType): string
    {
        return match ($leaderType) {
            CellLeader::class => 'Cell Leader',
            G12Leader::class => 'G12 Leader', 
            NetworkLeader::class => 'Network Leader',
            SeniorPastor::class => 'Senior Pastor',
            default => 'Leader',
        };
    }
}
