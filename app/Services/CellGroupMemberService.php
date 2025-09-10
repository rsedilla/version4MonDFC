<?php

namespace App\Services;

use App\Models\CellGroupInfo;
use App\Models\CellGroup;
use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CellGroupMemberService
{
    /**
     * Get all members under a specific cell group by finding members
     * whose direct leader is the cell group's leader
     * 
     * @param string $cellGroupIdNum
     * @return Collection
     */
    public static function getMembersByCellGroupIdNum(string $cellGroupIdNum): Collection
    {
        $cellGroupInfo = CellGroupInfo::where('cell_group_idnum', $cellGroupIdNum)
            ->with('cellGroup.leader')
            ->first();
            
        if (!$cellGroupInfo || !$cellGroupInfo->cellGroup->leader) {
            return collect();
        }
        
        $cellGroup = $cellGroupInfo->cellGroup;
        $leader = $cellGroup->leader;
        
        // Find all members whose direct leader is this cell group's leader
        return Member::where('leader_id', $leader->id)
            ->where('leader_type', $cellGroup->leader_type)
            ->with(['civilStatus', 'sex'])
            ->get()
            ->map(function ($member) use ($cellGroupIdNum) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'first_name' => $member->first_name,
                    'last_name' => $member->last_name,
                    'email' => $member->email,
                    'phone' => $member->phone_number,
                    'birthday' => $member->birthday,
                    'address' => $member->address,
                    'civil_status' => $member->civilStatus?->name,
                    'sex' => $member->sex?->name,
                    'cell_group_idnum' => $cellGroupIdNum,
                    'member_leader_type' => $member->member_leader_type,
                ];
            });
    }
    
    /**
     * Assign a cell group ID number to a leader
     * This automatically makes all members under that leader part of this cell group
     * 
     * @param string $cellGroupIdNum
     * @param int $leaderId
     * @param string $leaderType
     * @return bool
     */
    public static function assignCellGroupToLeader(string $cellGroupIdNum, int $leaderId, string $leaderType): bool
    {
        try {
            // Find the cell group info
            $cellGroupInfo = CellGroupInfo::where('cell_group_idnum', $cellGroupIdNum)
                ->with('cellGroup')
                ->first();
                
            if (!$cellGroupInfo) {
                return false;
            }
            
            // Update the cell group's leader
            $cellGroup = $cellGroupInfo->cellGroup;
            $cellGroup->leader_id = $leaderId;
            $cellGroup->leader_type = $leaderType;
            $cellGroup->save();
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to assign cell group to leader: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get cell group details with all members under the leader
     * 
     * @param string $cellGroupIdNum
     * @return array|null
     */
    public static function getCellGroupWithMembers(string $cellGroupIdNum): ?array
    {
        $cellGroupInfo = CellGroupInfo::where('cell_group_idnum', $cellGroupIdNum)
            ->with([
                'cellGroup.leader.member',
                'cellGroup.type'
            ])
            ->first();
            
        if (!$cellGroupInfo) {
            return null;
        }
        
        $cellGroup = $cellGroupInfo->cellGroup;
        $members = self::getMembersByCellGroupIdNum($cellGroupIdNum);
        
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
            'leader' => [
                'id' => $cellGroup->leader->id,
                'type' => $cellGroup->leader_type,
                'member' => [
                    'id' => $cellGroup->leader->member->id,
                    'name' => $cellGroup->leader->member->full_name,
                    'first_name' => $cellGroup->leader->member->first_name,
                    'last_name' => $cellGroup->leader->member->last_name,
                    'email' => $cellGroup->leader->member->email,
                    'phone' => $cellGroup->leader->member->phone_number,
                ]
            ],
            'members' => $members,
            'statistics' => [
                'total_members' => $members->count(),
                'active_members' => $members->count(), // All are considered active since they have this leader
            ]
        ];
    }
    
    /**
     * Search cell groups by member name
     * This finds which cell group a member belongs to based on their leader
     * 
     * @param string $memberName
     * @return Collection
     */
    public static function searchCellGroupsByMemberName(string $memberName): Collection
    {
        // Find members matching the name
        $members = Member::where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$memberName}%")
            ->orWhere('first_name', 'LIKE', "%{$memberName}%")
            ->orWhere('last_name', 'LIKE', "%{$memberName}%")
            ->whereNotNull('leader_id')
            ->whereNotNull('leader_type')
            ->with('directLeader.member')
            ->get();
        
        $results = collect();
        
        foreach ($members as $member) {
            // Find cell groups where this member's leader is the cell group leader
            $cellGroups = CellGroup::where('leader_id', $member->leader_id)
                ->where('leader_type', $member->leader_type)
                ->with(['info', 'type'])
                ->get();
                
            foreach ($cellGroups as $cellGroup) {
                if ($cellGroup->info) {
                    $results->push([
                        'id_number' => $cellGroup->info->cell_group_idnum,
                        'cell_group_name' => $cellGroup->name,
                        'member_name' => $member->full_name,
                        'leader_name' => $member->directLeader?->member?->full_name,
                        'type' => $cellGroup->type?->name,
                        'day' => $cellGroup->info->day,
                        'time' => $cellGroup->info->time,
                        'location' => $cellGroup->info->location,
                    ]);
                }
            }
        }
        
        return $results->unique('id_number');
    }
    
    /**
     * Get all members under a specific leader type and ID
     * 
     * @param int $leaderId
     * @param string $leaderType
     * @return Collection
     */
    public static function getMembersByLeader(int $leaderId, string $leaderType): Collection
    {
        return Member::where('leader_id', $leaderId)
            ->where('leader_type', $leaderType)
            ->with(['civilStatus', 'sex'])
            ->get();
    }
    
    /**
     * Check if a leader has any cell groups assigned
     * 
     * @param int $leaderId
     * @param string $leaderType
     * @return bool
     */
    public static function leaderHasCellGroups(int $leaderId, string $leaderType): bool
    {
        return CellGroup::where('leader_id', $leaderId)
            ->where('leader_type', $leaderType)
            ->exists();
    }
    
    /**
     * Get all cell groups assigned to a specific leader
     * 
     * @param int $leaderId
     * @param string $leaderType
     * @return Collection
     */
    public static function getCellGroupsByLeader(int $leaderId, string $leaderType): Collection
    {
        return CellGroup::where('leader_id', $leaderId)
            ->where('leader_type', $leaderType)
            ->with(['info', 'type'])
            ->get()
            ->map(function ($cellGroup) {
                return [
                    'id_number' => $cellGroup->info?->cell_group_idnum,
                    'name' => $cellGroup->name,
                    'type' => $cellGroup->type?->name,
                    'day' => $cellGroup->info?->day,
                    'time' => $cellGroup->info?->time,
                    'location' => $cellGroup->info?->location,
                    'is_active' => $cellGroup->is_active,
                ];
            })
            ->filter(fn($group) => $group['id_number']); // Only groups with valid ID numbers
    }
}
