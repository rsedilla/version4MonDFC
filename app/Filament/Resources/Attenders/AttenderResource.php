<?php

namespace App\Filament\Resources\Attenders;

use App\Filament\Resources\Attenders\Pages\CreateAttender;
use App\Filament\Resources\Attenders\Pages\EditAttender;
use App\Filament\Resources\Attenders\Pages\ListAttenders;
use App\Filament\Resources\Attenders\Schemas\AttenderForm;
use App\Filament\Resources\Attenders\Tables\AttendersTable;
use App\Models\Attender;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AttenderResource extends Resource
{
    protected static ?string $model = Attender::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AttenderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendersTable::configure($table);
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
            'index' => ListAttenders::route('/'),
            'create' => CreateAttender::route('/create'),
            'edit' => EditAttender::route('/{record}/edit'),
        ];
    }
}
