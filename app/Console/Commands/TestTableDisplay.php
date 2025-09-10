<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;

class TestTableDisplay extends Command
{
    protected $signature = 'test:table-display';
    protected $description = 'Test what the table would display for members';

    public function handle()
    {
        $this->info('🔍 Testing Table Display Logic');
        
        // Get Albert Castro with the same query as the table
        $member = Member::with(['directLeader.member'])
            ->withoutGlobalScopes()
            ->where('first_name', 'Albert')
            ->where('last_name', 'Castro')
            ->first();
            
        if (!$member) {
            $this->error('❌ Albert Castro not found');
            return;
        }
        
        $this->info("✅ Found: {$member->first_name} {$member->last_name}");
        $this->info("Leader Type: {$member->leader_type}");
        $this->info("Leader ID: {$member->leader_id}");
        
        // Test the exact logic from the table column
        $directLeaderDisplay = $this->getDirectLeaderDisplay($member);
        
        $this->info("Direct Leader Display: [{$directLeaderDisplay}]");
        
        // Test a few other members to see if the issue is specific to Albert
        $this->info("\n🔍 Testing other members:");
        $otherMembers = Member::with(['directLeader.member'])
            ->withoutGlobalScopes()
            ->whereNotNull('leader_type')
            ->whereNotNull('leader_id')
            ->limit(3)
            ->get();
            
        foreach ($otherMembers as $member) {
            $display = $this->getDirectLeaderDisplay($member);
            $this->info("{$member->first_name} {$member->last_name} -> [{$display}]");
        }
    }
    
    private function getDirectLeaderDisplay($record)
    {
        if (!$record->leader_type || !$record->leader_id) {
            return 'None assigned';
        }
        
        // Use preloaded relationship first
        if ($record->relationLoaded('directLeader') && $record->directLeader) {
            if ($record->directLeader->member) {
                return $record->directLeader->member->full_name;
            }
        }
        
        // Fallback to direct query
        try {
            $leaderClass = $record->leader_type;
            if (class_exists($leaderClass)) {
                $leader = $leaderClass::with('member')->find($record->leader_id);
                if ($leader && $leader->member) {
                    return $leader->member->full_name;
                }
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
        
        return 'Error loading leader';
    }
}
