<?php

namespace App\Filament\Resources\CellLeaders\Schemas;

use App\Models\Member;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;

class CellLeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->label('Member')
                    ->placeholder('Type to search for members...')
                    ->options([]) // Start with empty options to force search-only behavior
                    ->searchable()
                    ->noSearchResultsMessage('No members found. Try a different search term.')
                    ->loadingMessage('Searching members...')
                    ->searchDebounce(300) // Wait 300ms before searching
                    ->getSearchResultsUsing(function (string $search) {
                        // Only search if user types at least 2 characters
                        if (strlen(trim($search)) < 2) {
                            return [];
                        }
                        
                        try {
                            // Optimized search query with indexes
                            $members = Member::select(['id', 'first_name', 'last_name', 'email'])
                                ->where(function ($query) use ($search) {
                                    $searchTerm = strtolower(trim($search));
                                    $query->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchTerm}%"])
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
                                    strtolower($search) . '%',
                                    strtolower($search) . '%', 
                                    '%' . strtolower($search) . '%'
                                ])
                                ->limit(100) // Limit results for better performance
                                ->get();
                                
                            return $members->mapWithKeys(function ($member) {
                                return [$member->id => $member->first_name . ' ' . $member->last_name];
                            })->toArray();
                            
                        } catch (\Exception $e) {
                            // Enhanced fallback with error logging
                            Log::error('Member search error in CellLeaderForm: ' . $e->getMessage());
                            
                            return Member::select(['id', 'first_name', 'last_name'])
                                ->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%")
                                ->orderBy('first_name')
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(function ($member) {
                                    return [$member->id => $member->first_name . ' ' . $member->last_name];
                                })
                                ->toArray();
                        }
                    })
                    ->getOptionLabelUsing(function ($value) {
                        // Efficiently get selected option label
                        if (!$value) return null;
                        $member = Member::select(['first_name', 'last_name'])->find($value);
                        return $member ? $member->first_name . ' ' . $member->last_name : null;
                    })
                    ->required(),
                    
                TextInput::make('user_id')
                    ->numeric(),
            ]);
    }
}
