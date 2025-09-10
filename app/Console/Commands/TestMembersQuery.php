<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Filament\Resources\Members\MemberResource;

class TestMembersQuery extends Command
{
    protected $signature = 'test:members-query';
    protected $description = 'Test the exact query used by ListMembers page';

    public function handle()
    {
        $this->info('🔍 Testing ListMembers Query');
        
        // Get the exact query used by the ListMembers page
        $query = MemberResource::getEloquentQuery()
            ->withoutGlobalScopes()
            ->with([
                'trainingTypes:id,name',
                'directLeader.member:id,first_name,middle_name,last_name'
            ])
            ->orderBy('updated_at', 'desc');
            
        // Get Albert Castro
        $albert = $query->where('first_name', 'Albert')
            ->where('last_name', 'Castro')
            ->first();
            
        if (!$albert) {
            $this->error('❌ Albert Castro not found');
            return;
        }
        
        $this->info("✅ Found: {$albert->first_name} {$albert->last_name}");
        $this->info("Updated: {$albert->updated_at}");
        $this->info("Leader Type: {$albert->leader_type}");
        $this->info("Leader ID: {$albert->leader_id}");
        
        // Test relationships
        $this->info("DirectLeader loaded: " . ($albert->relationLoaded('directLeader') ? 'YES' : 'NO'));
        
        if ($albert->directLeader) {
            $this->info("✅ DirectLeader exists: " . get_class($albert->directLeader));
            if ($albert->directLeader->member) {
                $this->info("✅ Leader member: {$albert->directLeader->member->full_name}");
                
                // Test the exact column formatting
                $display = '👤 ' . $albert->directLeader->member->full_name;
                $this->info("📋 Column would display: [{$display}]");
            } else {
                $this->error("❌ Leader member is null");
            }
        } else {
            $this->error("❌ DirectLeader is null");
        }
        
        // Test a few more recent members
        $this->info("\n🔍 Recent members (top 5):");
        $recentMembers = $query->limit(5)->get();
        foreach ($recentMembers as $member) {
            $leaderDisplay = 'None assigned';
            if ($member->directLeader && $member->directLeader->member) {
                $leaderDisplay = '👤 ' . $member->directLeader->member->full_name;
            }
            $this->info("{$member->first_name} {$member->last_name} -> {$leaderDisplay}");
        }
    }
}
