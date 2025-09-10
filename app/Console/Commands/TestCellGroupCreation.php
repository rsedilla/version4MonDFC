<?php

namespace App\Console\Commands;

use App\Models\CellGroup;
use App\Models\CellGroupInfo;
use App\Models\CellGroupType;
use App\Models\CellLeader;
use App\Services\CellGroupIdService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestCellGroupCreation extends Command
{
    protected $signature = 'test:cell-group-creation';

    protected $description = 'Test cell group creation with all required fields';

    public function handle(): int
    {
        $this->info("🧪 Testing Cell Group Creation");
        $this->newLine();

        try {
            // Check if we have any cell leaders
            $cellLeader = CellLeader::with('member')->first();
            if (!$cellLeader) {
                $this->error("❌ No Cell Leaders found. Please create a Cell Leader first.");
                return 1;
            }

            // Check if we have any cell group types
            $cellGroupType = CellGroupType::first();
            if (!$cellGroupType) {
                $this->error("❌ No Cell Group Types found. Please create a Cell Group Type first.");
                return 1;
            }

            $this->info("✅ Found Cell Leader: " . ($cellLeader->member->full_name ?? 'Unknown'));
            $this->info("✅ Found Cell Group Type: {$cellGroupType->name}");
            $this->newLine();

            // Test 1: Create cell group manually
            $this->info("📝 Test 1: Creating Cell Group manually...");

            DB::beginTransaction();

            // Generate ID
            $generatedId = CellGroupIdService::generateCellGroupIdNum();
            $this->info("🔢 Generated ID: {$generatedId}");

            // Create cell group
            $cellGroup = CellGroup::create([
                'name' => 'Test Group ' . now()->format('H:i:s'),
                'leader_id' => $cellLeader->id,
                'leader_type' => 'App\\Models\\CellLeader',
                'cell_group_type_id' => $cellGroupType->id,
                'description' => 'Test group created by command',
                'is_active' => true,
            ]);

            $this->info("✅ Cell Group created with ID: {$cellGroup->id}");

            // Create cell group info
            $cellGroupInfo = CellGroupInfo::create([
                'cell_group_id' => $cellGroup->id,
                'cell_group_idnum' => $generatedId,
                'day' => 'Wednesday',
                'time' => '19:00',
                'location' => 'Test Location',
            ]);

            $this->info("✅ Cell Group Info created with ID: {$cellGroupInfo->id}");

            DB::commit();

            $this->newLine();
            $this->info("🎉 Cell Group created successfully!");
            $this->table(['Field', 'Value'], [
                ['ID', $cellGroup->id],
                ['Name', $cellGroup->name],
                ['Leader', ($cellLeader->member->full_name ?? 'Unknown')],
                ['Type', $cellGroupType->name],
                ['Generated ID', $generatedId],
                ['Day', 'Wednesday'],
                ['Time', '19:00'],
                ['Location', 'Test Location'],
            ]);

            // Test 2: Show what the form data should look like
            $this->newLine();
            $this->info("📋 Test 2: Expected form data structure:");
            
            $expectedFormData = [
                'name' => 'Test Group Name',
                'leader_id' => $cellLeader->id,
                'leader_type' => 'App\\Models\\CellLeader',
                'leader_info' => "CellLeader:{$cellLeader->id}", // Composite key format
                'cell_group_type_id' => $cellGroupType->id,
                'description' => 'Optional description',
                'is_active' => true,
                'info' => [
                    'day' => 'Wednesday',
                    'time' => '19:00',
                    'location' => 'Test Location',
                ]
            ];

            $this->line("Expected form data:");
            $this->line(json_encode($expectedFormData, JSON_PRETTY_PRINT));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
