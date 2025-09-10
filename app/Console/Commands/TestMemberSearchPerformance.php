<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Services\MemberSearchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestMemberSearchPerformance extends Command
{
    protected $signature = 'test:member-search-performance 
                          {--search=john : Search term to test}
                          {--limit=100 : Search result limit}';

    protected $description = 'Test and compare member search performance between old and optimized methods';

    public function handle(): int
    {
        $searchTerm = $this->option('search');
        $limit = (int) $this->option('limit');

        $this->info("🔍 Testing Member Search Performance");
        $this->info("Search Term: {$searchTerm}");
        $this->info("Limit: {$limit}");
        $this->newLine();

        // Test old inefficient method (similar to what was in MembersTable)
        $this->testOldSearchMethod($searchTerm, $limit);
        
        // Test optimized MemberSearchService
        $this->testOptimizedSearchService($searchTerm, $limit);
        
        // Test basic table query performance
        $this->testTableQueryPerformance();
        
        return 0;
    }

    private function testOldSearchMethod(string $searchTerm, int $limit): void
    {
        $this->info("📊 Testing OLD Search Method (Multiple LIKE queries)");
        
        // Enable query logging
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Old inefficient method similar to what was in MembersTable
        $results = Member::where(function ($query) use ($searchTerm) {
            $query->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('middle_name', 'like', "%{$searchTerm}%");
        })
        ->limit($limit)
        ->get();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $this->displayResults('Old Method', $startTime, $endTime, $startMemory, $endMemory, $queries, $results->count());
    }

    private function testOptimizedSearchService(string $searchTerm, int $limit): void
    {
        $this->info("🚀 Testing OPTIMIZED MemberSearchService");
        
        // Enable query logging
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $searchService = new MemberSearchService();
        $results = $searchService->searchMembers($searchTerm, $limit);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $this->displayResults('Optimized Service', $startTime, $endTime, $startMemory, $endMemory, $queries, count($results));
    }

    private function testTableQueryPerformance(): void
    {
        $this->info("📋 Testing Table Query Performance (with eager loading)");
        
        // Enable query logging
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Test the optimized table query with eager loading
        $results = Member::with([
            'trainingTypes:id,name',
            'directLeader.member:id,first_name,middle_name,last_name'
        ])
        ->limit(50)
        ->get();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $this->displayResults('Table Query (Eager Loading)', $startTime, $endTime, $startMemory, $endMemory, $queries, $results->count());
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
            ['Results Found', $resultCount],
            ['Execution Time', number_format($executionTime, 2) . ' ms'],
            ['Memory Used', number_format($memoryUsed, 2) . ' MB'],
            ['Query Count', $queryCount],
        ]);

        if ($queryCount > 0) {
            $this->line("📝 SQL Queries:");
            foreach ($queries as $index => $query) {
                $this->line("  " . ($index + 1) . ". " . $query['query']);
                if (!empty($query['bindings'])) {
                    $this->line("     Bindings: " . json_encode($query['bindings']));
                }
            }
        }

        $this->newLine();
    }
}
