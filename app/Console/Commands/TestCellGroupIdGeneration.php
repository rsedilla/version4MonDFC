<?php

namespace App\Console\Commands;

use App\Services\CellGroupIdService;
use App\Services\CellGroupLookupService;
use App\Services\CellGroupMemberService;
use Illuminate\Console\Command;

class TestCellGroupIdGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cell-group-id {action=info : Action to perform (info|lookup|search-leader|search-member|members-by-leader)} {search? : Search term for lookup operations} {--leader-id= : Leader ID for members-by-leader action} {--leader-type= : Leader type for members-by-leader action}';

    /**
     * The console description of the console command.
     *
     * @var string
     */
    protected $description = 'Test cell group ID generation and member lookup functionality (uses leader-member relationships)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $search = $this->argument('search');

        switch ($action) {
            case 'info':
                $this->showGenerationInfo();
                break;
                
            case 'lookup':
                if (!$search) {
                    $this->error('Search term is required for lookup action');
                    return 1;
                }
                $this->lookupCellGroup($search);
                break;
                
            case 'search-leader':
                if (!$search) {
                    $this->error('Leader name is required for search-leader action');
                    return 1;
                }
                $this->searchByLeader($search);
                break;
                
            case 'search-member':
                if (!$search) {
                    $this->error('Member name is required for search-member action');
                    return 1;
                }
                $this->searchByMember($search);
                break;
                
            case 'members-by-leader':
                $leaderId = $this->option('leader-id');
                $leaderType = $this->option('leader-type');
                if (!$leaderId || !$leaderType) {
                    $this->error('Both --leader-id and --leader-type options are required for members-by-leader action');
                    return 1;
                }
                $this->showMembersByLeader((int)$leaderId, $leaderType);
                break;
                
            default:
                $this->error('Invalid action. Use: info, lookup, search-leader, search-member, or members-by-leader');
                return 1;
        }

        return 0;
    }

    private function showGenerationInfo()
    {
        $this->info('Cell Group ID Generation Info');
        $this->line('================================');
        
        try {
            $currentCount = CellGroupIdService::getCurrentMonthCount();
            $availableSlots = CellGroupIdService::getAvailableSlots();
            $isLimitReached = CellGroupIdService::isMonthlyLimitReached();
            
            $currentMonth = date('F Y');
            $nextId = date('Ym') . str_pad($currentCount + 1, 3, '0', STR_PAD_LEFT);
            
            $this->table([
                'Property', 'Value'
            ], [
                ['Current Month', $currentMonth],
                ['Next Generated ID', $nextId],
                ['Current Month Count', $currentCount],
                ['Available Slots', $availableSlots],
                ['Monthly Limit Reached', $isLimitReached ? 'Yes' : 'No'],
                ['Format', 'YYYYMM###'],
                ['Max per Month', '300'],
            ]);
            
            // Show example of generating a new ID
            if (!$isLimitReached) {
                $this->info("\nGenerating example ID...");
                $newId = CellGroupIdService::generateCellGroupIdNum();
                $this->line("Generated ID: {$newId}");
                
                // Parse the generated ID
                $parsed = CellGroupIdService::parseIdNum($newId);
                $this->table([
                    'Component', 'Value'
                ], [
                    ['Year', $parsed['year']],
                    ['Month', $parsed['month']],
                    ['Counter', $parsed['counter']],
                    ['Full ID', $parsed['full']],
                ]);
            } else {
                $this->warn('Monthly limit reached - cannot generate new IDs this month');
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    private function lookupCellGroup(string $idNum)
    {
        $this->info("Looking up cell group: {$idNum}");
        $this->line('================================');
        
        try {
            $result = CellGroupLookupService::getCellGroupByIdNum($idNum);
            
            if (!$result) {
                $this->warn('Cell group not found');
                return;
            }
            
            // Display cell group info
            $this->line('Cell Group Information:');
            $this->table([
                'Property', 'Value'
            ], [
                ['ID Number', $result['cell_group_info']['id_number']],
                ['Name', $result['cell_group']['name']],
                ['Type', $result['cell_group']['type']],
                ['Day', $result['cell_group_info']['day']],
                ['Time', $result['cell_group_info']['time']],
                ['Location', $result['cell_group_info']['location']],
                ['Active', $result['cell_group']['is_active'] ? 'Yes' : 'No'],
                ['Created', $result['cell_group']['created_at']],
            ]);
            
            // Display leader info
            if ($result['leader']) {
                $this->line("\nLeader Information:");
                $this->table([
                    'Property', 'Value'
                ], [
                    ['Name', $result['leader']['member']['name']],
                    ['Type', $result['leader']['type']],
                    ['Email', $result['leader']['member']['email']],
                    ['Phone', $result['leader']['member']['phone']],
                ]);
            }
            
            // Display attendees
            if ($result['members']->count() > 0) {
                $this->line("\nMembers ({$result['statistics']['total_members']} total):");
                $membersData = $result['members']->map(function ($member) {
                    return [
                        $member['name'],
                        $member['member_leader_type'] ?? 'N/A',
                        $member['civil_status'] ?? 'N/A',
                        $member['email'] ?? 'N/A',
                    ];
                })->toArray();
                
                $this->table([
                    'Name', 'Member Type', 'Civil Status', 'Email'
                ], $membersData);
            } else {
                $this->line("\nNo members found");
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    private function searchByLeader(string $leaderName)
    {
        $this->info("Searching cell groups by leader: {$leaderName}");
        $this->line('===========================================');
        
        try {
            $results = CellGroupLookupService::searchByLeaderName($leaderName);
            
            if ($results->count() === 0) {
                $this->warn('No cell groups found for this leader');
                return;
            }
            
            $this->line("Found {$results->count()} cell group(s):");
            
            $tableData = $results->map(function ($group) {
                return [
                    $group['id_number'],
                    $group['cell_group_name'],
                    $group['leader_name'],
                    $group['type'],
                    $group['day'],
                    $group['time'],
                    $group['location'],
                ];
            })->toArray();
            
            $this->table([
                'ID Number', 'Group Name', 'Leader', 'Type', 'Day', 'Time', 'Location'
            ], $tableData);
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    private function searchByMember(string $memberName)
    {
        $this->info("Searching cell groups by member: {$memberName}");
        $this->line('===========================================');
        
        try {
            $results = CellGroupLookupService::searchByAttendeeName($memberName);
            
            if ($results->count() === 0) {
                $this->warn('No cell groups found for this member');
                return;
            }
            
            $this->line("Found {$results->count()} cell group(s):");
            
            $tableData = $results->map(function ($group) {
                return [
                    $group['id_number'],
                    $group['cell_group_name'],
                    $group['member_name'],
                    $group['leader_name'],
                    $group['type'],
                    $group['day'],
                    $group['time'],
                    $group['location'],
                ];
            })->toArray();
            
            $this->table([
                'ID Number', 'Group Name', 'Member', 'Leader', 'Type', 'Day', 'Time', 'Location'
            ], $tableData);
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    private function showMembersByLeader(int $leaderId, string $leaderType)
    {
        $this->info("Showing members under leader ID {$leaderId} (Type: {$leaderType})");
        $this->line('================================================================');
        
        try {
            $members = CellGroupMemberService::getMembersByLeader($leaderId, $leaderType);
            
            if ($members->count() === 0) {
                $this->warn('No members found under this leader');
                return;
            }
            
            $this->line("Found {$members->count()} member(s):");
            
            $tableData = $members->map(function ($member) {
                return [
                    $member->full_name,
                    $member->email,
                    $member->phone_number,
                    $member->member_leader_type ?? 'N/A',
                    $member->civilStatus?->name ?? 'N/A',
                    $member->sex?->name ?? 'N/A',
                ];
            })->toArray();
            
            $this->table([
                'Name', 'Email', 'Phone', 'Member Type', 'Civil Status', 'Sex'
            ], $tableData);
            
            // Also show if this leader has any cell groups assigned
            $cellGroups = CellGroupMemberService::getCellGroupsByLeader($leaderId, $leaderType);
            if ($cellGroups->count() > 0) {
                $this->line("\nCell Groups assigned to this leader:");
                $groupData = $cellGroups->map(function ($group) {
                    return [
                        $group['id_number'],
                        $group['name'],
                        $group['type'],
                        $group['day'],
                        $group['time'],
                        $group['location'],
                        $group['is_active'] ? 'Yes' : 'No',
                    ];
                })->toArray();
                
                $this->table([
                    'ID Number', 'Group Name', 'Type', 'Day', 'Time', 'Location', 'Active'
                ], $groupData);
            } else {
                $this->line("\nNo cell groups assigned to this leader yet.");
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
