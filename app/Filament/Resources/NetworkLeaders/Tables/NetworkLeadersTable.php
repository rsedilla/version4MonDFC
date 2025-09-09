<?php

namespace App\Filament\Resources\NetworkLeaders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NetworkLeadersTable
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
                                  ->whereColumn('members.id', 'network_leaders.member_id')
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
                                  ->whereColumn('members.id', 'network_leaders.member_id')
                                  ->where(function ($query) use ($search) {
                                      $query->where('last_name', 'like', "%{$search}%")
                                            ->orWhere('first_name', 'like', "%{$search}%")
                                            ->orWhere('middle_name', 'like', "%{$search}%");
                                  });
                        });
                    })
                    ->sortable(),
                
                TextColumn::make('member_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('leader_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('leader_type')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
