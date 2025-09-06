<?php

namespace App\Traits;

use App\Models\Attender;
use App\Models\CellLeader;
use App\Models\CellMember;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use App\Models\SeniorPastor;
use Illuminate\Support\Facades\DB;

trait ManagesLeaderTypeTransitions
{
    /**
     * Transfer member from one leader type to another
     * This handles moving data between tables and cleaning up old records
     */
    public function transferLeaderType(string $newLeaderType): bool
    {
        $oldLeaderType = $this->leader_type;
        
        if ($oldLeaderType === $newLeaderType) {
            return true; // No change needed
        }

        // Start transaction to ensure data integrity
        DB::transaction(function () use ($oldLeaderType, $newLeaderType) {
            // For new members (no old leader type), just create new records
            if (empty($oldLeaderType)) {
                $this->createLeaderTypeRecord($newLeaderType);
                $this->updateQuietly(['leader_type' => $newLeaderType]);
            } else {
                // For transfers, create new record first (to preserve references)
                $this->createLeaderTypeRecord($newLeaderType);
                
                // Clean up old leader records
                $this->cleanupLeaderTypeRecord($oldLeaderType);
                
                // Update the member's leader_type
                $this->updateQuietly(['leader_type' => $newLeaderType]);
            }
        });

        return true;
    }

    /**
     * Clean up old leader type records
     */
    private function cleanupLeaderTypeRecord(?string $leaderType = null): void
    {
        // Always delete from all leader tables for exclusivity
        Attender::where('member_id', $this->id)->delete();
        CellMember::where('member_id', $this->id)->delete();
        CellLeader::where('member_id', $this->id)->delete();
        G12Leader::where('member_id', $this->id)->delete();
        NetworkLeader::where('member_id', $this->id)->delete();
        SeniorPastor::where('member_id', $this->id)->delete();
    }

    /**
     * Create new leader type record
     */
    private function createLeaderTypeRecord(string $leaderType): void
    {
        switch ($leaderType) {
            case 'App\\Models\\Attender':
                Attender::firstOrCreate(
                    ['member_id' => $this->id],
                    [
                        'leader_id' => $this->leader_id,
                        'leader_type' => $this->leader_type,
                    ]
                );
                break;

            case 'App\\Models\\CellMember':
                $attender = Attender::where('member_id', $this->id)->first();
                CellMember::firstOrCreate(
                    ['member_id' => $this->id],
                    [
                        'attender_id' => $attender ? $attender->id : null,
                        'cell_group_id' => null,
                        'joined_date' => now(),
                        'status' => 'active',
                    ]
                );
                break;

            case 'App\\Models\\CellLeader':
                $cellLeader = CellLeader::firstOrCreate(
                    ['member_id' => $this->id],
                    []
                );
                $this->updateQuietly(['leader_id' => $cellLeader->id]);
                break;

            case 'App\\Models\\G12Leader':
                G12Leader::firstOrCreate(
                    ['member_id' => $this->id],
                    []
                );
                break;

            case 'App\\Models\\NetworkLeader':
                NetworkLeader::firstOrCreate(
                    ['member_id' => $this->id],
                    []
                );
                break;

            case 'App\\Models\\SeniorPastor':
                SeniorPastor::firstOrCreate(
                    ['member_id' => $this->id],
                    []
                );
                break;
        }
    }

    /**
     * Get available leader types for transition
     */
    public static function getAvailableLeaderTypes(): array
    {
        return [
            'App\\Models\\Attender' => 'Attender',
            'App\\Models\\CellMember' => 'Cell Member',
            'App\\Models\\CellLeader' => 'Cell Leader',
            'App\\Models\\G12Leader' => 'G12 Leader',
            'App\\Models\\NetworkLeader' => 'Network Leader',
            'App\\Models\\SeniorPastor' => 'Senior Pastor',
        ];
    }

    /**
     * Get current leader type display name
     */
    public function getLeaderTypeDisplayName(): string
    {
        $types = self::getAvailableLeaderTypes();
        return $types[$this->leader_type] ?? 'Unknown';
    }

    /**
     * Check if member can be transferred to a specific leader type
     */
    public function canTransferTo(string $leaderType): bool
    {
        // Add any business logic here to prevent invalid transitions
        // For example, maybe an Attender can't directly become a Senior Pastor
        
        if ($this->leader_type === $leaderType) {
            return false; // Can't transfer to same type
        }

        // Add custom validation rules here
        // Example: 
        // if ($this->leader_type === 'App\\Models\\Attender' && $leaderType === 'App\\Models\\SeniorPastor') {
        //     return false; // Attender can't directly become Senior Pastor
        // }

        return true;
    }
}
