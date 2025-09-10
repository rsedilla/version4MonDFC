<?php

namespace App\Filament\Resources\Members\Tables;

use App\Filament\Helpers\DirectLeaderActionHelper;
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
                    ->toggleable(),
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
                TextColumn::make('direct_leader')
                    ->label('Direct Leader')
                    ->formatStateUsing(function ($record) {
                        if (!$record->leader_type || !$record->leader_id) {
                            return 'None assigned';
                        }
                        
                        if (!$record->relationLoaded('directLeader')) {
                            $record->load(['directLeader.member']);
                        }
                        
                        $member = $record->directLeader?->member;
                        if ($member) {
                            $middleInitial = $member->middle_name ? strtoupper(substr($member->middle_name, 0, 1)) . '.' : '';
                            return trim($member->first_name . ' ' . $middleInitial . ' ' . $member->last_name);
                        }
                        
                        return 'None assigned';
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('directLeader.member', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),
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
