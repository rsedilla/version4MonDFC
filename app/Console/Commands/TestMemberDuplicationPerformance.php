<?php

namespace App\Console\Commands;

use App\Services\MemberDuplicationService;
use App\Services\OptimizedMemberDuplicationService;
use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestMemberDuplicationPerformance extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:member-duplication-performance {--count=100 : Number of members to test}';

    /**
     * The console command description.
     */
    protected $description = 'Test performance difference between original and optimized member duplication services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        
        $this->info("Testing Member Duplication Service Performance");
        $this->info("Testing with {$count} members...");
        $this->newLine();
        
        // Get random member IDs to test with
        $memberIds = Member::inRandomOrder()->limit($count)->pluck('id')->toArray();
        
        if (empty($memberIds)) {
            $this->error('No members found in database. Please seed some member data first.');
            return 1;
        }
        
        $this->info("Found " . count($memberIds) . " members to test with.");
        $this->newLine();
        
        // Test Original Service
        $this->testOriginalService($memberIds);
        
        // Test Optimized Service
        $this->testOptimizedService($memberIds);
        
        // Test Batch Processing
        $this->testBatchProcessing($memberIds);
        
        return 0;
    }
    
    private function testOriginalService(array $memberIds): void
    {
        $this->info("🔄 Testing Original MemberDuplicationService...");
        
        // Reset query count
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $results = [];
        foreach ($memberIds as $memberId) {
            $results[] = MemberDuplicationService::isAlreadyAssigned($memberId);
        }
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // Convert to MB
        $queryCount = count($queries);
        
        $this->table([
            'Metric', 'Original Service'
        ], [
            ['Execution Time', number_format($executionTime, 2) . ' ms'],
            ['Memory Used', number_format($memoryUsed, 2) . ' MB'],
            ['Database Queries', $queryCount],
            ['Avg Queries/Member', number_format($queryCount / count($memberIds), 1)],
            ['Members Processed', count($memberIds)],
            ['Assignments Found', array_sum(array_map('count', $results))]
        ]);
        
        $this->newLine();
    }
    
    private function testOptimizedService(array $memberIds): void
    {
        $this->info("⚡ Testing Optimized MemberDuplicationService...");
        
        // Reset query count
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $results = [];
        foreach ($memberIds as $memberId) {
            $results[] = OptimizedMemberDuplicationService::isAlreadyAssigned($memberId);
        }
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // Convert to MB
        $queryCount = count($queries);
        
        $this->table([
            'Metric', 'Optimized Service'
        ], [
            ['Execution Time', number_format($executionTime, 2) . ' ms'],
            ['Memory Used', number_format($memoryUsed, 2) . ' MB'],
            ['Database Queries', $queryCount],
            ['Avg Queries/Member', number_format($queryCount / count($memberIds), 1)],
            ['Members Processed', count($memberIds)],
            ['Assignments Found', array_sum(array_map('count', $results))]
        ]);
        
        $this->newLine();
    }
    
    private function testBatchProcessing(array $memberIds): void
    {
        $this->info("🚀 Testing Batch Processing (Optimized Service Only)...");
        
        // Reset query count
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $results = OptimizedMemberDuplicationService::batchCheckAssignments($memberIds);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // Convert to MB
        $queryCount = count($queries);
        
        $this->table([
            'Metric', 'Batch Processing'
        ], [
            ['Execution Time', number_format($executionTime, 2) . ' ms'],
            ['Memory Used', number_format($memoryUsed, 2) . ' MB'],
            ['Database Queries', $queryCount],
            ['Members Processed', count($memberIds)],
            ['Assignments Found', array_sum(array_map('count', $results))]
        ]);
        
        $this->newLine();
        $this->info("✅ Performance testing completed!");
        $this->newLine();
        
        $this->comment("💡 Performance Tips:");
        $this->line("• Use OptimizedMemberDuplicationService for individual checks");
        $this->line("• Use batchCheckAssignments() for processing multiple members");
        $this->line("• The optimized service includes automatic fallback to original service if needed");
        $this->line("• Monitor logs for any fallback warnings after deployment");
    }
}
