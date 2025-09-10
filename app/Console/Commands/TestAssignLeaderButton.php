<?php

namespace App\Console\Commands;

use App\Filament\Helpers\DirectLeaderActionHelper;
use Illuminate\Console\Command;

class TestAssignLeaderButton extends Command
{
    protected $signature = 'test:assign-leader-button';
    protected $description = 'Test if the Assign Leader button is working';

    public function handle()
    {
        $this->info('🔍 Testing Assign Leader Button');
        $this->newLine();
        
        try {
            // Test if DirectLeaderActionHelper class exists and methods are callable
            $this->info("✅ DirectLeaderActionHelper class: " . (class_exists(DirectLeaderActionHelper::class) ? 'EXISTS' : 'NOT FOUND'));
            
            // Test if the makeAssignDirectLeaderAction method exists
            if (method_exists(DirectLeaderActionHelper::class, 'makeAssignDirectLeaderAction')) {
                $this->info("✅ makeAssignDirectLeaderAction method: EXISTS");
                
                // Try to call the method
                $action = DirectLeaderActionHelper::makeAssignDirectLeaderAction();
                $this->info("✅ Action created successfully: " . get_class($action));
                
                // Check action properties
                $this->table(['Property', 'Value'], [
                    ['Label', method_exists($action, 'getLabel') ? 'Has label method' : 'No label method'],
                    ['Icon', method_exists($action, 'getIcon') ? 'Has icon method' : 'No icon method'],
                    ['Color', method_exists($action, 'getColor') ? 'Has color method' : 'No color method'],
                ]);
                
            } else {
                $this->error("❌ makeAssignDirectLeaderAction method: NOT FOUND");
            }
            
            // Test if the makeBulkAssignDirectLeaderAction method exists
            if (method_exists(DirectLeaderActionHelper::class, 'makeBulkAssignDirectLeaderAction')) {
                $this->info("✅ makeBulkAssignDirectLeaderAction method: EXISTS");
                
                $bulkAction = DirectLeaderActionHelper::makeBulkAssignDirectLeaderAction();
                $this->info("✅ Bulk action created successfully: " . get_class($bulkAction));
                
            } else {
                $this->error("❌ makeBulkAssignDirectLeaderAction method: NOT FOUND");
            }
            
            // Test getDirectLeaderOptions
            if (method_exists(DirectLeaderActionHelper::class, 'getDirectLeaderOptions')) {
                $this->info("✅ getDirectLeaderOptions method: EXISTS");
                
                $options = DirectLeaderActionHelper::getDirectLeaderOptions();
                $this->info("✅ Leader options count: " . count($options));
                
                if (count($options) > 0) {
                    $this->info("✅ Sample options (first 3):");
                    $sampleOptions = array_slice($options, 0, 3, true);
                    foreach ($sampleOptions as $key => $value) {
                        $this->line("  - {$key} => {$value}");
                    }
                } else {
                    $this->warn("⚠️  No leader options found - this might be why the button doesn't work");
                }
                
            } else {
                $this->error("❌ getDirectLeaderOptions method: NOT FOUND");
            }
            
            $this->newLine();
            $this->info('✅ DirectLeaderActionHelper functionality test completed!');
            
            // Check if there are any namespace or import issues
            $this->info("🔧 Additional checks:");
            $this->line("- DirectLeaderActionHelper full class name: " . DirectLeaderActionHelper::class);
            $this->line("- File should be at: app/Filament/Helpers/DirectLeaderActionHelper.php");
            
        } catch (\Exception $e) {
            $this->error('❌ Error testing DirectLeaderActionHelper: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
