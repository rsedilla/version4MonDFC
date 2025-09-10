<?php

namespace App\Traits;

use App\Services\LeaderSearchService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

trait HasLeaderSearch
{
    /**
     * Create a comprehensive leader search select component
     * Searches across Cell Leaders, G12 Leaders, and Network Leaders
     */
    protected static function leaderSelect(string $name = 'leader_info', string $label = '👤 Select Leader'): array
    {
        $searchService = app(LeaderSearchService::class);
        $config = LeaderSearchService::getSearchConfig();

        return [
            // Main leader selection field
            Select::make($name)
                ->label($label)
                ->placeholder($config['placeholder'])
                ->options([]) // Start with empty options to force search-only behavior
                ->searchable()
                ->noSearchResultsMessage($config['noResultsMessage'])
                ->loadingMessage($config['loadingMessage'])
                ->searchDebounce($config['searchDebounce'])
                ->getSearchResultsUsing(function (string $search) use ($searchService, $config) {
                    return $searchService->searchAllLeaders($search, $config['defaultLimitPerType']);
                })
                ->getOptionLabelUsing(function ($value) use ($searchService) {
                    return $searchService->getLeaderLabel($value);
                })
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) use ($searchService) {
                    if ($state) {
                        $parsed = $searchService->parseCompositeKey($state);
                        if ($parsed) {
                            $set('leader_type', $parsed['leader_type']);
                            $set('leader_id', $parsed['leader_id']);
                        }
                    }
                }), // Remove dehydrated(false) so the field is submitted
            
            // Hidden fields to store the actual leader relationship data
            TextInput::make('leader_id')
                ->hidden()
                ->dehydrated(),
                
            TextInput::make('leader_type')
                ->hidden()
                ->dehydrated(),
        ];
    }

    /**
     * Create a Cell Leader specific search select component
     */
    protected static function cellLeaderSelect(string $name = 'cell_leader_id', string $label = '👤 Cell Leader'): Select
    {
        $searchService = app(LeaderSearchService::class);
        $config = LeaderSearchService::getSearchConfig();

        return Select::make($name)
            ->label($label)
            ->placeholder('Type to search for Cell Leaders...')
            ->options([])
            ->searchable()
            ->noSearchResultsMessage('No Cell Leaders found. Try a different search term.')
            ->loadingMessage('Searching Cell Leaders...')
            ->searchDebounce($config['searchDebounce'])
            ->getSearchResultsUsing(function (string $search) use ($searchService, $config) {
                $results = $searchService->searchCellLeaders($search, $config['defaultLimitPerType']);
                // Extract just the ID from composite key for this specific search
                $cellLeaderResults = [];
                foreach ($results as $key => $label) {
                    if (str_starts_with($key, 'CellLeader:')) {
                        $id = explode(':', $key)[1];
                        $cellLeaderResults[$id] = str_replace(' (Cell Leader)', '', $label);
                    }
                }
                return $cellLeaderResults;
            })
            ->getOptionLabelUsing(function ($value) use ($searchService) {
                $compositeKey = "CellLeader:{$value}";
                $label = $searchService->getLeaderLabel($compositeKey);
                return $label ? str_replace(' (Cell Leader)', '', $label) : null;
            });
    }

    /**
     * Create a G12 Leader specific search select component
     */
    protected static function g12LeaderSelect(string $name = 'g12_leader_id', string $label = '👤 G12 Leader'): Select
    {
        $searchService = app(LeaderSearchService::class);
        $config = LeaderSearchService::getSearchConfig();

        return Select::make($name)
            ->label($label)
            ->placeholder('Type to search for G12 Leaders...')
            ->options([])
            ->searchable()
            ->noSearchResultsMessage('No G12 Leaders found. Try a different search term.')
            ->loadingMessage('Searching G12 Leaders...')
            ->searchDebounce($config['searchDebounce'])
            ->getSearchResultsUsing(function (string $search) use ($searchService, $config) {
                $results = $searchService->searchG12Leaders($search, $config['defaultLimitPerType']);
                // Extract just the ID from composite key for this specific search
                $g12LeaderResults = [];
                foreach ($results as $key => $label) {
                    if (str_starts_with($key, 'G12Leader:')) {
                        $id = explode(':', $key)[1];
                        $g12LeaderResults[$id] = str_replace(' (G12 Leader)', '', $label);
                    }
                }
                return $g12LeaderResults;
            })
            ->getOptionLabelUsing(function ($value) use ($searchService) {
                $compositeKey = "G12Leader:{$value}";
                $label = $searchService->getLeaderLabel($compositeKey);
                return $label ? str_replace(' (G12 Leader)', '', $label) : null;
            });
    }

    /**
     * Create a Network Leader specific search select component
     */
    protected static function networkLeaderSelect(string $name = 'network_leader_id', string $label = '👤 Network Leader'): Select
    {
        $searchService = app(LeaderSearchService::class);
        $config = LeaderSearchService::getSearchConfig();

        return Select::make($name)
            ->label($label)
            ->placeholder('Type to search for Network Leaders...')
            ->options([])
            ->searchable()
            ->noSearchResultsMessage('No Network Leaders found. Try a different search term.')
            ->loadingMessage('Searching Network Leaders...')
            ->searchDebounce($config['searchDebounce'])
            ->getSearchResultsUsing(function (string $search) use ($searchService, $config) {
                $results = $searchService->searchNetworkLeaders($search, $config['defaultLimitPerType']);
                // Extract just the ID from composite key for this specific search
                $networkLeaderResults = [];
                foreach ($results as $key => $label) {
                    if (str_starts_with($key, 'NetworkLeader:')) {
                        $id = explode(':', $key)[1];
                        $networkLeaderResults[$id] = str_replace(' (Network Leader)', '', $label);
                    }
                }
                return $networkLeaderResults;
            })
            ->getOptionLabelUsing(function ($value) use ($searchService) {
                $compositeKey = "NetworkLeader:{$value}";
                $label = $searchService->getLeaderLabel($compositeKey);
                return $label ? str_replace(' (Network Leader)', '', $label) : null;
            });
    }
}
