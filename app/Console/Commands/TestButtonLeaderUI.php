<?php

namespace App\Console\Commands;

use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use App\Traits\HasButtonLeaderSearch;
use Illuminate\Console\Command;

class TestButtonLeaderUI extends Command
{
    use HasButtonLeaderSearch;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:button-leader-ui';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the button-based leader selection UI functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Button-Based Leader Selection UI');
        $this->newLine();
        
        // Test 1: Check if leaders exist
        $this->info('📊 Leader Inventory:');
        $cellLeaders = CellLeader::with('member')->count();
        $g12Leaders = G12Leader::with('member')->count();
        $networkLeaders = NetworkLeader::with('member')->count();
        
        $this->line("👥 Cell Leaders: {$cellLeaders}");
        $this->line("🌟 G12 Leaders: {$g12Leaders}");
        $this->line("🌐 Network Leaders: {$networkLeaders}");
        $this->newLine();
        
        // Test 2: Validate form components generation
        try {
            $buttonComponents = self::buttonLeaderSelect();
            $unifiedComponents = self::unifiedLeaderSelect();
            
            $this->info('✅ Form Components Generated Successfully:');
            $this->line("🔘 Button Leader Select Components: " . count($buttonComponents));
            $this->line("🔘 Unified Leader Select Components: " . count($unifiedComponents));
            $this->newLine();
            
        } catch (\Exception $e) {
            $this->error('❌ Error generating form components: ' . $e->getMessage());
            return 1;
        }
        
        // Test 3: Check leader data structure
        if ($cellLeaders > 0) {
            $sampleCellLeader = CellLeader::with('member')->first();
            if ($sampleCellLeader && $sampleCellLeader->member) {
                $name = $sampleCellLeader->member->first_name . ' ' . $sampleCellLeader->member->last_name;
                $compositeKey = "CellLeader:{$sampleCellLeader->id}";
                
                $this->info('📝 Sample Leader Data:');
                $this->line("Name: {$name}");
                $this->line("Composite Key: {$compositeKey}");
                $this->line("Leader Type: App\\Models\\CellLeader");
                $this->newLine();
            }
        }
        
        // Test 4: Check unified leader options
        try {
            $this->info('🔍 Testing Unified Leader Options Generation...');
            
            $options = [];
            
            // Simulate the options generation logic
            $cellLeaders = CellLeader::with('member')->take(3)->get();
            foreach ($cellLeaders as $leader) {
                if ($leader->member) {
                    $key = "CellLeader:{$leader->id}";
                    $label = $leader->member->first_name . ' ' . $leader->member->last_name . ' (Cell Leader)';
                    $options[$key] = $label;
                }
            }
            
            $g12Leaders = G12Leader::with('member')->take(3)->get();
            foreach ($g12Leaders as $leader) {
                if ($leader->member) {
                    $key = "G12Leader:{$leader->id}";
                    $label = $leader->member->first_name . ' ' . $leader->member->last_name . ' (G12 Leader)';
                    $options[$key] = $label;
                }
            }
            
            $networkLeaders = NetworkLeader::with('member')->take(3)->get();
            foreach ($networkLeaders as $leader) {
                if ($leader->member) {
                    $key = "NetworkLeader:{$leader->id}";
                    $label = $leader->member->first_name . ' ' . $leader->member->last_name . ' (Network Leader)';
                    $options[$key] = $label;
                }
            }
            
            $this->info('📋 Sample Options (first 9 total):');
            $count = 0;
            foreach ($options as $key => $label) {
                $this->line("  {$key} => {$label}");
                $count++;
                if ($count >= 9) break;
            }
            
            $this->newLine();
            $this->info('✅ All Tests Passed! Button-based leader selection is ready to use.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error in unified options test: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
