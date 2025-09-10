<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;

class TestMemberTableDisplay extends Command
{
    protected $signature = 'test:member-table-display {member_id?}';
    protected $description = 'Test how the member table displays leader information';

    public function handle()
    {
        $memberId = $this->argument('member_id') ?? 31; // Default to Bon Fran
        
        $this->info('🔍 Testing Member Table Display');
        $this->line("Member ID: {$memberId}");
        $this->newLine();
        
        try {
            // Get the member
            $member = Member::find($memberId);
            if (!$member) {
                $this->error("❌ Member with ID {$memberId} not found");
                return 1;
            }
            
            $this->info("✅ Member: {$member->full_name}");
            $this->line("leader_id: " . ($member->leader_id ?? 'NULL'));
            $this->line("leader_type: " . ($member->leader_type ?? 'NULL'));
            $this->newLine();
            
            // Test the table column logic
            $this->info("🔧 Testing Table Column Logic:");
            
            if (!$member->leader_type || !$member->leader_id) {
                $displayValue = 'None assigned';
                $this->line("Result: {$displayValue}");
            } else {
                // Force fresh relationship loading (same as table does)
                $member->load(['directLeader.member']);
                
                $leader = $member->directLeader;
                if ($leader && $leader->member) {
                    $memberLeader = $leader->member;
                    $middleInitial = $memberLeader->middle_name ? strtoupper(substr($memberLeader->middle_name, 0, 1)) . '.' : '';
                    $displayValue = trim($memberLeader->first_name . ' ' . $middleInitial . ' ' . $memberLeader->last_name);
                    $this->info("✅ Table should show: {$displayValue}");
                } else {
                    $this->warn("⚠️  Relationship not loading, trying fallback...");
                    
                    // Fallback: try to get leader directly from database
                    try {
                        $leaderClass = $member->leader_type;
                        if (class_exists($leaderClass)) {
                            $leader = $leaderClass::with('member')->find($member->leader_id);
                            if ($leader && $leader->member) {
                                $memberLeader = $leader->member;
                                $middleInitial = $memberLeader->middle_name ? strtoupper(substr($memberLeader->middle_name, 0, 1)) . '.' : '';
                                $displayValue = trim($memberLeader->first_name . ' ' . $middleInitial . ' ' . $memberLeader->last_name);
                                $this->info("✅ Fallback shows: {$displayValue}");
                            } else {
                                $this->error("❌ Leader not found in database");
                            }
                        } else {
                            $this->error("❌ Leader class does not exist: {$leaderClass}");
                        }
                    } catch (\Exception $e) {
                        $this->error("❌ Fallback error: " . $e->getMessage());
                    }
                }
            }
            
            $this->newLine();
            
            // Test the search functionality that the table uses
            $this->info("🔧 Testing Table Search Query:");
            if ($member->leader_type && $member->leader_id) {
                $searchTest = Member::whereHas('directLeader.member', function ($query) {
                    $query->where('first_name', 'like', "%Raymond%")
                          ->orWhere('last_name', 'like', "%Raymond%");
                })->where('id', $member->id)->exists();
                
                $this->line("Search for 'Raymond' finds this member: " . ($searchTest ? 'YES' : 'NO'));
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
