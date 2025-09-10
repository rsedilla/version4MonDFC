<?php

namespace App\Services;

use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use App\Models\Member;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaderSearchService
{
    /**
     * Cache duration for leader data (5 minutes)
     */
    private const CACHE_DURATION = 300;
    
    /**
     * Default search result limit per leader type
     */
    private const DEFAULT_LIMIT_PER_TYPE = 10;
    
    /**
     * Minimum search length
     */
    private const MIN_SEARCH_LENGTH = 2;

    /**
     * Search for leaders across all leader types
     */
    public function searchAllLeaders(string $search, int $limitPerType = self::DEFAULT_LIMIT_PER_TYPE): array
    {
        if (strlen(trim($search)) < self::MIN_SEARCH_LENGTH) {
            return [];
        }

        try {
            $results = [];
            
            // Search Cell Leaders
            $cellLeaders = $this->searchCellLeaders($search, $limitPerType);
            $results = array_merge($results, $cellLeaders);
            
            // Search G12 Leaders
            $g12Leaders = $this->searchG12Leaders($search, $limitPerType);
            $results = array_merge($results, $g12Leaders);
            
            // Search Network Leaders
            $networkLeaders = $this->searchNetworkLeaders($search, $limitPerType);
            $results = array_merge($results, $networkLeaders);
            
            return $results;
            
        } catch (\Exception $e) {
            Log::error('Leader search error: ' . $e->getMessage(), [
                'search_term' => $search,
                'limit_per_type' => $limitPerType
            ]);
            
            return $this->fallbackLeaderSearch($search, $limitPerType);
        }
    }

    /**
     * Search Cell Leaders specifically
     */
    public function searchCellLeaders(string $search, int $limit = self::DEFAULT_LIMIT_PER_TYPE): array
    {
        return $this->performLeaderSearch(CellLeader::class, 'Cell Leader', $search, $limit);
    }

    /**
     * Search G12 Leaders specifically
     */
    public function searchG12Leaders(string $search, int $limit = self::DEFAULT_LIMIT_PER_TYPE): array
    {
        return $this->performLeaderSearch(G12Leader::class, 'G12 Leader', $search, $limit);
    }

    /**
     * Search Network Leaders specifically
     */
    public function searchNetworkLeaders(string $search, int $limit = self::DEFAULT_LIMIT_PER_TYPE): array
    {
        return $this->performLeaderSearch(NetworkLeader::class, 'Network Leader', $search, $limit);
    }

    /**
     * Get leader label by composite key (LeaderType:ID)
     */
    public function getLeaderLabel(?string $compositeKey): ?string
    {
        if (!$compositeKey || !str_contains($compositeKey, ':')) {
            return null;
        }

        try {
            [$modelType, $id] = explode(':', $compositeKey, 2);
            
            $cacheKey = "leader_label_{$modelType}_{$id}";
            
            return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($modelType, $id) {
                $model = match ($modelType) {
                    'CellLeader' => CellLeader::with('member')->find($id),
                    'G12Leader' => G12Leader::with('member')->find($id),
                    'NetworkLeader' => NetworkLeader::with('member')->find($id),
                    default => null
                };
                
                if ($model && $model->member) {
                    $leaderTypeLabel = match ($modelType) {
                        'CellLeader' => 'Cell Leader',
                        'G12Leader' => 'G12 Leader',
                        'NetworkLeader' => 'Network Leader',
                        default => 'Leader'
                    };
                    return $model->member->full_name . " ({$leaderTypeLabel})";
                }
                
                return null;
            });
            
        } catch (\Exception $e) {
            Log::error('Leader label retrieval error: ' . $e->getMessage(), [
                'composite_key' => $compositeKey
            ]);
            
            return null;
        }
    }

    /**
     * Parse composite key to get leader type and ID
     */
    public function parseCompositeKey(string $compositeKey): ?array
    {
        if (!str_contains($compositeKey, ':')) {
            return null;
        }

        [$modelType, $id] = explode(':', $compositeKey, 2);
        
        $leaderType = match ($modelType) {
            'CellLeader' => 'App\Models\CellLeader',
            'G12Leader' => 'App\Models\G12Leader',
            'NetworkLeader' => 'App\Models\NetworkLeader',
            default => null
        };
        
        return $leaderType ? ['leader_type' => $leaderType, 'leader_id' => $id] : null;
    }

    /**
     * Clear leader search cache
     */
    public function clearCache(): void
    {
        // Clear leader label cache pattern
        $patterns = ['leader_label_CellLeader_*', 'leader_label_G12Leader_*', 'leader_label_NetworkLeader_*'];
        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Perform optimized leader search for a specific leader type
     */
    private function performLeaderSearch(string $leaderModel, string $leaderLabel, string $search, int $limit): array
    {
        $searchTerm = strtolower(trim($search));
        
        $leaders = $leaderModel::with('member')
            ->whereHas('member', function ($query) use ($searchTerm) {
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw('LOWER(CONCAT(first_name, " ", COALESCE(middle_name, ""), " ", last_name)) LIKE ?', ["%{$searchTerm}%"]);
                });
            })
            ->orderByRaw('
                (SELECT CASE 
                    WHEN LOWER(m.first_name) LIKE ? THEN 1
                    WHEN LOWER(m.last_name) LIKE ? THEN 2  
                    WHEN LOWER(CONCAT(m.first_name, " ", COALESCE(m.middle_name, ""), " ", m.last_name)) LIKE ? THEN 3
                    ELSE 4
                END
                FROM members m WHERE m.id = ' . (new $leaderModel)->getTable() . '.member_id)
            ', [
                $searchTerm . '%',
                $searchTerm . '%', 
                '%' . $searchTerm . '%'
            ])
            ->limit($limit)
            ->get();

        $results = [];
        $modelShortName = class_basename($leaderModel);
        
        foreach ($leaders as $leader) {
            if ($leader->member) {
                $compositeKey = "{$modelShortName}:{$leader->id}";
                $displayName = $leader->member->full_name . " ({$leaderLabel})";
                $results[$compositeKey] = $displayName;
            }
        }
        
        return $results;
    }

    /**
     * Fallback search with simplified query
     */
    private function fallbackLeaderSearch(string $search, int $limitPerType): array
    {
        $results = [];
        $leaderTypes = [
            ['model' => CellLeader::class, 'label' => 'Cell Leader'],
            ['model' => G12Leader::class, 'label' => 'G12 Leader'],
            ['model' => NetworkLeader::class, 'label' => 'Network Leader']
        ];

        foreach ($leaderTypes as $type) {
            try {
                $leaders = $type['model']::with('member')
                    ->whereHas('member', function ($query) use ($search) {
                        $query->where('first_name', 'LIKE', "%{$search}%")
                              ->orWhere('last_name', 'LIKE', "%{$search}%");
                    })
                    ->limit($limitPerType)
                    ->get();

                $modelShortName = class_basename($type['model']);
                
                foreach ($leaders as $leader) {
                    if ($leader->member) {
                        $compositeKey = "{$modelShortName}:{$leader->id}";
                        $displayName = $leader->member->full_name . " ({$type['label']})";
                        $results[$compositeKey] = $displayName;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Fallback search error for {$type['model']}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Get search configuration for Filament Select components
     */
    public static function getSearchConfig(): array
    {
        return [
            'searchDebounce' => 300,
            'minSearchLength' => self::MIN_SEARCH_LENGTH,
            'defaultLimitPerType' => self::DEFAULT_LIMIT_PER_TYPE,
            'loadingMessage' => 'Searching leaders...',
            'noResultsMessage' => 'No leaders found. Try a different search term.',
            'placeholder' => 'Type to search for leaders...'
        ];
    }
}
