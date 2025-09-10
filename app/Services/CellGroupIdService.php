<?php

namespace App\Services;

use App\Models\CellGroupInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CellGroupIdService
{
    /**
     * Generate a unique cell group ID number following format: YYYYMM###
     * Where ### is a counter from 001 to 300 per month
     * 
     * @return string
     */
    public static function generateCellGroupIdNum(): string
    {
        $year = date('Y');
        $month = str_pad(date('m'), 2, '0', STR_PAD_LEFT);
        $prefix = $year . $month;
        
        try {
            // Get the highest counter for this year-month combination
            $lastRecord = CellGroupInfo::where('cell_group_idnum', 'LIKE', $prefix . '%')
                ->orderBy('cell_group_idnum', 'desc')
                ->first();
            
            $counter = 1;
            
            if ($lastRecord) {
                // Extract the counter from the last record
                $lastIdNum = $lastRecord->cell_group_idnum;
                $lastCounter = (int) substr($lastIdNum, -3);
                $counter = $lastCounter + 1;
            }
            
            // Check if we've reached the monthly limit
            if ($counter > 300) {
                throw new \Exception("Monthly limit of 300 cell groups reached for {$year}-{$month}");
            }
            
            // Format the counter with leading zeros
            $counterStr = str_pad($counter, 3, '0', STR_PAD_LEFT);
            $newIdNum = $prefix . $counterStr;
            
            // Double-check uniqueness (race condition protection)
            while (CellGroupInfo::where('cell_group_idnum', $newIdNum)->exists()) {
                $counter++;
                if ($counter > 300) {
                    throw new \Exception("Monthly limit of 300 cell groups reached for {$year}-{$month}");
                }
                $counterStr = str_pad($counter, 3, '0', STR_PAD_LEFT);
                $newIdNum = $prefix . $counterStr;
            }
            
            return $newIdNum;
            
        } catch (\Exception $e) {
            Log::error('Failed to generate cell group ID number: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get current month's cell group count
     * 
     * @return int
     */
    public static function getCurrentMonthCount(): int
    {
        $year = date('Y');
        $month = str_pad(date('m'), 2, '0', STR_PAD_LEFT);
        $prefix = $year . $month;
        
        return CellGroupInfo::where('cell_group_idnum', 'LIKE', $prefix . '%')->count();
    }
    
    /**
     * Check if monthly limit is reached
     * 
     * @return bool
     */
    public static function isMonthlyLimitReached(): bool
    {
        return self::getCurrentMonthCount() >= 300;
    }
    
    /**
     * Get available slots for current month
     * 
     * @return int
     */
    public static function getAvailableSlots(): int
    {
        return 300 - self::getCurrentMonthCount();
    }
    
    /**
     * Parse cell group ID number to get year, month, and counter
     * 
     * @param string $idNum
     * @return array
     */
    public static function parseIdNum(string $idNum): array
    {
        if (strlen($idNum) !== 9) {
            throw new \InvalidArgumentException('Invalid cell group ID number format');
        }
        
        return [
            'year' => substr($idNum, 0, 4),
            'month' => substr($idNum, 4, 2),
            'counter' => (int) substr($idNum, 6, 3),
            'full' => $idNum
        ];
    }
    
    /**
     * Get cell groups by year and month
     * 
     * @param int $year
     * @param int $month
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCellGroupsByMonth(int $year, int $month): \Illuminate\Database\Eloquent\Collection
    {
        $prefix = $year . str_pad($month, 2, '0', STR_PAD_LEFT);
        
        return CellGroupInfo::where('cell_group_idnum', 'LIKE', $prefix . '%')
            ->with('cellGroup.leader')
            ->orderBy('cell_group_idnum')
            ->get();
    }
}
