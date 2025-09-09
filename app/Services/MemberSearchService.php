<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Attender;
use App\Models\SeniorPastor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MemberSearchService
{
    /**
     * Cache duration for excluded IDs (5 minutes)
     */
    private const CACHE_DURATION = 300;
    
    /**
     * Default search result limit
     */
    private const DEFAULT_LIMIT = 100;
    
    /**
     * Minimum search length
     */
    private const MIN_SEARCH_LENGTH = 2;

    /**
     * Search for consolidators (excludes attenders and senior pastors)
     */
    public function searchConsolidators(string $search, int $limit = self::DEFAULT_LIMIT): array
    {
        if (strlen(trim($search)) < self::MIN_SEARCH_LENGTH) {
            return [];
        }

        try {
            $excludedIds = $this->getExcludedIdsForConsolidators();
            
            return $this->performMemberSearch($search, $limit, $excludedIds);
            
        } catch (\Exception $e) {
            Log::error('Consolidator search error: ' . $e->getMessage(), [
                'search_term' => $search,
                'limit' => $limit
            ]);
            
            return $this->fallbackSearch($search, min($limit, 50));
        }
    }

    /**
     * Search for all members without exclusions
     */
    public function searchMembers(string $search, int $limit = self::DEFAULT_LIMIT): array
    {
        if (strlen(trim($search)) < self::MIN_SEARCH_LENGTH) {
            return [];
        }

        try {
            return $this->performMemberSearch($search, $limit);
            
        } catch (\Exception $e) {
            Log::error('Member search error: ' . $e->getMessage(), [
                'search_term' => $search,
                'limit' => $limit
            ]);
            
            return $this->fallbackSearch($search, min($limit, 50));
        }
    }

    /**
     * Search for members with custom exclusions
     */
    public function searchMembersWithExclusions(string $search, array $excludedIds = [], int $limit = self::DEFAULT_LIMIT): array
    {
        if (strlen(trim($search)) < self::MIN_SEARCH_LENGTH) {
            return [];
        }

        try {
            return $this->performMemberSearch($search, $limit, $excludedIds);
            
        } catch (\Exception $e) {
            Log::error('Member search with exclusions error: ' . $e->getMessage(), [
                'search_term' => $search,
                'excluded_count' => count($excludedIds),
                'limit' => $limit
            ]);
            
            return $this->fallbackSearch($search, min($limit, 50));
        }
    }

    /**
     * Get member label by ID efficiently
     */
    public function getMemberLabel(?int $memberId): ?string
    {
        if (!$memberId) {
            return null;
        }

        try {
            $cacheKey = "member_label_{$memberId}";
            
            return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($memberId) {
                $member = Member::select(['first_name', 'last_name'])->find($memberId);
                return $member ? $member->first_name . ' ' . $member->last_name : null;
            });
            
        } catch (\Exception $e) {
            Log::error('Member label retrieval error: ' . $e->getMessage(), [
                'member_id' => $memberId
            ]);
            
            return null;
        }
    }

    /**
     * Clear member search cache
     */
    public function clearCache(): void
    {
        Cache::forget('consolidator_excluded_ids');
        
        // Clear member label cache pattern
        $keys = Cache::get('member_label_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('member_label_keys');
    }

    /**
     * Get excluded member IDs for consolidator search (cached)
     */
    private function getExcludedIdsForConsolidators(): array
    {
        return Cache::remember('consolidator_excluded_ids', self::CACHE_DURATION, function () {
            $attenderIds = Attender::pluck('member_id')->toArray();
            $seniorPastorIds = SeniorPastor::pluck('member_id')->toArray();
            
            return array_merge($attenderIds, $seniorPastorIds);
        });
    }

    /**
     * Perform optimized member search with smart ordering
     */
    private function performMemberSearch(string $search, int $limit, array $excludedIds = []): array
    {
        $searchTerm = strtolower(trim($search));
        
        $query = Member::select(['id', 'first_name', 'last_name', 'email'])
            ->where(function ($subQuery) use ($searchTerm) {
                $subQuery->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchTerm}%"])
                         ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchTerm}%"])
                         ->orWhereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ["%{$searchTerm}%"])
                         ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"]);
            });

        // Apply exclusions if provided
        if (!empty($excludedIds)) {
            $query->whereNotIn('id', $excludedIds);
        }

        $members = $query->orderByRaw('
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
            ])
            ->limit($limit)
            ->get();

        return $members->mapWithKeys(function ($member) {
            return [$member->id => $member->first_name . ' ' . $member->last_name];
        })->toArray();
    }

    /**
     * Fallback search with simplified query
     */
    private function fallbackSearch(string $search, int $limit): array
    {
        return Member::select(['id', 'first_name', 'last_name'])
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit($limit)
            ->get()
            ->mapWithKeys(function ($member) {
                return [$member->id => $member->first_name . ' ' . $member->last_name];
            })
            ->toArray();
    }

    /**
     * Get search configuration for Filament Select components
     */
    public static function getSearchConfig(): array
    {
        return [
            'searchDebounce' => 300,
            'minSearchLength' => self::MIN_SEARCH_LENGTH,
            'defaultLimit' => self::DEFAULT_LIMIT,
            'loadingMessage' => 'Searching members...',
            'noResultsMessage' => 'No members found. Try a different search term.',
            'placeholder' => 'Type to search for members...'
        ];
    }
}
