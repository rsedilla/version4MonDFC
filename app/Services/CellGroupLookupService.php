<?php

namespace App\Services;

use App\Models\CellGroupInfo;
use App\Models\CellGroup;
use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CellGroupLookupService
{
    /**
     * Get cell group details by cell_group_idnum
     * 
     * @param string $idNum
     * @return array|null
     */
    public static function getCellGroupByIdNum(string $idNum): ?array
    {
        $cellGroupInfo = CellGroupInfo::where('cell_group_idnum', $idNum)
            ->with([
                'cellGroup.leader.member', // Get the leader's member details
                'cellGroup.type',          // Get the cell group type
                'cellGroup'                // Get the cell group itself
            ])
            ->first();
            
        if (!$cellGroupInfo) {
            return null;
        }
        
        $cellGroup = $cellGroupInfo->cellGroup;
        
        // Get attendees/members of this cell group
        $attendees = self::getCellGroupAttendees($cellGroup->id);
        
        return [
            'cell_group_info' => [
                'id_number' => $cellGroupInfo->cell_group_idnum,
                'day' => $cellGroupInfo->day,
                'time' => $cellGroupInfo->time,
                'location' => $cellGroupInfo->location,
                'created_at' => $cellGroupInfo->created_at,
            ],
            'cell_group' => [
                'id' => $cellGroup->id,
                'name' => $cellGroup->name,
                'description' => $cellGroup->description,
                'is_active' => $cellGroup->is_active,
                'type' => $cellGroup->type?->name,
                'created_at' => $cellGroup->created_at,
            ],
            'leader' => self::getLeaderDetails($cellGroup),
            'attendees' => $attendees,
            'statistics' => [
                'total_attendees' => $attendees->count(),
                'active_attendees' => $attendees->where('status', 'active')->count(),
            ]
        ];
    }
    
    /**
     * Get leader details from cell group
     * 
     * @param CellGroup $cellGroup
     * @return array|null
     */
    private static function getLeaderDetails(CellGroup $cellGroup): ?array
    {
        if (!$cellGroup->leader) {
            return null;
        }
        
        $leader = $cellGroup->leader;
        $member = $leader->member ?? null;
        
        if (!$member) {
            return null;
        }
        
        return [
            'id' => $leader->id,
            'type' => $cellGroup->leader_type,
            'member' => [
                'id' => $member->id,
                'name' => $member->first_name . ' ' . $member->last_name,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'email' => $member->email,
                'phone' => $member->phone,
            ]
        ];
    }
    
    /**
     * Get attendees of a cell group
     * 
     * @param int $cellGroupId
     * @return Collection
     */
    private static function getCellGroupAttendees(int $cellGroupId): Collection
    {
        // Check multiple possible attendance relationships
        $attendees = collect();
        
        // From cell_group_attendees table (polymorphic)
        $polymorphicAttendees = DB::table('cell_group_attendees')
            ->where('cell_group_id', $cellGroupId)
            ->get();
            
        foreach ($polymorphicAttendees as $attendee) {
            if ($attendee->attendee_type === 'App\\Models\\Member') {
                $member = Member::find($attendee->attendee_id);
                if ($member) {
                    $attendees->push([
                        'type' => 'Member',
                        'id' => $member->id,
                        'name' => $member->first_name . ' ' . $member->last_name,
                        'first_name' => $member->first_name,
                        'last_name' => $member->last_name,
                        'email' => $member->email,
                        'phone' => $member->phone,
                        'status' => 'active', // Default status
                        'joined_date' => $attendee->created_at,
                    ]);
                }
            }
        }
        
        // From cell_members table (direct relationship)
        $cellMembers = DB::table('cell_members')
            ->join('members', 'cell_members.member_id', '=', 'members.id')
            ->where('cell_members.cell_group_id', $cellGroupId)
            ->select(
                'members.*',
                'cell_members.status',
                'cell_members.joined_date',
                'cell_members.id as cell_member_id'
            )
            ->get();
            
        foreach ($cellMembers as $cellMember) {
            $attendees->push([
                'type' => 'Cell Member',
                'id' => $cellMember->id,
                'cell_member_id' => $cellMember->cell_member_id,
                'name' => $cellMember->first_name . ' ' . $cellMember->last_name,
                'first_name' => $cellMember->first_name,
                'last_name' => $cellMember->last_name,
                'email' => $cellMember->email,
                'phone' => $cellMember->phone,
                'status' => $cellMember->status,
                'joined_date' => $cellMember->joined_date,
            ]);
        }
        
        return $attendees->unique('id'); // Remove duplicates based on member ID
    }
    
    /**
     * Search cell groups by leader name
     * 
     * @param string $leaderName
     * @return Collection
     */
    public static function searchByLeaderName(string $leaderName): Collection
    {
        return CellGroupInfo::whereHas('cellGroup.leader.member', function ($query) use ($leaderName) {
            $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$leaderName}%")
                  ->orWhere('first_name', 'LIKE', "%{$leaderName}%")
                  ->orWhere('last_name', 'LIKE', "%{$leaderName}%");
        })
        ->with(['cellGroup.leader.member', 'cellGroup.type'])
        ->get()
        ->map(function ($info) {
            return [
                'id_number' => $info->cell_group_idnum,
                'cell_group_name' => $info->cellGroup->name,
                'leader_name' => $info->cellGroup->leader->member->first_name . ' ' . $info->cellGroup->leader->member->last_name,
                'type' => $info->cellGroup->type?->name,
                'day' => $info->day,
                'time' => $info->time,
                'location' => $info->location,
            ];
        });
    }
    
    /**
     * Search cell groups by attendee name
     * 
     * @param string $attendeeName
     * @return Collection
     */
    public static function searchByAttendeeName(string $attendeeName): Collection
    {
        // Search in cell_members table
        $cellGroupIds = DB::table('cell_members')
            ->join('members', 'cell_members.member_id', '=', 'members.id')
            ->where(DB::raw("CONCAT(members.first_name, ' ', members.last_name)"), 'LIKE', "%{$attendeeName}%")
            ->orWhere('members.first_name', 'LIKE', "%{$attendeeName}%")
            ->orWhere('members.last_name', 'LIKE', "%{$attendeeName}%")
            ->pluck('cell_members.cell_group_id')
            ->unique();
        
        // Search in cell_group_attendees table
        $polymorphicGroupIds = DB::table('cell_group_attendees')
            ->join('members', function($join) {
                $join->on('cell_group_attendees.attendee_id', '=', 'members.id')
                     ->where('cell_group_attendees.attendee_type', '=', 'App\\Models\\Member');
            })
            ->where(DB::raw("CONCAT(members.first_name, ' ', members.last_name)"), 'LIKE', "%{$attendeeName}%")
            ->orWhere('members.first_name', 'LIKE', "%{$attendeeName}%")
            ->orWhere('members.last_name', 'LIKE', "%{$attendeeName}%")
            ->pluck('cell_group_attendees.cell_group_id')
            ->unique();
        
        $allGroupIds = $cellGroupIds->merge($polymorphicGroupIds)->unique();
        
        return CellGroupInfo::whereIn('cell_group_id', $allGroupIds)
            ->with(['cellGroup.leader.member', 'cellGroup.type'])
            ->get()
            ->map(function ($info) {
                return [
                    'id_number' => $info->cell_group_idnum,
                    'cell_group_name' => $info->cellGroup->name,
                    'leader_name' => $info->cellGroup->leader->member->first_name . ' ' . $info->cellGroup->leader->member->last_name,
                    'type' => $info->cellGroup->type?->name,
                    'day' => $info->day,
                    'time' => $info->time,
                    'location' => $info->location,
                ];
            });
    }
    
    /**
     * Get all cell groups for a specific month
     * 
     * @param int $year
     * @param int $month
     * @return Collection
     */
    public static function getCellGroupsByMonth(int $year, int $month): Collection
    {
        return CellGroupIdService::getCellGroupsByMonth($year, $month)
            ->map(function ($info) {
                return [
                    'id_number' => $info->cell_group_idnum,
                    'cell_group_name' => $info->cellGroup->name,
                    'leader_name' => $info->cellGroup->leader->member->first_name . ' ' . $info->cellGroup->leader->member->last_name,
                    'type' => $info->cellGroup->type?->name,
                    'day' => $info->day,
                    'time' => $info->time,
                    'location' => $info->location,
                    'is_active' => $info->cellGroup->is_active,
                    'created_at' => $info->created_at,
                ];
            });
    }
}
