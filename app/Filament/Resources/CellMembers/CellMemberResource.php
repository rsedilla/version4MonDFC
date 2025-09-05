<?php

namespace App\Filament\Resources\CellMembers;

use App\Filament\Resources\CellMembers\Pages\CreateCellMember;
use App\Filament\Resources\CellMembers\Pages\EditCellMember;
use App\Filament\Resources\CellMembers\Pages\ListCellMembers;
use App\Filament\Resources\CellMembers\Pages\ViewCellMember;
use App\Filament\Resources\CellMembers\Schemas\CellMemberForm;
use App\Filament\Resources\CellMembers\Schemas\CellMemberInfolist;
use App\Filament\Resources\CellMembers\Tables\CellMembersTable;
use App\Models\CellMember;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CellMemberResource extends Resource
{
    protected static ?string $model = CellMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Cell Members';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return CellMemberForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CellMemberInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CellMembersTable::configure($table);
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
            'index' => ListCellMembers::route('/'),
            'create' => CreateCellMember::route('/create'),
            'view' => ViewCellMember::route('/{record}'),
            'edit' => EditCellMember::route('/{record}/edit'),
        ];
    }
}