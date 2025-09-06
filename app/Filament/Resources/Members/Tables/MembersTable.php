<?php

namespace App\Filament\Resources\Members\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('leader_type')
                    ->label('Leader Type')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'App\\Models\\NetworkLeader' => 'Network Leader',
                        'App\\Models\\G12Leader' => 'G12 Leader',
                        'App\\Models\\SeniorPastor' => 'Senior Pastor',
                        'App\\Models\\CellLeader' => 'Cell Leader',
                        'App\\Models\\CellMember' => 'Cell Member',
                        'App\\Models\\Attender' => 'Attender',

                        default => 'Unknowns',
                    })
                    ->searchable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('sex.name')
                    ->label('Sex')
                    ->sortable(),
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
