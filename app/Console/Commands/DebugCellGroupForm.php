<?php

namespace App\Console\Commands;

use App\Traits\HasButtonLeaderSearch;
use Illuminate\Console\Command;

class DebugCellGroupForm extends Command
{
    use HasButtonLeaderSearch;
    
    protected $signature = 'debug:cell-group-form';
    protected $description = 'Debug the cell group form structure and field generation';

    public function handle()
    {
        $this->info('🔍 Debugging Cell Group Form Structure');
        $this->newLine();
        
        try {
            // Test the unified leader select method
            $components = self::unifiedLeaderSelect('leader_info', '👤 Select Leader');
            
            $this->info('✅ Form Components Generated: ' . count($components));
            $this->newLine();
            
            foreach ($components as $index => $component) {
                $componentClass = get_class($component);
                $componentName = $component->getName();
                $isRequired = method_exists($component, 'isRequired') ? ($component->isRequired() ? 'YES' : 'NO') : 'N/A';
                $isDehydrated = method_exists($component, 'isDehydrated') ? ($component->isDehydrated() ? 'YES' : 'NO') : 'N/A';
                
                $this->line("Component #{$index}:");
                $this->line("  - Class: {$componentClass}");
                $this->line("  - Name: {$componentName}");
                $this->line("  - Required: {$isRequired}");
                $this->line("  - Dehydrated (saved): {$isDehydrated}");
                $this->newLine();
            }
            
            // Test the options generation
            $this->info('🎯 Testing Options Generation:');
            
            // Simulate the options function
            $options = [];
            
            // Add Cell Leaders
            $cellLeaders = \App\Models\CellLeader::with('member')->get();
            foreach ($cellLeaders as $leader) {
                if ($leader->member) {
                    $key = "CellLeader:{$leader->id}";
                    $label = $leader->member->first_name . ' ' . $leader->member->last_name . ' (Cell Leader)';
                    $options[$key] = $label;
                }
            }
            
            // Add G12 Leaders
            $g12Leaders = \App\Models\G12Leader::with('member')->get();
            foreach ($g12Leaders as $leader) {
                if ($leader->member) {
                    $key = "G12Leader:{$leader->id}";
                    $label = $leader->member->first_name . ' ' . $leader->member->last_name . ' (G12 Leader)';
                    $options[$key] = $label;
                }
            }
            
            // Add Network Leaders  
            $networkLeaders = \App\Models\NetworkLeader::with('member')->get();
            foreach ($networkLeaders as $leader) {
                if ($leader->member) {
                    $key = "NetworkLeader:{$leader->id}";
                    $label = $leader->member->first_name . ' ' . $leader->member->last_name . ' (Network Leader)';
                    $options[$key] = $label;
                }
            }
            
            $this->line("Total options available: " . count($options));
            
            if (count($options) > 0) {
                $this->line("Sample options:");
                $count = 0;
                foreach ($options as $key => $label) {
                    $this->line("  {$key} => {$label}");
                    $count++;
                    if ($count >= 5) break;
                }
            }
            
            $this->newLine();
            $this->info('✅ Debug complete. If options are empty, there might be no leaders in the database.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error during debug: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
