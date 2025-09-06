<?php

namespace App\Filament\Resources\NetworkLeaders;


use App\Filament\Resources\NetworkLeaders\Pages\CreateNetworkLeader;
use App\Filament\Resources\NetworkLeaders\Pages\EditNetworkLeader;
use App\Filament\Resources\NetworkLeaders\Pages\ListNetworkLeaders;
use App\Filament\Resources\NetworkLeaders\Schemas\NetworkLeaderForm;
use App\Filament\Resources\NetworkLeaders\Tables\NetworkLeadersTable;
use App\Models\NetworkLeader;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NetworkLeaderResource extends Resource
{
    protected static ?string $model = NetworkLeader::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'member.full_name';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return NetworkLeaderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NetworkLeadersTable::configure($table);
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
            'index' => ListNetworkLeaders::route('/'),
            'create' => CreateNetworkLeader::route('/create'),
            'edit' => EditNetworkLeader::route('/{record}/edit'),
        ];
    }
}
