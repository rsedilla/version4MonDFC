<?php

namespace App\Filament\Resources\CellLeaders;

use App\Filament\Resources\CellLeaders\Pages\CreateCellLeader;
use App\Filament\Resources\CellLeaders\Pages\EditCellLeader;
use App\Filament\Resources\CellLeaders\Pages\ListCellLeaders;
use App\Filament\Resources\CellLeaders\Pages\ViewCellLeader;
use App\Filament\Resources\CellLeaders\Schemas\CellLeaderForm;
use App\Filament\Resources\CellLeaders\Schemas\CellLeaderInfolist;
use App\Filament\Resources\CellLeaders\Tables\CellLeadersTable;
use App\Models\CellLeader;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CellLeaderResource extends Resource
{
    protected static ?string $model = CellLeader::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return CellLeaderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CellLeaderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CellLeadersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCellLeaders::route('/'),
            'create' => CreateCellLeader::route('/create'),
            'view' => ViewCellLeader::route('/{record}'),
            'edit' => EditCellLeader::route('/{record}/edit'),
        ];
    }
}
