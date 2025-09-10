<?php

namespace App\Console\Commands;

use App\Services\LeaderSearchService;
use App\Traits\HasLeaderSearch;
use Illuminate\Console\Command;

class TestUnifiedLeaderSearch extends Command
{
    use HasLeaderSearch;
    
    protected $signature = 'test:unified-leader-search {search?}';
    protected $description = 'Test the unified leader search across all three leader tables';

    public function handle()
    {
        $searchTerm = $this->argument('search') ?? 'john';
        
        $this->info('🔍 Testing Unified Leader Search System');
        $this->info("Search Term: '{$searchTerm}'");
        $this->newLine();
        
        $service = app(LeaderSearchService::class);
        
        // Test 1: Show the problem with separate searches
        $this->info('❌ OLD WAY: Separate searches (what you were doing)');
        $this->testSeparateSearches($service, $searchTerm);
        $this->newLine();
        
        // Test 2: Show the solution with unified search
        $this->info('✅ NEW WAY: Unified search (what you should use)');
        $this->testUnifiedSearch($service, $searchTerm);
        $this->newLine();
        
        // Test 3: Show form component structure
        $this->info('🎨 FORM COMPONENT: How it works in your CellGroupForm');
        $this->testFormComponent();
        
        return 0;
    }
    
    private function testSeparateSearches(LeaderSearchService $service, string $search): void
    {
        $this->line('  🔍 Searching Cell Leaders...');
        $cellResults = $service->searchCellLeaders($search, 3);
        $this->line("     Found: " . count($cellResults) . " results");
        
        $this->line('  🔍 Searching G12 Leaders...');
        $g12Results = $service->searchG12Leaders($search, 3);
        $this->line("     Found: " . count($g12Results) . " results");
        
        $this->line('  🔍 Searching Network Leaders...');
        $networkResults = $service->searchNetworkLeaders($search, 3);
        $this->line("     Found: " . count($networkResults) . " results");
        
        $totalResults = count($cellResults) + count($g12Results) + count($networkResults);
        $this->line("  📊 Total: {$totalResults} results from 3 separate database queries");
        $this->line("  ❌ Problem: User has to know which leader type to search in");
    }
    
    private function testUnifiedSearch(LeaderSearchService $service, string $search): void
    {
        $results = $service->searchAllLeaders($search, 5);
        
        $this->line("  🎯 Single search across ALL leader types");
        $this->line("  📊 Found: " . count($results) . " results in one operation");
        $this->line("  ✅ Benefit: User searches once, gets all matching leaders");
        $this->newLine();
        
        if (!empty($results)) {
            $this->line("  📋 Sample Results:");
            $count = 0;
            foreach ($results as $key => $label) {
                $this->line("     • {$key} → {$label}");
                $count++;
                if ($count >= 5) break;
            }
        }
    }
    
    private function testFormComponent(): void
    {
        $this->line("  🎨 Your form now uses: HasLeaderSearch trait");
        $this->line("  📝 Single field: ...self::leaderSelect('leader_info', 'Select Leader')");
        $this->line("  🔄 Auto-parsing: Extracts leader_id and leader_type automatically");
        $this->line("  💾 Database saves: leader_id = 3, leader_type = 'App\\Models\\CellLeader'");
        $this->newLine();
        
        // Test form component generation
        try {
            $components = self::leaderSelect('leader_info', '👤 Select Leader');
            $this->line("  ✅ Form components generated: " . count($components) . " fields");
            $this->line("     1. Main search field (searches all tables)");
            $this->line("     2. Hidden leader_id field (for database)");
            $this->line("     3. Hidden leader_type field (for polymorphic relationship)");
            
        } catch (\Exception $e) {
            $this->error("  ❌ Error generating form components: " . $e->getMessage());
        }
    }
}
