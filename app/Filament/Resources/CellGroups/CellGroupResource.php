<?php

namespace App\Filament\Resources\CellGroups;

use App\Filament\Resources\CellGroups\Pages\CreateCellGroup;
use App\Filament\Resources\CellGroups\Pages\EditCellGroup;
use App\Filament\Resources\CellGroups\Pages\ListCellGroups;
use App\Filament\Resources\CellGroups\Pages\ViewCellGroup;
use App\Filament\Resources\CellGroups\Schemas\CellGroupForm;
use App\Filament\Resources\CellGroups\Schemas\CellGroupInfolist;
use App\Filament\Resources\CellGroups\Tables\CellGroupsTable;
use App\Models\CellGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CellGroupResource extends Resource
{
    protected static ?string $model = CellGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return CellGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CellGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CellGroupsTable::configure($table);
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
            'index' => ListCellGroups::route('/'),
            'create' => CreateCellGroup::route('/create'),
            'view' => ViewCellGroup::route('/{record}'),
            'edit' => EditCellGroup::route('/{record}/edit'),
        ];
    }
}
