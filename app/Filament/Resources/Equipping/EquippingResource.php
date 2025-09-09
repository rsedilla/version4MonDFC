<?php

namespace App\Filament\Resources\Equipping;

use App\Filament\Resources\Equipping\Pages\CreateEquipping;
use App\Filament\Resources\Equipping\Pages\EditEquipping;
use App\Filament\Resources\Equipping\Pages\ListEquipping;
use App\Filament\Resources\Equipping\Pages\ViewEquipping;
use App\Filament\Resources\Equipping\Schemas\EquippingForm;
use App\Filament\Resources\Equipping\Schemas\EquippingInfolist;
use App\Filament\Resources\Equipping\Tables\EquippingTable;
use App\Models\MemberTrainingType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EquippingResource extends Resource
{
    protected static ?string $model = MemberTrainingType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $slug = 'equipping';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Equipping';

    protected static ?string $modelLabel = 'Equipping Record';

    protected static ?string $pluralModelLabel = 'Equipping Records';

    public static function form(Schema $schema): Schema
    {
        return EquippingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EquippingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EquippingTable::configure($table);
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
            'index' => ListEquipping::route('/'),
            'create' => CreateEquipping::route('/create'),
            'view' => ViewEquipping::route('/{record}'),
            'edit' => EditEquipping::route('/{record}/edit'),
        ];
    }
}
