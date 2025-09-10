<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\NetworkLeader;
use Illuminate\Console\Command;

class TestMemberLeaderAssignment extends Command
{
    protected $signature = 'test:member-leader-assignment {member_name?} {leader_name?}';
    protected $description = 'Test member leader assignment functionality';

    public function handle()
    {
        $memberName = $this->argument('member_name') ?? 'Bon FRAN';
        $leaderName = $this->argument('leader_name') ?? 'Raymond';
        
        $this->info('🔍 Testing Member Leader Assignment');
        $this->line("Looking for member: '{$memberName}'");
        $this->line("Looking for leader: '{$leaderName}'");
        $this->newLine();
        
        try {
            // Find the member
            $member = Member::where('first_name', 'like', "%{$memberName}%")
                           ->orWhere('last_name', 'like', "%{$memberName}%")
                           ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$memberName}%"])
                           ->first();
            
            if (!$member) {
                $this->error("❌ Member '{$memberName}' not found");
                
                // Show available members
                $this->warn("Available Members (first 10):");
                $availableMembers = Member::select('id', 'first_name', 'last_name', 'leader_id', 'leader_type')
                    ->limit(10)->get();
                foreach ($availableMembers as $availableMember) {
                    $leaderInfo = $availableMember->leader_id ? 
                        "Leader: {$availableMember->leader_id} ({$availableMember->leader_type})" : 
                        "No Leader";
                    $this->line("  - {$availableMember->full_name} (ID: {$availableMember->id}) - {$leaderInfo}");
                }
                return 1;
            }
            
            $this->info("✅ Found Member: {$member->full_name} (ID: {$member->id})");
            $this->line("Current leader_id: " . ($member->leader_id ?? 'NULL'));
            $this->line("Current leader_type: " . ($member->leader_type ?? 'NULL'));
            $this->newLine();
            
            // Find the leader
            $leader = null;
            $leaders = NetworkLeader::with('member')
                ->whereHas('member', function ($query) use ($leaderName) {
                    $query->where('first_name', 'like', "%{$leaderName}%")
                          ->orWhere('last_name', 'like', "%{$leaderName}%")
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$leaderName}%"]);
                })->get();
            
            if ($leaders->count() > 0) {
                $leader = $leaders->first();
                $this->info("✅ Found Leader: {$leader->member->full_name} (ID: {$leader->id})");
                $this->line("Leader Type: " . get_class($leader));
                $this->newLine();
                
                // Test assignment
                $this->info("🔧 Testing Assignment...");
                $member->leader_id = $leader->id;
                $member->leader_type = get_class($leader);
                $saved = $member->save();
                
                if ($saved) {
                    $this->info("✅ Assignment saved successfully!");
                    
                    // Reload and verify
                    $member->refresh();
                    $this->table(
                        ['Field', 'Value'],
                        [
                            ['leader_id', $member->leader_id],
                            ['leader_type', $member->leader_type],
                            ['DirectLeader Loaded', $member->directLeader ? 'YES' : 'NO'],
                            ['DirectLeader Name', $member->directLeader?->member?->full_name ?? 'NULL']
                        ]
                    );
                    
                    // Test the relationship
                    if ($member->directLeader) {
                        $this->info("✅ Relationship working: " . $member->directLeader->member->full_name);
                    } else {
                        $this->warn("⚠️  Relationship not loading properly");
                    }
                } else {
                    $this->error("❌ Failed to save assignment");
                }
            } else {
                $this->error("❌ Leader '{$leaderName}' not found");
                
                // Show available leaders
                $this->warn("Available Network Leaders:");
                $allLeaders = NetworkLeader::with('member')->limit(5)->get();
                foreach ($allLeaders as $availableLeader) {
                    $this->line("  - {$availableLeader->member->full_name} (ID: {$availableLeader->id})");
                }
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
