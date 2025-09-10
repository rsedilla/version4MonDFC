<?php

namespace App\Traits;

use App\Services\MemberSearchService;
use Illuminate\Database\Eloquent\Builder;

trait OptimizedMemberSearch
{
    /**
     * Apply optimized member search to query
     */
    public function scopeOptimizedSearch(Builder $query, string $search): Builder
    {
        if (strlen(trim($search)) < 2) {
            return $query;
        }

        $searchTerm = strtolower(trim($search));
        
        return $query->where(function ($subQuery) use ($searchTerm) {
            $subQuery->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchTerm}%"])
                     ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchTerm}%"])
                     ->orWhereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ["%{$searchTerm}%"])
                     ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"]);
        })
        ->orderByRaw('
            CASE 
                WHEN LOWER(first_name) LIKE ? THEN 1
                WHEN LOWER(last_name) LIKE ? THEN 2  
                WHEN LOWER(CONCAT(first_name, " ", last_name)) LIKE ? THEN 3
                ELSE 4
            END, first_name, last_name
        ', [
            $searchTerm . '%',
            $searchTerm . '%', 
            '%' . $searchTerm . '%'
        ]);
    }

    /**
     * Apply optimized member search with exclusions
     */
    public function scopeOptimizedSearchWithExclusions(Builder $query, string $search, array $excludedIds = []): Builder
    {
        $query = $this->scopeOptimizedSearch($query, $search);
        
        if (!empty($excludedIds)) {
            $query->whereNotIn('id', $excludedIds);
        }
        
        return $query;
    }

    /**
     * Get search configuration for consistent behavior
     */
    public static function getSearchConfig(): array
    {
        return MemberSearchService::getSearchConfig();
    }
}
