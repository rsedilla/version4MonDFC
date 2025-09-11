<?php

namespace App\Services;

use App\Models\CellGroup;
use App\Models\Member;
use App\Models\G12Leader;
use App\Models\CgAttendanceRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CellGroupService
{
    /**
     * Get all members under a specific leader (like Raymond Sedilla)
     * This will return G12 Leaders and their members
     */
    public function getCellGroupMembers(int $leaderId, string $leaderType = 'App\\Models\\G12Leader'): Collection
    {
        try {
            // Find members who report directly to this leader
            $directMembers = Member::where('leader_id', $leaderId)
                                 ->where('leader_type', $leaderType)
                                 ->with(['leader'])
                                 ->get();

            $result = collect();

            foreach ($directMembers as $member) {
                // Get the leader record (G12Leader, NetworkLeader, etc.)
                $leaderRecord = $member->leader;
                
                if ($leaderRecord) {
                    // Get members under this leader
                    $subMembers = $leaderRecord->members ?? collect();
                    
                    $result->push([
                        'leader_name' => $member->full_name,
                        'leader_type' => class_basename($member->leader_type),
                        'leader_id' => $member->id,
                        'leader_record_id' => $leaderRecord->id,
                        'members_count' => $subMembers->count(),
                        'members' => $subMembers->map(function ($subMember) {
                            return [
                                'id' => $subMember->id,
                                'name' => $subMember->full_name,
                                'email' => $subMember->email,
                            ];
                        })
                    ]);
                }
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error retrieving cell group members: ' . $e->getMessage(), [
                'leader_id' => $leaderId,
                'leader_type' => $leaderType
            ]);
            
            return collect();
        }
    }

    /**
     * Get G12 Leaders under a specific leader (like Raymond Sedilla)
     */
    public function getG12LeadersUnder(int $leaderId): Collection
    {
        try {
            // Find G12 Leaders who report to this leader
            $g12Leaders = Member::where('leader_id', $leaderId)
                               ->where('leader_type', 'App\\Models\\G12Leader')
                               ->with(['leader.members'])
                               ->get();

            return $g12Leaders->map(function ($member) {
                $g12Leader = $member->leader; // This gets the G12Leader record
                
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'leader_type' => 'G12 Leader',
                    'g12_leader_id' => $g12Leader?->id,
                    'members_under_them' => $g12Leader?->members ?? collect(),
                    'members_count' => $g12Leader?->members?->count() ?? 0
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error retrieving G12 leaders: ' . $e->getMessage(), [
                'leader_id' => $leaderId
            ]);
            
            return collect();
        }
    }

    /**
     * Find a leader by name (for searching Raymond Sedilla for example)
     */
    public function findLeaderByName(string $name): ?Member
    {
        try {
            return Member::whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . strtolower($name) . '%'])
                        ->first();
                        
        } catch (\Exception $e) {
            Log::error('Error finding leader by name: ' . $e->getMessage(), [
                'name' => $name
            ]);
            
            return null;
        }
    }

    /**
     * Get all cell groups with their attendees from attendance records
     */
    public function getCellGroupsWithAttendees(): Collection
    {
        try {
            return CellGroup::with(['attendanceRecords.attendee'])
                ->get()
                ->map(function ($cellGroup) {
                    // Get unique attendees from attendance records
                    $attendees = $cellGroup->attendanceRecords
                        ->whereNotNull('attendee')
                        ->groupBy(function ($record) {
                            return $record->attendee_type . ':' . $record->attendee_id;
                        })
                        ->map(function ($records) {
                            $attendee = $records->first()->attendee;
                            return [
                                'id' => $attendee->id,
                                'name' => $attendee->full_name ?? $attendee->name ?? 'Unknown',
                                'type' => class_basename($records->first()->attendee_type),
                                'attendance_count' => $records->where('present', true)->count(),
                                'total_records' => $records->count(),
                            ];
                        });

                    return [
                        'cell_group_id' => $cellGroup->id,
                        'cell_group_name' => $cellGroup->name,
                        'attendees' => $attendees->values(),
                        'attendees_count' => $attendees->count(),
                    ];
                })
                ->filter(function ($cellGroup) {
                    return $cellGroup['attendees_count'] > 0;
                });

        } catch (\Exception $e) {
            Log::error('Error retrieving cell groups with attendees: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get cell group with its assigned members (not just attendance records)
     */
    public function getCellGroupWithMembers(?string $cellGroupName = null): Collection
    {
        try {
            $query = CellGroup::with(['attendees', 'info']);
            
            // If specific cell group name is provided, filter by it
            if ($cellGroupName) {
                $query->where('name', 'like', "%{$cellGroupName}%");
            }
            
            return $query->get()->map(function ($cellGroup) {
                // Get leader information
                $leaderName = 'No Leader Assigned';
                $leaderType = 'Unknown';
                
                if ($cellGroup->leader_type && $cellGroup->leader_id) {
                    try {
                        $leaderModel = app($cellGroup->leader_type)->find($cellGroup->leader_id);
                        if ($leaderModel && $leaderModel->member) {
                            $leaderName = $leaderModel->member->full_name;
                            $leaderType = class_basename($cellGroup->leader_type);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error getting leader info: ' . $e->getMessage());
                        $leaderName = 'Leader Not Found';
                    }
                }
                
                // Get assigned members (attendees)
                $members = $cellGroup->attendees->map(function ($member) {
                    // Get member role type
                    $roleType = 'Member';
                    if ($member->member_leader_type) {
                        $roleType = str_replace('App\\Models\\', '', $member->member_leader_type);
                        $roleType = match ($roleType) {
                            'CellLeader' => 'Cell Leader',
                            'G12Leader' => 'G12 Leader',
                            'NetworkLeader' => 'Network Leader',
                            'EmergingLeader' => 'Emerging Leader',
                            'SeniorPastor' => 'Senior Pastor',
                            default => ucfirst($roleType)
                        };
                    }
                    
                    return [
                        'id' => $member->id,
                        'name' => $member->full_name,
                        'email' => $member->email,
                        'role_type' => $roleType,
                        'phone' => $member->phone_number,
                    ];
                });
                
                return [
                    'cell_group_id' => $cellGroup->id,
                    'cell_group_name' => $cellGroup->name,
                    'leader_name' => $leaderName,
                    'leader_type' => $leaderType,
                    'meeting_day' => $cellGroup->info?->day ?? 'Not Set',
                    'meeting_time' => $cellGroup->info?->time ? \Carbon\Carbon::parse($cellGroup->info->time)->format('g:i A') : 'Not Set',
                    'meeting_location' => $cellGroup->info?->location ?? 'Not Set',
                    'members' => $members,
                    'members_count' => $members->count(),
                ];
            })->filter(function ($cellGroup) {
                // Only return cell groups that have data
                return $cellGroup['cell_group_name'] !== null;
            });
            
        } catch (\Exception $e) {
            Log::error('Error retrieving cell groups with members: ' . $e->getMessage());
            return collect();
        }
    }
}
