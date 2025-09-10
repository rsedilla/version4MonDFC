<?php

namespace App\Console\Commands;

use App\Filament\Helpers\DirectLeaderActionHelper;
use App\Models\Member;
use Illuminate\Console\Command;

class DebugDirectLeaderAction extends Command
{
    protected $signature = 'debug:direct-leader-action {member_id?}';
    protected $description = 'Debug the DirectLeaderActionHelper functionality';

    public function handle()
    {
        $memberId = $this->argument('member_id') ?? 1; // Default to Albert Castro
        
        $this->info('🔍 Debugging DirectLeaderActionHelper');
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
            $this->line("Current leader_id: " . ($member->leader_id ?? 'NULL'));
            $this->line("Current leader_type: " . ($member->leader_type ?? 'NULL'));
            $this->newLine();
            
            // Test the getDirectLeaderOptions method
            $this->info("🔧 Testing getDirectLeaderOptions():");
            $options = DirectLeaderActionHelper::getDirectLeaderOptions();
            $this->line("Total options: " . count($options));
            
            // Show first 5 options
            $this->table(['Key', 'Value'], array_slice(array_map(function($key, $value) {
                return [$key, $value];
            }, array_keys($options), array_values($options)), 0, 5));
            
            $this->newLine();
            
            // Test the fillForm method logic
            $this->info("🔧 Testing form fill logic:");
            if ($member->leader_type && $member->leader_id) {
                $key = $member->leader_type . ':' . $member->leader_id;
                $this->line("Expected key: {$key}");
                $this->line("Key exists in options: " . (array_key_exists($key, $options) ? 'YES' : 'NO'));
                
                if (array_key_exists($key, $options)) {
                    $this->info("✅ Form should fill with: {$options[$key]}");
                } else {
                    $this->warn("⚠️  Key not found in options - form will be empty");
                    
                    // Check if leader exists in database
                    $leaderModel = $member->leader_type;
                    if (class_exists($leaderModel)) {
                        $leader = $leaderModel::with('member')->find($member->leader_id);
                        if ($leader) {
                            $this->line("Leader exists in DB: {$leader->member->full_name}");
                        } else {
                            $this->error("Leader does not exist in DB");
                        }
                    }
                }
            } else {
                $this->warn("⚠️  Member has no leader assigned");
            }
            
            $this->newLine();
            
            // Test action logic simulation
            $this->info("🔧 Testing action logic simulation:");
            $testData = ['direct_leader' => 'App\\Models\\NetworkLeader:10']; // Raymond
            
            if (!empty($testData['direct_leader']) && str_contains($testData['direct_leader'], ':')) {
                [$leaderType, $leaderId] = explode(':', $testData['direct_leader']);
                
                $this->line("Parsed leader_type: {$leaderType}");
                $this->line("Parsed leader_id: {$leaderId}");
                
                // Test getting leader name for notification
                if (class_exists($leaderType)) {
                    $leaderModel = app($leaderType)->find($leaderId);
                    if ($leaderModel && $leaderModel->member) {
                        $leaderName = $leaderModel->member->full_name;
                        $this->info("✅ Leader name for notification: {$leaderName}");
                    } else {
                        $this->error("❌ Could not get leader name");
                    }
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
