<?php

namespace App\Console\Commands;

use App\Models\CellGroup;
use App\Services\LeaderSearchService;
use Illuminate\Console\Command;

class TestEditFormLoading extends Command
{
    protected $signature = 'test:edit-form-loading {cell_group_id?}';
    protected $description = 'Test the edit form data loading logic';

    public function handle()
    {
        $cellGroupId = $this->argument('cell_group_id') ?? 2; // Default to the TKD group
        
        $this->info('🔍 Testing Edit Form Data Loading');
        $this->line("Cell Group ID: {$cellGroupId}");
        $this->newLine();
        
        try {
            // Find the cell group
            $cellGroup = CellGroup::with('info')->find($cellGroupId);
            
            if (!$cellGroup) {
                $this->error("❌ Cell Group with ID {$cellGroupId} not found");
                return 1;
            }
            
            $this->info("📋 Cell Group: {$cellGroup->name}");
            $this->line("Leader ID: {$cellGroup->leader_id}");
            $this->line("Leader Type: {$cellGroup->leader_type}");
            $this->newLine();
            
            // Test the form data preparation
            $data = $cellGroup->toArray();
            
            // Simulate the mutateFormDataBeforeFill logic
            if ($cellGroup->info) {
                $data['info'] = [
                    'day' => $cellGroup->info->day,
                    'time' => $cellGroup->info->time?->format('H:i'),
                    'location' => $cellGroup->info->location,
                    'cell_group_idnum' => $cellGroup->info->cell_group_idnum,
                ];
                
                $this->info("✅ Info Record Found:");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Day', $data['info']['day'] ?? 'NULL'],
                        ['Time', $data['info']['time'] ?? 'NULL'],
                        ['Location', $data['info']['location'] ?? 'NULL'],
                        ['ID Number', $data['info']['cell_group_idnum'] ?? 'NULL'],
                    ]
                );
            } else {
                $this->warn("⚠️  No Info Record Found");
                $data['info'] = [
                    'day' => null,
                    'time' => null,
                    'location' => null,
                    'cell_group_idnum' => null,
                ];
            }
            
            // Test leader composite key creation
            if ($cellGroup->leader_id && $cellGroup->leader_type) {
                $modelShortName = class_basename($cellGroup->leader_type);
                $compositeKey = "{$modelShortName}:{$cellGroup->leader_id}";
                $data['leader_info'] = $compositeKey;
                
                $this->info("✅ Leader Composite Key: {$compositeKey}");
                
                // Test if the leader search service can find this leader
                $leaderSearchService = app(LeaderSearchService::class);
                $leaderLabel = $leaderSearchService->getLeaderLabel($compositeKey);
                
                if ($leaderLabel) {
                    $this->info("✅ Leader Label: {$leaderLabel}");
                } else {
                    $this->warn("⚠️  Could not get leader label for composite key");
                }
            } else {
                $this->warn("⚠️  No leader information available");
            }
            
            $this->newLine();
            $this->info('📝 Form data that would be loaded:');
            $this->line('- name: ' . ($data['name'] ?? 'NULL'));
            $this->line('- leader_info: ' . ($data['leader_info'] ?? 'NULL'));
            $this->line('- cell_group_type_id: ' . ($data['cell_group_type_id'] ?? 'NULL'));
            $this->line('- info.day: ' . ($data['info']['day'] ?? 'NULL'));
            $this->line('- info.time: ' . ($data['info']['time'] ?? 'NULL'));
            $this->line('- info.location: ' . ($data['info']['location'] ?? 'NULL'));
            $this->line('- is_active: ' . ($data['is_active'] ? 'true' : 'false'));
            
            $this->newLine();
            $this->info('✅ Edit form loading test completed!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
