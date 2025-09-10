<?php

namespace App\Filament\Resources\Members\Tables;

use App\Filament\Helpers\DirectLeaderActionHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->searchDebounce('750ms')
            ->columns([
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('middle_name')
                    ->label('Middle Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('trainingTypes.name')
                    ->label('Training')
                    ->formatStateUsing(function ($record) {
                        if (!$record->relationLoaded('trainingTypes')) {
                            $record->load('trainingTypes');
                        }
                        return $record->trainingTypes->pluck('name')->join(', ') ?: 'None';
                    })
                    ->badge()
                    ->color('info')
                    ->toggleable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('trainingTypes', function (Builder $query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('training_status')
                    ->label('Status')
                    ->formatStateUsing(function ($record) {
                        if (!$record->relationLoaded('trainingTypes')) {
                            $record->load(['trainingTypes' => function ($query) {
                                $query->with('pivot');
                            }]);
                        }
                        
                        $statuses = $record->trainingTypes->map(function ($trainingType) {
                            $statusId = $trainingType->pivot->training_status_id ?? null;
                            if ($statusId) {
                                $status = \App\Models\TrainingStatus::find($statusId);
                                return $status?->name ?? 'Unknown';
                            }
                            return 'No status';
                        })->filter()->unique();
                        
                        return $statuses->join(', ') ?: 'No status';
                    })
                    ->badge()
                    ->color(fn($state) => str_contains($state, 'Graduate') ? 'info' : (str_contains($state, 'Enrolled') ? 'success' : 'gray'))
                    ->toggleable(),
                TextColumn::make('directLeader.member.full_name')
                    ->label('Direct Leader')
                    ->placeholder('None assigned')
                    ->formatStateUsing(fn($state) => $state ? "👤 {$state}" : 'None assigned')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->searchable(['directLeader.member.first_name', 'directLeader.member.last_name'])
                    ->sortable(),
                TextColumn::make('member_leader_type')
                    ->label('Member Type')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'App\\Models\\NetworkLeader' => 'Network Leader',
                        'App\\Models\\G12Leader' => 'G12 Leader',
                        'App\\Models\\SeniorPastor' => 'Senior Pastor',
                        'App\\Models\\CellLeader' => 'Cell Leader',
                        'App\\Models\\CellMember' => 'Cell Member',
                        'App\\Models\\Attender' => 'Attender',
                        default => 'Unknown',
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $typeMap = [
                            'network' => 'App\\Models\\NetworkLeader',
                            'g12' => 'App\\Models\\G12Leader',
                            'senior' => 'App\\Models\\SeniorPastor',
                            'pastor' => 'App\\Models\\SeniorPastor',
                            'cell' => 'App\\Models\\CellLeader',
                            'leader' => ['App\\Models\\CellLeader', 'App\\Models\\G12Leader', 'App\\Models\\NetworkLeader'],
                            'member' => 'App\\Models\\CellMember',
                            'attender' => 'App\\Models\\Attender',
                        ];
                        
                        $searchLower = strtolower($search);
                        foreach ($typeMap as $keyword => $types) {
                            if (str_contains($searchLower, $keyword)) {
                                if (is_array($types)) {
                                    return $query->whereIn('member_leader_type', $types);
                                } else {
                                    return $query->where('member_leader_type', $types);
                                }
                            }
                        }
                        
                        return $query;
                    }),
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
                DirectLeaderActionHelper::makeAssignDirectLeaderAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    DirectLeaderActionHelper::makeBulkAssignDirectLeaderAction(),
                ]),
            ]);
    }
}
