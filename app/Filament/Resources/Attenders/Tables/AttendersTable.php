<?php

namespace App\Filament\Resources\Attenders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

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
                    ->searchable(query: function ($query, $search) {
                        return $query->whereExists(function ($query) use ($search) {
                            $query->select(DB::raw(1))
                                  ->from('members')
                                  ->whereColumn('members.id', 'attenders.consolidator_id')
                                  ->where(function ($query) use ($search) {
                                      $query->where('first_name', 'like', "%{$search}%")
                                            ->orWhere('last_name', 'like', "%{$search}%")
                                            ->orWhere('middle_name', 'like', "%{$search}%");
                                  });
                        });
                    })
                    ->sortable()
                    ->placeholder('Not assigned')
                    ->badge()
                    ->color('info'),
                
                // Progress tracking columns
                TextColumn::make('suyln_progress')
                    ->label('SUYLN')
                    ->state(function ($record) {
                        $completed = 0;
                        for ($i = 1; $i <= 10; $i++) {
                            if ($record->{"suyln_lesson_$i"}) {
                                $completed++;
                            }
                        }
                        return "$completed/10";
                    })
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state === '10/10' => 'success',
                        (int)explode('/', $state)[0] >= 8 => 'warning',
                        (int)explode('/', $state)[0] >= 5 => 'info',
                        (int)explode('/', $state)[0] >= 1 => 'primary',
                        default => 'gray'
                    })
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
                    ->state(function ($record) {
                        $completed = 0;
                        for ($i = 1; $i <= 4; $i++) {
                            if ($record->{"sunday_service_$i"}) {
                                $completed++;
                            }
                        }
                        return "$completed/4";
                    })
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state === '4/4' => 'success',
                        (int)explode('/', $state)[0] >= 3 => 'warning',
                        (int)explode('/', $state)[0] >= 2 => 'info',
                        (int)explode('/', $state)[0] >= 1 => 'primary',
                        default => 'gray'
                    })
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
                    ->state(function ($record) {
                        $completed = 0;
                        for ($i = 1; $i <= 4; $i++) {
                            if ($record->{"cell_group_$i"}) {
                                $completed++;
                            }
                        }
                        return "$completed/4";
                    })
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state === '4/4' => 'success',
                        (int)explode('/', $state)[0] >= 3 => 'warning',
                        (int)explode('/', $state)[0] >= 2 => 'info',
                        (int)explode('/', $state)[0] >= 1 => 'primary',
                        default => 'gray'
                    })
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
