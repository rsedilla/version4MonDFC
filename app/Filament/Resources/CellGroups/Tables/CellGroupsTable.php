<?php

namespace App\Filament\Resources\CellGroups\Tables;

use App\Filament\Helpers\CellGroupMemberActionHelper;
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
                    ->label('Cell Group Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('leader_first_name')
                    ->label('Leader First Name')
                    ->getStateUsing(function ($record) {
                        if ($record->leader_type && $record->leader_id) {
                            $leaderModel = app($record->leader_type);
                            $leader = $leaderModel::find($record->leader_id);
                            return $leader?->member?->first_name ?? 'N/A';
                        }
                        return 'N/A';
                    })
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('leader_last_name')
                    ->label('Leader Last Name')
                    ->getStateUsing(function ($record) {
                        if ($record->leader_type && $record->leader_id) {
                            $leaderModel = app($record->leader_type);
                            $leader = $leaderModel::find($record->leader_id);
                            return $leader?->member?->last_name ?? 'N/A';
                        }
                        return 'N/A';
                    })
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('info.day')
                    ->label('Meeting Day')
                    ->sortable()
                    ->default('Not Set'),
                
                TextColumn::make('info.time')
                    ->label('Meeting Time')
                    ->time('g:i A')
                    ->sortable()
                    ->default('Not Set'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                CellGroupMemberActionHelper::makeAssignMembersAction(),
                CellGroupMemberActionHelper::makeViewMembersAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    CellGroupMemberActionHelper::makeBulkAssignMembersAction(),
                ]),
            ]);
    }
}
