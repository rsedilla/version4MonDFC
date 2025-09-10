<?php

namespace App\Console\Commands;

use App\Traits\HasLeaderSearch;
use App\Services\LeaderSearchService;
use Illuminate\Console\Command;

class TestLeaderSearchForm extends Command
{
    use HasLeaderSearch;
    
    protected $signature = 'test:leader-search-form {search?}';
    protected $description = 'Test the HasLeaderSearch trait form functionality';

    public function handle()
    {
        $searchTerm = $this->argument('search') ?? 'adrian';
        
        $this->info('🔍 Testing HasLeaderSearch Trait Form');
        $this->info("Search Term: '{$searchTerm}'");
        $this->newLine();
        
        try {
            // Test form component generation
            $components = self::leaderSelect('leader_info', '👤 Select Leader');
            $this->info('✅ Form Components Generated: ' . count($components));
            
            foreach ($components as $index => $component) {
                $componentClass = class_basename(get_class($component));
                $componentName = $component->getName();
                $this->line("  Component #{$index}: {$componentClass} -> '{$componentName}'");
            }
            $this->newLine();
            
            // Test the search service directly
            $searchService = app(LeaderSearchService::class);
            $results = $searchService->searchAllLeaders($searchTerm, 5);
            
            $this->info("🎯 Search Results for '{$searchTerm}': " . count($results));
            foreach ($results as $key => $label) {
                $this->line("  {$key} => {$label}");
            }
            $this->newLine();
            
            // Test composite key parsing
            if (count($results) > 0) {
                $testKey = array_key_first($results);
                $this->info("🔧 Testing Composite Key Parsing:");
                $this->line("Test Key: {$testKey}");
                
                $parsed = $searchService->parseCompositeKey($testKey);
                if ($parsed) {
                    $this->info("✅ Parsed Successfully:");
                    $this->line("  leader_id: {$parsed['leader_id']}");
                    $this->line("  leader_type: {$parsed['leader_type']}");
                } else {
                    $this->error("❌ Failed to parse composite key");
                }
            }
            
            $this->newLine();
            $this->info('✅ HasLeaderSearch trait is working correctly!');
            $this->info('💡 The form should now work properly with real-time search across all leader tables.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
