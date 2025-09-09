<?php

namespace App\Filament\Resources\EmergingLeaders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmergingLeadersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.first_name')
                    ->label('First Name')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereExists(function ($query) use ($search) {
                            $query->select(\Illuminate\Support\Facades\DB::raw(1))
                                  ->from('members')
                                  ->whereColumn('members.id', 'emerging_leaders.member_id')
                                  ->where(function ($query) use ($search) {
                                      $query->where('first_name', 'like', "%{$search}%")
                                            ->orWhere('last_name', 'like', "%{$search}%")
                                            ->orWhere('middle_name', 'like', "%{$search}%");
                                  });
                        });
                    })
                    ->sortable(),
                TextColumn::make('member.last_name')
                    ->label('Last Name')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereExists(function ($query) use ($search) {
                            $query->select(\Illuminate\Support\Facades\DB::raw(1))
                                  ->from('members')
                                  ->whereColumn('members.id', 'emerging_leaders.member_id')
                                  ->where(function ($query) use ($search) {
                                      $query->where('last_name', 'like', "%{$search}%")
                                            ->orWhere('first_name', 'like', "%{$search}%")
                                            ->orWhere('middle_name', 'like', "%{$search}%");
                                  });
                        });
                    })
                    ->sortable(),
                TextColumn::make('leadership_area')
                    ->label('Leadership Area')
                    ->searchable()
                    ->placeholder('Not specified'),
                TextColumn::make('identified_date')
                    ->label('Date Identified')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->placeholder('No notes')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
