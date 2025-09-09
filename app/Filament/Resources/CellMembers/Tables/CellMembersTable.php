<?php

namespace App\Filament\Resources\CellMembers\Tables;

use App\Models\CellGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CellMembersTable
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
                                  ->whereColumn('members.id', 'cell_members.member_id')
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
                                  ->whereColumn('members.id', 'cell_members.member_id')
                                  ->where(function ($query) use ($search) {
                                      $query->where('last_name', 'like', "%{$search}%")
                                            ->orWhere('first_name', 'like', "%{$search}%")
                                            ->orWhere('middle_name', 'like', "%{$search}%");
                                  });
                        });
                    })
                    ->sortable(),
                
                TextColumn::make('cellGroup.name')
                    ->label('Cell Group')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('joined_date')
                    ->label('Joined Date')
                    ->date()
                    ->sortable(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'transferred' => 'danger',
                        default => 'gray',
                    }),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'transferred' => 'Transferred',
                    ]),
                
                SelectFilter::make('cell_group_id')
                    ->label('Cell Group')
                    ->options(CellGroup::all()->pluck('name', 'id')),
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
