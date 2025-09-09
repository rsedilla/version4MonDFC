<?php

namespace App\Filament\Resources\Equipping\Tables;

use App\Models\TrainingType;
use App\Models\TrainingStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EquippingTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.full_name')
                    ->label('Member')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                
                TextColumn::make('member.directLeader.member.full_name')
                    ->label('Direct Leader')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->member->leader_type || !$record->member->leader_id || !$state) {
                            return 'None assigned';
                        }
                        $member = $record->member->directLeader?->member;
                        if ($member) {
                            $middleInitial = $member->middle_name ? strtoupper(substr($member->middle_name, 0, 1)) . '.' : '';
                            return trim($member->first_name . ' ' . $middleInitial . ' ' . $member->last_name);
                        }
                        return 'None assigned';
                    })
                    ->searchable(false)
                    ->sortable(false),
                
                TextColumn::make('trainingType.name')
                    ->label('Training Type')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('trainingStatus.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Enrolled' => 'success',
                        'Graduate' => 'info',
                        'Not Enrolled' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('training_type_id')
                    ->label('Training Type')
                    ->options(TrainingType::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('training_status_id')
                    ->label('Status')
                    ->options(TrainingStatus::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
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
