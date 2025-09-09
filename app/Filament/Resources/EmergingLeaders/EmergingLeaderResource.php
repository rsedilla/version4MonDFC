<?php

namespace App\Filament\Resources\EmergingLeaders;

use App\Filament\Resources\EmergingLeaders\Pages\CreateEmergingLeader;
use App\Filament\Resources\EmergingLeaders\Pages\EditEmergingLeader;
use App\Filament\Resources\EmergingLeaders\Pages\ListEmergingLeaders;
use App\Filament\Resources\EmergingLeaders\Pages\ViewEmergingLeader;
use App\Filament\Resources\EmergingLeaders\Schemas\EmergingLeaderForm;
use App\Filament\Resources\EmergingLeaders\Schemas\EmergingLeaderInfolist;
use App\Filament\Resources\EmergingLeaders\Tables\EmergingLeadersTable;
use App\Models\EmergingLeader;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmergingLeaderResource extends Resource
{
    protected static ?string $model = EmergingLeader::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Emerging Leaders';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'member.name';

    public static function form(Schema $schema): Schema
    {
        return EmergingLeaderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmergingLeaderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmergingLeadersTable::configure($table);
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
            'index' => ListEmergingLeaders::route('/'),
            'create' => CreateEmergingLeader::route('/create'),
            'view' => ViewEmergingLeader::route('/{record}'),
            'edit' => EditEmergingLeader::route('/{record}/edit'),
        ];
    }
}
