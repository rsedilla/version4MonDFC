<?php

namespace App\Console\Commands;

use App\Models\CellGroup;
use App\Models\CellGroupInfo;
use Illuminate\Console\Command;

class CheckCellGroupTables extends Command
{
    protected $signature = 'check:cell-group-tables';
    protected $description = 'Check the state of cell_groups and cell_group_infos tables';

    public function handle()
    {
        $this->info('🔍 Checking Cell Group Tables State');
        $this->newLine();
        
        // Check cell_groups table
        $cellGroups = CellGroup::all();
        $this->info('📊 Cell Groups Table:');
        $this->line("Total records: " . $cellGroups->count());
        
        if ($cellGroups->count() > 0) {
            $this->table(
                ['ID', 'Name', 'Leader ID', 'Leader Type', 'Active', 'Created'],
                $cellGroups->map(function ($cg) {
                    return [
                        $cg->id,
                        $cg->name,
                        $cg->leader_id,
                        class_basename($cg->leader_type),
                        $cg->is_active ? 'Yes' : 'No',
                        $cg->created_at->format('Y-m-d H:i')
                    ];
                })->toArray()
            );
        }
        
        $this->newLine();
        
        // Check cell_group_infos table
        $cellGroupInfos = CellGroupInfo::all();
        $this->info('📊 Cell Group Infos Table:');
        $this->line("Total records: " . $cellGroupInfos->count());
        
        if ($cellGroupInfos->count() > 0) {
            $this->table(
                ['ID', 'Cell Group ID', 'ID Num', 'Day', 'Time', 'Location'],
                $cellGroupInfos->map(function ($cgi) {
                    return [
                        $cgi->id,
                        $cgi->cell_group_id,
                        $cgi->cell_group_idnum,
                        $cgi->day,
                        $cgi->time ? $cgi->time->format('H:i') : null,
                        $cgi->location
                    ];
                })->toArray()
            );
        }
        
        $this->newLine();
        
        // Check orphaned records
        $orphanedCellGroups = CellGroup::doesntHave('info')->get();
        if ($orphanedCellGroups->count() > 0) {
            $this->warn('⚠️  Cell Groups without Info records: ' . $orphanedCellGroups->count());
            foreach ($orphanedCellGroups as $cg) {
                $this->line("  - ID {$cg->id}: {$cg->name}");
            }
        } else {
            $this->info('✅ All Cell Groups have corresponding Info records');
        }
        
        $orphanedInfos = CellGroupInfo::doesntHave('cellGroup')->get();
        if ($orphanedInfos->count() > 0) {
            $this->warn('⚠️  Info records without Cell Groups: ' . $orphanedInfos->count());
            foreach ($orphanedInfos as $info) {
                $this->line("  - Info ID {$info->id} references Cell Group ID {$info->cell_group_id}");
            }
        } else {
            $this->info('✅ All Info records have corresponding Cell Groups');
        }
        
        return 0;
    }
}
