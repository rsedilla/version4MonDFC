<?php

namespace App\Filament\Resources\CgAttendanceRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use App\Services\CellGroupService;

class CgAttendanceRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Custom search results display
                ViewColumn::make('search_results')
                    ->label('Cell Group Information')
                    ->view('filament.components.cell-group-search-results')
                    ->visible(fn () => session()->has('cell_group_search_results')),
                
                // Normal columns (hidden when search results are shown)
                TextColumn::make('cellGroup.name')
                    ->label('Cell Group')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => !session()->has('cell_group_search_results')),
                
                TextColumn::make('attendee.full_name')
                    ->label('Attendee')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => !session()->has('cell_group_search_results')),
                
                TextColumn::make('present')
                    ->label('Present')
                    ->boolean()
                    ->visible(fn () => !session()->has('cell_group_search_results')),
                
                TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->visible(fn () => !session()->has('cell_group_search_results')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('searchCellGroup')
                    ->label('Search Cell Group')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('primary')
                    ->form([
                        TextInput::make('cell_group_name')
                            ->label('Cell Group Name')
                            ->placeholder('Enter cell group name (e.g., TKD)')
                            ->required()
                            ->helperText('Search for a specific cell group to view its members'),
                    ])
                    ->action(function (array $data) {
                        $cellGroupService = new CellGroupService();
                        $cellGroups = $cellGroupService->getCellGroupWithMembers($data['cell_group_name']);
                        
                        if ($cellGroups->isEmpty()) {
                            Notification::make()
                                ->title('Cell Group Not Found')
                                ->body("No cell group found with name: {$data['cell_group_name']}")
                                ->warning()
                                ->send();
                            return;
                        }
                        
                        // Store the search results in session to be used by the table
                        session(['cell_group_search_results' => $cellGroups->toArray()]);
                        
                        Notification::make()
                            ->title('Search Complete')
                            ->body("Found {$cellGroups->count()} cell group(s). Table updated with members.")
                            ->success()
                            ->send();
                            
                        // Refresh the page to show results
                        redirect()->to(request()->url());
                    }),
                    
                Action::make('clearSearch')
                    ->label('Clear Search')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->action(function () {
                        session()->forget('cell_group_search_results');
                        
                        Notification::make()
                            ->title('Search Cleared')
                            ->body('Returning to original attendance records view.')
                            ->info()
                            ->send();
                            
                        redirect()->to(request()->url());
                    })
                    ->visible(fn () => session()->has('cell_group_search_results')),
                
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
