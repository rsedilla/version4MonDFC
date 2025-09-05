<?php

namespace App\Filament\Resources\CellMembers\Schemas;

use App\Models\CellGroup;
use App\Models\Member;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class CellMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->label('Member')
                    ->options(function () {
                        return Member::all()->mapWithKeys(function ($member) {
                            return [$member->id => $member->full_name];
                        });
                    })
                    ->searchable()
                    ->required(),
                
                Select::make('cell_group_id')
                    ->label('Cell Group')
                    ->options(CellGroup::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                
                DatePicker::make('joined_date')
                    ->label('Joined Date')
                    ->default(now())
                    ->required(),
                
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'transferred' => 'Transferred',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
