<?php

namespace App\Filament\Helpers;

use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use App\Models\SeniorPastor;

class CellGroupLeaderHelper
{
    /**
     * Get all available leader options for cell groups
     */
    public static function getLeaderOptions(): array
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
