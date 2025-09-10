<?php

namespace App\Console\Commands;

use App\Filament\Helpers\DirectLeaderActionHelper;
use Illuminate\Console\Command;

class TestDirectLeaderButton extends Command
{
    protected $signature = 'test:direct-leader-button';
    protected $description = 'Test if the DirectLeaderActionHelper button is working';

    public function handle()
    {
        $this->info('🔍 Testing DirectLeaderActionHelper Button');
        $this->newLine();
        
        try {
            // Test if we can create the action
            $action = DirectLeaderActionHelper::makeAssignDirectLeaderAction();
            
            $this->info('✅ DirectLeaderActionHelper::makeAssignDirectLeaderAction() works');
            $this->line('Action Label: ' . $action->getLabel());
            $this->line('Action Icon: ' . $action->getIcon());
            $this->line('Action Color: ' . $action->getColor());
            $this->newLine();
            
            // Test if we can get options
            $this->info('🔧 Testing getDirectLeaderOptions():');
            $options = DirectLeaderActionHelper::getDirectLeaderOptions();
            $this->line('Total options: ' . count($options));
            
            if (count($options) > 0) {
                $this->info('✅ Leader options are available');
                $firstOption = array_key_first($options);
                $this->line('First option: ' . $firstOption . ' => ' . $options[$firstOption]);
            } else {
                $this->error('❌ No leader options found');
            }
            
            $this->newLine();
            $this->info('✅ DirectLeaderActionHelper is working correctly!');
            $this->info('💡 The button should appear in the Members table.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
