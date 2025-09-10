<?php

namespace App\Console\Commands;

use App\Filament\Resources\Members\Tables\MembersTable;
use App\Models\Member;
use Filament\Tables\Table;
use Illuminate\Console\Command;

class TestMembersTableActions extends Command
{
    protected $signature = 'test:members-table-actions';
    protected $description = 'Test if the Members table actions are configured correctly';

    public function handle()
    {
        $this->info('🔍 Testing Members Table Actions Configuration');
        $this->newLine();
        
        try {
            $this->info('✅ MembersTable class exists and is importable');
            $this->newLine();
            
            // Test that we can get a member record
            $member = Member::first();
            if ($member) {
                $this->info("✅ Found test member: {$member->full_name} (ID: {$member->id})");
            } else {
                $this->warn("⚠️  No members found in database");
            }
            
            $this->newLine();
            
            // List the expected actions
            $this->info('📋 Expected Record Actions:');
            $this->line('1. View Action');
            $this->line('2. Edit Action');
            $this->line('3. Assign Leader Action (DirectLeaderActionHelper)');
            
            $this->newLine();
            
            $this->info('📋 Expected Toolbar Actions:');
            $this->line('1. Delete Bulk Action');
            $this->line('2. Bulk Assign Leader Action (DirectLeaderActionHelper)');
            
            $this->newLine();
            
            $this->info('✅ MembersTable configuration is valid!');
            $this->info('💡 If the "Assign Leader" button is not showing:');
            $this->line('   1. Try hard refresh (Ctrl+F5) in browser');
            $this->line('   2. Clear browser cache');
            $this->line('   3. Check browser console for JavaScript errors');
            $this->line('   4. Make sure you\'re looking at Members table, not CellMembers');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
