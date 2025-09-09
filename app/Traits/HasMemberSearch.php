<?php

namespace App\Traits;

use App\Services\MemberSearchService;
use Filament\Forms\Components\Select;

trait HasMemberSearch
{
    /**
     * Create a consolidator search select component
     */
    protected static function consolidatorSelect(string $name = 'consolidator_id', string $label = '👨‍🏫 Consolidator'): Select
    {
        $searchService = app(MemberSearchService::class);
        $config = MemberSearchService::getSearchConfig();

        return Select::make($name)
            ->label($label)
            ->placeholder($config['placeholder'])
            ->options([]) // Start with empty options to force search-only behavior
            ->searchable()
            ->noSearchResultsMessage($config['noResultsMessage'])
            ->loadingMessage($config['loadingMessage'])
            ->searchDebounce($config['searchDebounce'])
            ->getSearchResultsUsing(function (string $search) use ($searchService, $config) {
                return $searchService->searchConsolidators($search, $config['defaultLimit']);
            })
            ->getOptionLabelUsing(function ($value) use ($searchService) {
                return $searchService->getMemberLabel($value);
            });
    }

    /**
     * Create a member search select component
     */
    protected static function memberSelect(string $name = 'member_id', string $label = 'Member'): Select
    {
        $searchService = app(MemberSearchService::class);
        $config = MemberSearchService::getSearchConfig();

        return Select::make($name)
            ->label($label)
            ->placeholder($config['placeholder'])
            ->options([]) // Start with empty options to force search-only behavior
            ->searchable()
            ->noSearchResultsMessage($config['noResultsMessage'])
            ->loadingMessage($config['loadingMessage'])
            ->searchDebounce($config['searchDebounce'])
            ->getSearchResultsUsing(function (string $search) use ($searchService, $config) {
                return $searchService->searchMembers($search, $config['defaultLimit']);
            })
            ->getOptionLabelUsing(function ($value) use ($searchService) {
                return $searchService->getMemberLabel($value);
            });
    }

    /**
     * Create a member search select component with custom exclusions
     */
    protected static function memberSelectWithExclusions(
        array $excludedIds = [], 
        string $name = 'member_id', 
        string $label = 'Member'
    ): Select {
        $searchService = app(MemberSearchService::class);
        $config = MemberSearchService::getSearchConfig();

        return Select::make($name)
            ->label($label)
            ->placeholder($config['placeholder'])
            ->options([]) // Start with empty options to force search-only behavior
            ->searchable()
            ->noSearchResultsMessage($config['noResultsMessage'])
            ->loadingMessage($config['loadingMessage'])
            ->searchDebounce($config['searchDebounce'])
            ->getSearchResultsUsing(function (string $search) use ($searchService, $config, $excludedIds) {
                return $searchService->searchMembersWithExclusions($search, $excludedIds, $config['defaultLimit']);
            })
            ->getOptionLabelUsing(function ($value) use ($searchService) {
                return $searchService->getMemberLabel($value);
            });
    }
}
