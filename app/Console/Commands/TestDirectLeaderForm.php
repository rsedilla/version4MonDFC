<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestDirectLeaderForm extends Command
{
    protected $signature = 'test:direct-leader-form';
    protected $description = 'Test the direct leader form fields';

    public function handle()
    {
        $this->info('🧪 Testing Direct Leader Form');
        $this->newLine();
        
        // Test options generation
        $options = [];
        
        // Add Cell Leaders
        $cellLeaders = \App\Models\CellLeader::with('member')->get();
        foreach ($cellLeaders as $leader) {
            if ($leader->member) {
                $key = "CellLeader:{$leader->id}";
                $name = $leader->member->first_name . ' ' . $leader->member->last_name;
                $options[$key] = "👥 {$name} (Cell Leader)";
            }
        }
        
        // Add G12 Leaders
        $g12Leaders = \App\Models\G12Leader::with('member')->get();
        foreach ($g12Leaders as $leader) {
            if ($leader->member) {
                $key = "G12Leader:{$leader->id}";
                $name = $leader->member->first_name . ' ' . $leader->member->last_name;
                $options[$key] = "🌟 {$name} (G12 Leader)";
            }
        }
        
        // Add Network Leaders
        $networkLeaders = \App\Models\NetworkLeader::with('member')->get();
        foreach ($networkLeaders as $leader) {
            if ($leader->member) {
                $key = "NetworkLeader:{$leader->id}";
                $name = $leader->member->first_name . ' ' . $leader->member->last_name;
                $options[$key] = "🌐 {$name} (Network Leader)";
            }
        }
        
        $this->info("📊 Available Leaders: " . count($options));
        $this->newLine();
        
        if (count($options) > 0) {
            $this->info("📋 Sample Options:");
            $count = 0;
            foreach ($options as $key => $label) {
                $this->line("  {$key} => {$label}");
                $count++;
                if ($count >= 5) break;
            }
            $this->newLine();
        }
        
        // Test parsing logic
        $this->info("🔧 Testing Parsing Logic:");
        $testKey = array_key_first($options);
        if ($testKey) {
            $this->line("Test key: {$testKey}");
            
            if (str_contains($testKey, ':')) {
                [$modelType, $id] = explode(':', $testKey, 2);
                
                $leaderType = match ($modelType) {
                    'CellLeader' => 'App\\Models\\CellLeader',
                    'G12Leader' => 'App\\Models\\G12Leader',
                    'NetworkLeader' => 'App\\Models\\NetworkLeader',
                    default => null
                };
                
                $this->line("Parsed - Model: {$modelType}, ID: {$id}, Type: {$leaderType}");
                
                if ($leaderType && is_numeric($id)) {
                    $this->info("✅ Parsing successful - leader_id={$id}, leader_type={$leaderType}");
                } else {
                    $this->error("❌ Parsing failed");
                }
            }
        }
        
        $this->newLine();
        $this->info("✅ Direct leader form test complete!");
        return 0;
    }
}
