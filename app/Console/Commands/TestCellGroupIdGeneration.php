<?php

namespace App\Console\Commands;

use App\Services\CellGroupIdService;
use App\Services\CellGroupLookupService;
use Illuminate\Console\Command;

class TestCellGroupIdGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cell-group-id {action=info : Action to perform (info|lookup|search-leader|search-attendee)} {search? : Search term for lookup operations}';

    /**
     * The console description of the console command.
     *
     * @var string
     */
    protected $description = 'Test cell group ID generation and lookup functionality';

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
                
            case 'search-attendee':
                if (!$search) {
                    $this->error('Attendee name is required for search-attendee action');
                    return 1;
                }
                $this->searchByAttendee($search);
                break;
                
            default:
                $this->error('Invalid action. Use: info, lookup, search-leader, or search-attendee');
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
            if ($result['attendees']->count() > 0) {
                $this->line("\nAttendees ({$result['statistics']['total_attendees']} total):");
                $attendeesData = $result['attendees']->map(function ($attendee) {
                    return [
                        $attendee['name'],
                        $attendee['type'],
                        $attendee['status'] ?? 'N/A',
                        $attendee['joined_date'] ?? 'N/A',
                    ];
                })->toArray();
                
                $this->table([
                    'Name', 'Type', 'Status', 'Joined Date'
                ], $attendeesData);
            } else {
                $this->line("\nNo attendees found");
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

    private function searchByAttendee(string $attendeeName)
    {
        $this->info("Searching cell groups by attendee: {$attendeeName}");
        $this->line('=============================================');
        
        try {
            $results = CellGroupLookupService::searchByAttendeeName($attendeeName);
            
            if ($results->count() === 0) {
                $this->warn('No cell groups found for this attendee');
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
}
