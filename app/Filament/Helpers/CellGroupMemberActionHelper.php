<?php

namespace App\Filament\Helpers;

use App\Models\Member;
use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use App\Models\EmergingLeader;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class CellGroupMemberActionHelper
{
    /**
     * Create the assign members action for cell groups
     */
    public static function makeAssignMembersAction(): Action
    {
        return Action::make('assignMembers')
            ->label('Assign Members')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->form([
                Select::make('selected_members')
                    ->label('Select Members to Assign')
                    ->options(function () {
                        return self::getAllAvailableMembers();
                    })
                    ->multiple()
                    ->searchable()
                    ->placeholder('Search and select members...')
                    ->helperText('Select members from all leadership levels and regular members')
            ])
            ->action(function (array $data, $record) {
                if (!empty($data['selected_members'])) {
                    // Get current attendees
                    $currentAttendees = $record->attendees()->pluck('members.id')->toArray();
                    
                    // Merge with new selections (avoid duplicates)
                    $allAttendees = array_unique(array_merge($currentAttendees, $data['selected_members']));
                    
                    // Sync the attendees
                    $record->attendees()->sync($allAttendees);
                    
                    $memberCount = count($data['selected_members']);
                    $totalCount = count($allAttendees);
                    
                    Notification::make()
                        ->title('Members Assigned')
                        ->body("Successfully assigned {$memberCount} new members. Total: {$totalCount} members.")
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('No Members Selected')
                        ->body('Please select at least one member to assign.')
                        ->warning()
                        ->send();
                }
            })
            ->fillForm(function ($record) {
                // Pre-fill with currently assigned members
                $currentAttendees = $record->attendees()->pluck('members.id')->toArray();
                return ['selected_members' => $currentAttendees];
            });
    }

    /**
     * Create bulk assign members action for cell group table
     */
    public static function makeBulkAssignMembersAction(): \Filament\Actions\BulkAction
    {
        return \Filament\Actions\BulkAction::make('bulkAssignMembers')
            ->label('Bulk Assign Members')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->form([
                Select::make('selected_members')
                    ->label('Select Members to Assign')
                    ->options(function () {
                        return self::getAllAvailableMembers();
                    })
                    ->multiple()
                    ->searchable()
                    ->placeholder('Search and select members...')
                    ->helperText('These members will be assigned to all selected cell groups')
                    ->required()
            ])
            ->action(function (array $data, $records) {
                $assignedCount = 0;
                foreach ($records as $cellGroup) {
                    if (!empty($data['selected_members'])) {
                        // Get current attendees
                        $currentAttendees = $cellGroup->attendees()->pluck('members.id')->toArray();
                        
                        // Merge with new selections
                        $allAttendees = array_unique(array_merge($currentAttendees, $data['selected_members']));
                        
                        // Sync the attendees
                        $cellGroup->attendees()->sync($allAttendees);
                        $assignedCount++;
                    }
                }
                
                $memberCount = count($data['selected_members']);
                
                Notification::make()
                    ->title('Bulk Assignment Complete')
                    ->body("Successfully assigned {$memberCount} members to {$assignedCount} cell groups.")
                    ->success()
                    ->send();
            });
    }

    /**
     * Get all available members from different leadership levels
     */
    public static function getAllAvailableMembers(): array
    {
        $options = [];
        $processedMemberIds = []; // Track processed members to avoid duplicates

        try {
            // Get all members first - this will be our primary source
            $allMembers = Member::orderBy('first_name')->get();
            
            foreach ($allMembers as $member) {
                if (!in_array($member->id, $processedMemberIds)) {
                    $options[$member->id] = $member->full_name;
                    $processedMemberIds[] = $member->id;
                }
            }

        } catch (\Exception $e) {
            Log::error('Error loading members for cell group assignment: ' . $e->getMessage());
            
            // Fallback to just regular members
            $fallbackMembers = Member::orderBy('first_name')->get();
            foreach ($fallbackMembers as $member) {
                $options[$member->id] = $member->full_name;
            }
        }

        return $options;
    }

    /**
     * Get members for a specific leader type
     */
    private static function getLeaderMembers(string $leaderModel, string $leaderType): array
    {
        $options = [];
        
        try {
            $leaders = $leaderModel::with('member')->get();
            
            foreach ($leaders as $leader) {
                if ($leader->member) {
                    $options[$leader->member->id] = $leader->member->full_name . " ({$leaderType})";
                }
            }
        } catch (\Exception $e) {
            Log::error("Error loading {$leaderType} members: " . $e->getMessage());
        }
        
        return $options;
    }

    /**
     * Get clean role type for display
     */
    private static function getCleanRoleType($member): string
    {
        // First check if member has a clean member_leader_type
        if ($member->member_leader_type && !str_contains($member->member_leader_type, 'App\\Models\\')) {
            return $member->member_leader_type;
        }
        
        // If member_leader_type contains full class path, clean it up
        if ($member->member_leader_type) {
            $roleType = str_replace('App\\Models\\', '', $member->member_leader_type);
            
            // Convert class names to readable format
            return match ($roleType) {
                'CellLeader' => 'Cell Leader',
                'G12Leader' => 'G12 Leader',
                'NetworkLeader' => 'Network Leader',
                'EmergingLeader' => 'Emerging Leader',
                'SeniorPastor' => 'Senior Pastor',
                default => ucfirst($roleType)
            };
        }
        
        // Fallback to checking actual leadership relationships
        try {
            // Check if member exists in leadership tables
            if (NetworkLeader::where('member_id', $member->id)->exists()) {
                return 'Network Leader';
            }
            if (G12Leader::where('member_id', $member->id)->exists()) {
                return 'G12 Leader';
            }
            if (CellLeader::where('member_id', $member->id)->exists()) {
                return 'Cell Leader';
            }
            if (class_exists(EmergingLeader::class) && EmergingLeader::where('member_id', $member->id)->exists()) {
                return 'Emerging Leader';
            }
        } catch (\Exception $e) {
            Log::error('Error checking member leadership: ' . $e->getMessage());
        }
        
        return 'Member';
    }

    /**
     * Get member statistics for a cell group
     */
    public static function getMemberStatistics($cellGroup): array
    {
        if (!$cellGroup) {
            return ['total' => 0, 'by_type' => []];
        }

        $attendees = $cellGroup->attendees;
        $stats = [
            'total' => $attendees->count(),
            'by_type' => [
                'members' => 0,
                'cell_leaders' => 0,
                'emerging_leaders' => 0,
                'g12_leaders' => 0,
                'network_leaders' => 0
            ]
        ];

        foreach ($attendees as $member) {
            // Use the same clean role type logic
            $cleanRoleType = self::getCleanRoleType($member);
            
            switch ($cleanRoleType) {
                case 'Network Leader':
                    $stats['by_type']['network_leaders']++;
                    break;
                case 'G12 Leader':
                    $stats['by_type']['g12_leaders']++;
                    break;
                case 'Cell Leader':
                    $stats['by_type']['cell_leaders']++;
                    break;
                case 'Emerging Leader':
                    $stats['by_type']['emerging_leaders']++;
                    break;
                default:
                    $stats['by_type']['members']++;
                    break;
            }
        }

        return $stats;
    }

    /**
     * Create view members action to see assigned members
     */
    public static function makeViewMembersAction(): Action
    {
        return Action::make('viewMembers')
            ->label('View Members')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->modalHeading(fn ($record) => $record->name . ' - Members')
            ->modalDescription('View all members assigned to this cell group')
            ->modalWidth('2xl')
            ->infolist(function ($record): array {
                $attendees = $record->attendees()->orderBy('first_name')->get();
                $stats = self::getMemberStatistics($record);
                
                // Get leader information
                $leaderName = 'No Leader Assigned';
                if ($record->leader_type && $record->leader_id) {
                    try {
                        $leaderModel = app($record->leader_type)->find($record->leader_id);
                        if ($leaderModel && $leaderModel->member) {
                            $leaderName = $leaderModel->member->full_name;
                        }
                    } catch (\Exception $e) {
                        $leaderName = 'Leader Not Found';
                    }
                }

                return [
                    TextEntry::make('cell_group_name')
                        ->label('Cell Group Name')
                        ->getStateUsing(fn ($record) => $record->name)
                        ->weight('bold')
                        ->size('lg')
                        ->icon('heroicon-o-user-group')
                        ->color('primary'),
                    
                    TextEntry::make('leader_name')
                        ->label('Leader')
                        ->getStateUsing(fn ($record) => $leaderName)
                        ->icon('heroicon-o-user-circle')
                        ->color('success'),
                    
                    TextEntry::make('total_members')
                        ->label('Total Members')
                        ->getStateUsing(fn ($record) => $stats['total'])
                        ->badge()
                        ->color('info'),
                    
                    TextEntry::make('meeting_schedule')
                        ->label('Meeting Schedule')
                        ->getStateUsing(function ($record) {
                            $day = $record->info?->day ?? 'Not Set';
                            $time = $record->info?->time ? \Carbon\Carbon::parse($record->info->time)->format('g:i A') : 'Not Set';
                            $location = $record->info?->location ?? 'Not Set';
                            return "{$day} at {$time} | {$location}";
                        })
                        ->icon('heroicon-o-calendar-days')
                        ->columnSpanFull(),
                    
                    TextEntry::make('members_list')
                        ->label('Cell Members')
                        ->getStateUsing(function ($record) use ($attendees) {
                            if ($attendees->isEmpty()) {
                                return 'No members assigned yet.';
                            }
                            
                            $membersList = '';
                            foreach ($attendees as $index => $member) {
                                $number = $index + 1;
                                
                                // Get clean role type
                                $roleType = self::getCleanRoleType($member);
                                
                                $membersList .= "{$number}. {$member->full_name} ({$roleType})\n";
                            }
                            return trim($membersList);
                        })
                        ->formatStateUsing(fn (string $state): \Illuminate\Support\HtmlString => 
                            new \Illuminate\Support\HtmlString(nl2br(e($state)))
                        )
                        ->columnSpanFull(),
                    
                    TextEntry::make('statistics')
                        ->label('Member Statistics')
                        ->getStateUsing(function ($record) use ($stats) {
                            $breakdown = [];
                            if ($stats['by_type']['members'] > 0) {
                                $breakdown[] = "Regular Members: {$stats['by_type']['members']}";
                            }
                            if ($stats['by_type']['cell_leaders'] > 0) {
                                $breakdown[] = "Cell Leaders: {$stats['by_type']['cell_leaders']}";
                            }
                            if ($stats['by_type']['g12_leaders'] > 0) {
                                $breakdown[] = "G12 Leaders: {$stats['by_type']['g12_leaders']}";
                            }
                            if ($stats['by_type']['network_leaders'] > 0) {
                                $breakdown[] = "Network Leaders: {$stats['by_type']['network_leaders']}";
                            }
                            if ($stats['by_type']['emerging_leaders'] > 0) {
                                $breakdown[] = "Emerging Leaders: {$stats['by_type']['emerging_leaders']}";
                            }
                            return empty($breakdown) ? 'No statistics available' : implode(' | ', $breakdown);
                        })
                        ->badge()
                        ->color('gray')
                        ->columnSpanFull(),
                ];
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }
}
