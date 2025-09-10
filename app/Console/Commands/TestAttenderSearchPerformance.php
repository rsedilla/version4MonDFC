<?php

namespace App\Console\Commands;

use App\Models\Attender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestAttenderSearchPerformance extends Command
{
    protected $signature = 'test:attender-search-performance 
                          {--search=john : Search term to test}
                          {--limit=50 : Search result limit}';

    protected $description = 'Test and compare attender search and table performance optimizations';

    public function handle(): int
    {
        $searchTerm = $this->option('search');
        $limit = (int) $this->option('limit');

        $this->info("🔍 Testing Attender Search Performance");
        $this->info("Search Term: {$searchTerm}");
        $this->info("Limit: {$limit}");
        $this->newLine();

        // Test old inefficient consolidator search
        $this->testOldConsolidatorSearch($searchTerm, $limit);
        
        // Test optimized consolidator search
        $this->testOptimizedConsolidatorSearch($searchTerm, $limit);
        
        // Test table query performance with eager loading
        $this->testTableQueryPerformance($limit);
        
        // Test progress calculation performance
        $this->testProgressCalculationPerformance($limit);
        
        return 0;
    }

    private function testOldConsolidatorSearch(string $searchTerm, int $limit): void
    {
        $this->info("📊 Testing OLD Consolidator Search Method (Complex EXISTS query)");
        
        // Enable query logging
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Old inefficient method similar to what was in AttendersTable
        $results = Attender::whereExists(function ($query) use ($searchTerm) {
            $query->select(DB::raw(1))
                  ->from('members')
                  ->whereColumn('members.id', 'attenders.consolidator_id')
                  ->where(function ($query) use ($searchTerm) {
                      $query->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%")
                            ->orWhere('middle_name', 'like', "%{$searchTerm}%");
                  });
        })
        ->limit($limit)
        ->get();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $this->displayResults('Old Consolidator Search', $startTime, $endTime, $startMemory, $endMemory, $queries, $results->count());
    }

    private function testOptimizedConsolidatorSearch(string $searchTerm, int $limit): void
    {
        $this->info("🚀 Testing OPTIMIZED Consolidator Search (with eager loading)");
        
        // Enable query logging
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Optimized method using standard searchable and eager loading
        $results = Attender::with([
            'member:id,first_name,middle_name,last_name',
            'consolidator:id,first_name,middle_name,last_name'
        ])
        ->whereHas('consolidator', function ($query) use ($searchTerm) {
            $query->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%");
        })
        ->limit($limit)
        ->get();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $this->displayResults('Optimized Consolidator Search', $startTime, $endTime, $startMemory, $endMemory, $queries, $results->count());
    }

    private function testTableQueryPerformance(int $limit): void
    {
        $this->info("📋 Testing Table Query Performance (with eager loading)");
        
        // Enable query logging
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Test the optimized table query with eager loading
        $results = Attender::with([
            'member:id,first_name,middle_name,last_name',
            'consolidator:id,first_name,middle_name,last_name'
        ])
        ->limit($limit)
        ->get();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $this->displayResults('Table Query (Eager Loading)', $startTime, $endTime, $startMemory, $endMemory, $queries, $results->count());
    }

    private function testProgressCalculationPerformance(int $limit): void
    {
        $this->info("📈 Testing Progress Calculation Performance");
        
        // Get some sample records
        $attenders = Attender::limit($limit)->get();
        
        // Test old calculation method
        $this->info("  📊 OLD Progress Calculation (for loops)");
        
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        foreach ($attenders as $attender) {
            // Old method - using for loops
            $suylnCompleted = 0;
            for ($i = 1; $i <= 10; $i++) {
                if ($attender->{"suyln_lesson_$i"}) {
                    $suylnCompleted++;
                }
            }
            
            $dccCompleted = 0;
            for ($i = 1; $i <= 4; $i++) {
                if ($attender->{"sunday_service_$i"}) {
                    $dccCompleted++;
                }
            }
            
            $cgCompleted = 0;
            for ($i = 1; $i <= 4; $i++) {
                if ($attender->{"cell_group_$i"}) {
                    $cgCompleted++;
                }
            }
        }
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $this->displayResults('Old Progress Calculation', $startTime, $endTime, $startMemory, $endMemory, $queries, $attenders->count());
        
        // Test new calculation method
        $this->info("  🚀 NEW Progress Calculation (optimized trait)");
        
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        foreach ($attenders as $attender) {
            // New method - using trait attributes
            $suylnProgress = $attender->suyln_progress;
            $dccProgress = $attender->dcc_progress;
            $cgProgress = $attender->cg_progress;
            $overallProgress = $attender->overall_progress;
        }
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $this->displayResults('New Progress Calculation', $startTime, $endTime, $startMemory, $endMemory, $queries, $attenders->count());
    }

    private function displayResults(
        string $method, 
        float $startTime, 
        float $endTime, 
        int $startMemory, 
        int $endMemory, 
        array $queries, 
        int $resultCount
    ): void {
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // Convert to MB
        $queryCount = count($queries);

        $this->table(['Metric', 'Value'], [
            ['Method', $method],
            ['Results Processed', $resultCount],
            ['Execution Time', number_format($executionTime, 2) . ' ms'],
            ['Memory Used', number_format($memoryUsed, 2) . ' MB'],
            ['Query Count', $queryCount],
        ]);

        if ($queryCount > 0 && $queryCount <= 5) {
            $this->line("📝 SQL Queries:");
            foreach ($queries as $index => $query) {
                $this->line("  " . ($index + 1) . ". " . $query['query']);
                if (!empty($query['bindings'])) {
                    $this->line("     Bindings: " . json_encode($query['bindings']));
                }
            }
        } elseif ($queryCount > 5) {
            $this->line("📝 Too many queries to display ({$queryCount} total)");
        }

        $this->newLine();
    }
}
