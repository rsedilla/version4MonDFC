<?php

namespace App\Console\Commands;

use App\Services\LeaderSearchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestLeaderSearchPerformance extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:leader-search-performance {search?} {--limit=10}';

    /**
     * The console command description.
     */
    protected $description = 'Test the performance and functionality of the LeaderSearchService';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $searchTerm = $this->argument('search') ?? 'john';
        $limit = (int) $this->option('limit');
        
        $this->info("Testing Leader Search Performance");
        $this->info("Search Term: {$searchTerm}");
        $this->info("Limit per type: {$limit}");
        $this->newLine();
        
        $leaderSearchService = app(LeaderSearchService::class);
        
        // Test individual leader type searches
        $this->testIndividualSearches($leaderSearchService, $searchTerm, $limit);
        
        // Test combined search
        $this->testCombinedSearch($leaderSearchService, $searchTerm, $limit);
        
        // Test composite key parsing
        $this->testCompositeKeyParsing($leaderSearchService);
        
        return 0;
    }
    
    private function testIndividualSearches(LeaderSearchService $service, string $searchTerm, int $limit): void
    {
        $this->info("🔍 Testing Individual Leader Type Searches");
        
        // Test Cell Leaders
        $this->testSearchType('Cell Leaders', function() use ($service, $searchTerm, $limit) {
            return $service->searchCellLeaders($searchTerm, $limit);
        });
        
        // Test G12 Leaders
        $this->testSearchType('G12 Leaders', function() use ($service, $searchTerm, $limit) {
            return $service->searchG12Leaders($searchTerm, $limit);
        });
        
        // Test Network Leaders
        $this->testSearchType('Network Leaders', function() use ($service, $searchTerm, $limit) {
            return $service->searchNetworkLeaders($searchTerm, $limit);
        });
        
        $this->newLine();
    }
    
    private function testCombinedSearch(LeaderSearchService $service, string $searchTerm, int $limit): void
    {
        $this->info("🚀 Testing Combined Leader Search");
        
        // Enable query logging
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $results = $service->searchAllLeaders($searchTerm, $limit);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // Convert to MB
        $queryCount = count($queries);
        
        $this->table([
            'Metric', 'Combined Search'
        ], [
            ['Execution Time', number_format($executionTime, 2) . ' ms'],
            ['Memory Used', number_format($memoryUsed, 2) . ' MB'],
            ['Database Queries', $queryCount],
            ['Results Found', count($results)],
        ]);
        
        if (!empty($results)) {
            $this->info("📋 Sample Results:");
            $sampleResults = array_slice($results, 0, 5, true);
            foreach ($sampleResults as $key => $label) {
                $this->line("  • {$key} → {$label}");
            }
            
            if (count($results) > 5) {
                $this->line("  ... and " . (count($results) - 5) . " more results");
            }
        }
        
        $this->newLine();
    }
    
    private function testSearchType(string $type, callable $searchFunction): void
    {
        $startTime = microtime(true);
        
        try {
            $results = $searchFunction();
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000;
            
            $this->line("  ✅ {$type}: " . count($results) . " results in " . number_format($executionTime, 2) . "ms");
            
            if (!empty($results)) {
                $sampleKey = array_key_first($results);
                $sampleValue = $results[$sampleKey];
                $this->line("     Sample: {$sampleKey} → {$sampleValue}");
            }
            
        } catch (\Exception $e) {
            $this->error("  ❌ {$type}: Error - " . $e->getMessage());
        }
    }
    
    private function testCompositeKeyParsing(LeaderSearchService $service): void
    {
        $this->info("🔑 Testing Composite Key Parsing");
        
        $testKeys = [
            'CellLeader:1',
            'G12Leader:2',
            'NetworkLeader:3',
            'InvalidKey:4',
            'NoColon',
            ''
        ];
        
        foreach ($testKeys as $key) {
            $parsed = $service->parseCompositeKey($key);
            $label = $service->getLeaderLabel($key);
            
            if ($parsed) {
                $this->line("  ✅ {$key} → Type: {$parsed['leader_type']}, ID: {$parsed['leader_id']}");
                if ($label) {
                    $this->line("     Label: {$label}");
                }
            } else {
                $this->line("  ❌ {$key} → Invalid or not found");
            }
        }
        
        $this->newLine();
        $this->info("✅ Leader Search Performance Test Completed!");
        $this->newLine();
        
        $this->comment("💡 Performance Tips:");
        $this->line("• The service uses caching for repeated label lookups");
        $this->line("• Search results are ordered by relevance (exact matches first)");
        $this->line("• Fallback search mechanisms ensure reliability");
        $this->line("• Composite keys enable polymorphic leader relationships");
    }
}
