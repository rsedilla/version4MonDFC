<?php

namespace App\Filament\Resources\CgAttendanceRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CgAttendanceRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cell_group_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('attendee_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('attendee_type')
                    ->searchable(),
                TextColumn::make('year'),
                TextColumn::make('month')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('week_number')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('present')
                    ->boolean(),
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
