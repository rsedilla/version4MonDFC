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
                    ->searchable(query: function ($query, $search) {
                        return $query->where(function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('middle_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(query: function ($query, $search) {
                        return $query->where(function ($query) use ($search) {
                            $query->where('last_name', 'like', "%{$search}%")
                                  ->orWhere('first_name', 'like', "%{$search}%")
                                  ->orWhere('middle_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                    TextColumn::make('trainingTypes.name')
                        ->label('Training')
                        ->getStateUsing(fn($record) => $record->trainingTypes?->pluck('name')->join(', '))
                        ->placeholder('None')
                        ->badge()
                        ->color('info'),
                    TextColumn::make('trainingTypes.pivot.training_status_id')
                        ->label('Status')
                        ->getStateUsing(function($record) {
                            return $record->trainingTypes?->map(function($trainingType) {
                                $statusId = $trainingType->pivot->training_status_id;
                                $status = \App\Models\TrainingStatus::find($statusId);
                                return $status ? $status->name : 'Unknown';
                            })->join(', ');
                        })
                        ->placeholder('No status')
                        ->badge()
                        ->color(fn($state) => str_contains($state, 'Graduate') ? 'info' : (str_contains($state, 'Enrolled') ? 'success' : 'gray')),
                TextColumn::make('directLeader.member.full_name')
                    ->label('Direct Leader')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->leader_type || !$record->leader_id || !$state) {
                            return 'None assigned';
                        }
                        $member = $record->directLeader?->member;
                        if ($member) {
                            $middleInitial = $member->middle_name ? strtoupper(substr($member->middle_name, 0, 1)) . '.' : '';
                            return trim($member->first_name . ' ' . $middleInitial . ' ' . $member->last_name);
                        }
                        return $state;
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
