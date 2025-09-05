<?php

namespace App\Filament\Resources\G12Leaders;

use App\Filament\Resources\G12Leaders\Pages\CreateG12Leader;
use App\Filament\Resources\G12Leaders\Pages\EditG12Leader;
use App\Filament\Resources\G12Leaders\Pages\ListG12Leaders;
use App\Filament\Resources\G12Leaders\Pages\ViewG12Leader;
use App\Filament\Resources\G12Leaders\Schemas\G12LeaderForm;
use App\Filament\Resources\G12Leaders\Schemas\G12LeaderInfolist;
use App\Filament\Resources\G12Leaders\Tables\G12LeadersTable;
use App\Models\G12Leader;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class G12LeaderResource extends Resource
{
    protected static ?string $model = G12Leader::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return G12LeaderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return G12LeaderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return G12LeadersTable::configure($table);
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
            'index' => ListG12Leaders::route('/'),
            'create' => CreateG12Leader::route('/create'),
            'view' => ViewG12Leader::route('/{record}'),
            'edit' => EditG12Leader::route('/{record}/edit'),
        ];
    }
}
