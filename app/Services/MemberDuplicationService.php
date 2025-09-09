<?php

namespace App\Services;

use App\Models\Attender;
use App\Models\CellMember;
use App\Models\EmergingLeader;
use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use Illuminate\Database\Eloquent\Model;

class MemberDuplicationService
{
    /**
     * Check if a member is already assigned to any other table
     */
    public static function isAlreadyAssigned(int $memberId, ?Model $currentRecord = null): array
    {
        $assignments = [];

        // Check in Attenders table
        $attender = Attender::where('member_id', $memberId)
            ->when($currentRecord instanceof Attender, function ($query) use ($currentRecord) {
                return $query->where('id', '!=', $currentRecord->id);
            })
            ->first();
        
        if ($attender) {
            $assignments[] = [
                'table' => 'Attenders',
                'model' => 'Attender',
                'record_id' => $attender->id,
                'message' => 'This member is already registered as an Attender'
            ];
        }

        // Check in Cell Members table
        $cellMember = CellMember::where('member_id', $memberId)
            ->when($currentRecord instanceof CellMember, function ($query) use ($currentRecord) {
                return $query->where('id', '!=', $currentRecord->id);
            })
            ->first();
        
        if ($cellMember) {
            $assignments[] = [
                'table' => 'Cell Members',
                'model' => 'CellMember',
                'record_id' => $cellMember->id,
                'message' => 'This member is already assigned as a Cell Member'
            ];
        }

        // Check in Emerging Leaders table
        $emergingLeader = EmergingLeader::where('member_id', $memberId)
            ->when($currentRecord instanceof EmergingLeader, function ($query) use ($currentRecord) {
                return $query->where('id', '!=', $currentRecord->id);
            })
            ->first();
        
        if ($emergingLeader) {
            $assignments[] = [
                'table' => 'Emerging Leaders',
                'model' => 'EmergingLeader',
                'record_id' => $emergingLeader->id,
                'message' => 'This member is already identified as an Emerging Leader'
            ];
        }

        // Check in Cell Leaders table
        $cellLeader = CellLeader::where('member_id', $memberId)
            ->when($currentRecord instanceof CellLeader, function ($query) use ($currentRecord) {
                return $query->where('id', '!=', $currentRecord->id);
            })
            ->first();
        
        if ($cellLeader) {
            $assignments[] = [
                'table' => 'Cell Leaders',
                'model' => 'CellLeader',
                'record_id' => $cellLeader->id,
                'message' => 'This member is already assigned as a Cell Leader'
            ];
        }

        // Check in G12 Leaders table
        $g12Leader = G12Leader::where('member_id', $memberId)
            ->when($currentRecord instanceof G12Leader, function ($query) use ($currentRecord) {
                return $query->where('id', '!=', $currentRecord->id);
            })
            ->first();
        
        if ($g12Leader) {
            $assignments[] = [
                'table' => 'G12 Leaders',
                'model' => 'G12Leader',
                'record_id' => $g12Leader->id,
                'message' => 'This member is already assigned as a G12 Leader'
            ];
        }

        // Check in Network Leaders table
        $networkLeader = NetworkLeader::where('member_id', $memberId)
            ->when($currentRecord instanceof NetworkLeader, function ($query) use ($currentRecord) {
                return $query->where('id', '!=', $currentRecord->id);
            })
            ->first();
        
        if ($networkLeader) {
            $assignments[] = [
                'table' => 'Network Leaders',
                'model' => 'NetworkLeader',
                'record_id' => $networkLeader->id,
                'message' => 'This member is already assigned as a Network Leader'
            ];
        }

        return $assignments;
    }

    /**
     * Get a formatted error message for duplicate assignments
     */
    public static function getDuplicationErrorMessage(array $assignments): string
    {
        if (empty($assignments)) {
            return '';
        }

        $messages = array_column($assignments, 'message');
        
        if (count($messages) === 1) {
            return $messages[0] . '.';
        }

        return 'This member is already assigned in multiple roles: ' . implode(', ', $messages) . '.';
    }

    /**
     * Check if member can be assigned to a specific table
     */
    public static function canAssignToTable(int $memberId, string $targetTable, ?Model $currentRecord = null): bool
    {
        $assignments = self::isAlreadyAssigned($memberId, $currentRecord);
        
        // Filter out assignments from the same table type we're trying to assign to
        $conflictingAssignments = array_filter($assignments, function ($assignment) use ($targetTable) {
            return $assignment['table'] !== $targetTable;
        });

        return empty($conflictingAssignments);
    }

    /**
     * Get all possible table names for validation
     */
    public static function getAllTableNames(): array
    {
        return [
            'Attenders',
            'Cell Members',
            'Emerging Leaders',
            'Cell Leaders',
            'G12 Leaders',
            'Network Leaders'
        ];
    }

    /**
     * Get member's current assignments summary
     */
    public static function getMemberAssignmentsSummary(int $memberId): array
    {
        $assignments = self::isAlreadyAssigned($memberId);
        
        return [
            'member_id' => $memberId,
            'total_assignments' => count($assignments),
            'assignments' => $assignments,
            'can_add_new' => count($assignments) === 0,
            'summary_message' => count($assignments) === 0 
                ? 'This member is available for assignment' 
                : 'This member is already assigned to: ' . implode(', ', array_column($assignments, 'table'))
        ];
    }
}
