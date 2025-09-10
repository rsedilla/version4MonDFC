<?php

namespace App\Filament\Resources\CellGroups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CellGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('📝 Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('info.cell_group_idnum')
                    ->label('🔢 Group ID')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('cellGroupType.name')
                    ->label('📋 Type')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('leader.member.full_name')
                    ->label('👤 Leader')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        if ($record->leader && $record->leader->member) {
                            return $record->leader->member->full_name;
                        }
                        return 'No Leader Assigned';
                    }),
                TextColumn::make('info.day')
                    ->label('📅 Meeting Day')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('info.time')
                    ->label('🕐 Time')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('H:i') : null),
                TextColumn::make('info.location')
                    ->label('📍 Location')
                    ->limit(30),
                TextColumn::make('is_active')
                    ->label('✅ Status')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
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
