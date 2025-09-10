<?php

namespace App\Services;

use App\Models\Attender;
use App\Models\CellMember;
use App\Models\EmergingLeader;
use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OptimizedMemberDuplicationService
{
    /**
     * Optimized check for member assignments using a single query with unions
     */
    public static function isAlreadyAssigned(int $memberId, ?Model $currentRecord = null): array
    {
        $assignments = [];
        
        // Build exclusion conditions for current record
        $excludeCurrentRecord = self::buildExclusionConditions($currentRecord);
        
        try {
            // Single optimized query using UNION to check all tables at once
            $results = DB::select("
                SELECT 'Attenders' as table_name, 'Attender' as model_name, id as record_id, 'This member is already registered as an Attender' as message
                FROM attenders 
                WHERE member_id = ? {$excludeCurrentRecord['attenders']}
                
                UNION ALL
                
                SELECT 'Cell Members' as table_name, 'CellMember' as model_name, id as record_id, 'This member is already assigned as a Cell Member' as message
                FROM cell_members 
                WHERE member_id = ? {$excludeCurrentRecord['cell_members']}
                
                UNION ALL
                
                SELECT 'Emerging Leaders' as table_name, 'EmergingLeader' as model_name, id as record_id, 'This member is already identified as an Emerging Leader' as message
                FROM emerging_leaders 
                WHERE member_id = ? {$excludeCurrentRecord['emerging_leaders']}
                
                UNION ALL
                
                SELECT 'Cell Leaders' as table_name, 'CellLeader' as model_name, id as record_id, 'This member is already assigned as a Cell Leader' as message
                FROM cell_leaders 
                WHERE member_id = ? {$excludeCurrentRecord['cell_leaders']}
                
                UNION ALL
                
                SELECT 'G12 Leaders' as table_name, 'G12Leader' as model_name, id as record_id, 'This member is already assigned as a G12 Leader' as message
                FROM g12_leaders 
                WHERE member_id = ? {$excludeCurrentRecord['g12_leaders']}
                
                UNION ALL
                
                SELECT 'Network Leaders' as table_name, 'NetworkLeader' as model_name, id as record_id, 'This member is already assigned as a Network Leader' as message
                FROM network_leaders 
                WHERE member_id = ? {$excludeCurrentRecord['network_leaders']}
            ", array_fill(0, 6, $memberId));
            
            // Convert results to the expected format
            foreach ($results as $result) {
                $assignments[] = [
                    'table' => $result->table_name,
                    'model' => $result->model_name,
                    'record_id' => $result->record_id,
                    'message' => $result->message
                ];
            }
            
        } catch (\Exception $e) {
            // Fallback to original method if optimized query fails
            Log::warning('OptimizedMemberDuplicationService failed, falling back to original method: ' . $e->getMessage());
            return MemberDuplicationService::isAlreadyAssigned($memberId, $currentRecord);
        }
        
        return $assignments;
    }
    
    /**
     * Build exclusion conditions for current record to avoid self-conflicts
     */
    private static function buildExclusionConditions(?Model $currentRecord): array
    {
        $conditions = [
            'attenders' => '',
            'cell_members' => '',
            'emerging_leaders' => '',
            'cell_leaders' => '',
            'g12_leaders' => '',
            'network_leaders' => ''
        ];
        
        if ($currentRecord) {
            $currentId = $currentRecord->id;
            
            switch (get_class($currentRecord)) {
                case Attender::class:
                    $conditions['attenders'] = "AND id != {$currentId}";
                    break;
                case CellMember::class:
                    $conditions['cell_members'] = "AND id != {$currentId}";
                    break;
                case EmergingLeader::class:
                    $conditions['emerging_leaders'] = "AND id != {$currentId}";
                    break;
                case CellLeader::class:
                    $conditions['cell_leaders'] = "AND id != {$currentId}";
                    break;
                case G12Leader::class:
                    $conditions['g12_leaders'] = "AND id != {$currentId}";
                    break;
                case NetworkLeader::class:
                    $conditions['network_leaders'] = "AND id != {$currentId}";
                    break;
            }
        }
        
        return $conditions;
    }
    
    /**
     * Optimized batch check for multiple members
     */
    public static function batchCheckAssignments(array $memberIds): array
    {
        if (empty($memberIds)) {
            return [];
        }
        
        $memberIdsString = implode(',', array_map('intval', $memberIds));
        $results = [];
        
        try {
            $dbResults = DB::select("
                SELECT member_id, 'Attenders' as table_name, 'Attender' as model_name, id as record_id
                FROM attenders WHERE member_id IN ({$memberIdsString})
                
                UNION ALL
                
                SELECT member_id, 'Cell Members' as table_name, 'CellMember' as model_name, id as record_id
                FROM cell_members WHERE member_id IN ({$memberIdsString})
                
                UNION ALL
                
                SELECT member_id, 'Emerging Leaders' as table_name, 'EmergingLeader' as model_name, id as record_id
                FROM emerging_leaders WHERE member_id IN ({$memberIdsString})
                
                UNION ALL
                
                SELECT member_id, 'Cell Leaders' as table_name, 'CellLeader' as model_name, id as record_id
                FROM cell_leaders WHERE member_id IN ({$memberIdsString})
                
                UNION ALL
                
                SELECT member_id, 'G12 Leaders' as table_name, 'G12Leader' as model_name, id as record_id
                FROM g12_leaders WHERE member_id IN ({$memberIdsString})
                
                UNION ALL
                
                SELECT member_id, 'Network Leaders' as table_name, 'NetworkLeader' as model_name, id as record_id
                FROM network_leaders WHERE member_id IN ({$memberIdsString})
            ");
            
            // Group results by member_id
            foreach ($dbResults as $result) {
                if (!isset($results[$result->member_id])) {
                    $results[$result->member_id] = [];
                }
                $results[$result->member_id][] = [
                    'table' => $result->table_name,
                    'model' => $result->model_name,
                    'record_id' => $result->record_id
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Batch assignment check failed: ' . $e->getMessage());
        }
        
        return $results;
    }
    
    /**
     * Get formatted error message for duplicate assignments
     */
    public static function getDuplicationErrorMessage(array $assignments): string
    {
        return MemberDuplicationService::getDuplicationErrorMessage($assignments);
    }
    
    /**
     * Check if member can be assigned to a specific table (optimized)
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
     * Get member's current assignments summary (optimized)
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
    
    /**
     * Get all possible table names for validation
     */
    public static function getAllTableNames(): array
    {
        return MemberDuplicationService::getAllTableNames();
    }
}
