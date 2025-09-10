<?php

namespace App\Filament\Resources\Attenders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('member.last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('consolidator.full_name')
                    ->label('Consolidator')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not assigned')
                    ->badge()
                    ->color('info'),
                
                // Progress tracking columns
                TextColumn::make('suyln_progress')
                    ->label('SUYLN')
                    ->getStateUsing(fn($record) => $record->suyln_progress)
                    ->badge()
                    ->color(fn($record) => $record->suyln_progress_color)
                    ->icon(fn ($state) => match(true) {
                        $state === '10/10' => 'heroicon-o-trophy',
                        (int)explode('/', $state)[0] >= 8 => 'heroicon-o-star',
                        (int)explode('/', $state)[0] >= 5 => 'heroicon-o-academic-cap',
                        default => null
                    })
                    ->sortable(query: function ($query, string $direction) {
                        return $query->selectRaw('
                            (CASE WHEN suyln_lesson_1 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN suyln_lesson_2 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN suyln_lesson_3 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN suyln_lesson_4 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN suyln_lesson_5 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN suyln_lesson_6 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN suyln_lesson_7 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN suyln_lesson_8 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN suyln_lesson_9 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN suyln_lesson_10 IS NOT NULL THEN 1 ELSE 0 END) as suyln_count
                        ')->orderBy('suyln_count', $direction);
                    }),

                TextColumn::make('dcc_progress')
                    ->label('DCC')
                    ->getStateUsing(fn($record) => $record->dcc_progress)
                    ->badge()
                    ->color(fn($record) => $record->dcc_progress_color)
                    ->icon(fn ($state) => match(true) {
                        $state === '4/4' => 'heroicon-o-check-circle',
                        (int)explode('/', $state)[0] >= 3 => 'heroicon-o-clock',
                        default => null
                    })
                    ->sortable(query: function ($query, string $direction) {
                        return $query->selectRaw('
                            (CASE WHEN sunday_service_1 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN sunday_service_2 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN sunday_service_3 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN sunday_service_4 IS NOT NULL THEN 1 ELSE 0 END) as dcc_count
                        ')->orderBy('dcc_count', $direction);
                    }),

                TextColumn::make('cg_progress')
                    ->label('CG')
                    ->getStateUsing(fn($record) => $record->cg_progress)
                    ->badge()
                    ->color(fn($record) => $record->cg_progress_color)
                    ->icon(fn ($state) => match(true) {
                        $state === '4/4' => 'heroicon-o-user-group',
                        (int)explode('/', $state)[0] >= 3 => 'heroicon-o-users',
                        default => null
                    })
                    ->sortable(query: function ($query, string $direction) {
                        return $query->selectRaw('
                            (CASE WHEN cell_group_1 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN cell_group_2 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN cell_group_3 IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN cell_group_4 IS NOT NULL THEN 1 ELSE 0 END) as cg_count
                        ')->orderBy('cg_count', $direction);
                    }),

                // Overall progress indicator
                TextColumn::make('overall_progress')
                    ->label('Overall')
                    ->getStateUsing(fn($record) => $record->overall_progress . '%')
                    ->badge()
                    ->color(fn($record) => match(true) {
                        $record->overall_progress >= 90 => 'success',
                        $record->overall_progress >= 70 => 'warning',
                        $record->overall_progress >= 50 => 'info',
                        $record->overall_progress >= 25 => 'primary',
                        default => 'gray'
                    })
                    ->icon(fn($record) => match(true) {
                        $record->overall_progress === 100 => 'heroicon-o-check-badge',
                        $record->overall_progress >= 75 => 'heroicon-o-shield-check',
                        default => null
                    })
                    ->toggleable(),
                TextColumn::make('suyln_lesson_1')
                    ->label('SUYLN L1')
                    ->date()
                    ->placeholder('Not completed')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('suyln_lesson_2')
                    ->label('SUYLN L2')
                    ->date()
                    ->placeholder('Not completed')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('suyln_lesson_3')
                    ->label('SUYLN L3')
                    ->date()
                    ->placeholder('Not completed')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('suyln_lesson_4')
                    ->label('SUYLN L4')
                    ->date()
                    ->placeholder('Not completed')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('suyln_lesson_5')
                    ->label('SUYLN L5')
                    ->date()
                    ->placeholder('Not completed')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sunday_service_1')
                    ->label('Sun Service 1')
                    ->date()
                    ->placeholder('Not attended')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sunday_service_4')
                    ->label('Sun Service 4')
                    ->date()
                    ->placeholder('Not attended')
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cell_group_1')
                    ->label('Cell Group 1')
                    ->date()
                    ->placeholder('Not attended')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cell_group_4')
                    ->label('Cell Group 4')
                    ->date()
                    ->placeholder('Not attended')
                    ->badge()
                    ->color('success')
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
