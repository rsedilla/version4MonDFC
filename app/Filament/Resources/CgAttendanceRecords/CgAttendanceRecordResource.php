<?php

namespace App\Filament\Resources\CgAttendanceRecords;

use App\Filament\Resources\CgAttendanceRecords\Pages\CreateCgAttendanceRecord;
use App\Filament\Resources\CgAttendanceRecords\Pages\EditCgAttendanceRecord;
use App\Filament\Resources\CgAttendanceRecords\Pages\ListCgAttendanceRecords;
use App\Filament\Resources\CgAttendanceRecords\Pages\ViewCgAttendanceRecord;
use App\Filament\Resources\CgAttendanceRecords\Schemas\CgAttendanceRecordForm;
use App\Filament\Resources\CgAttendanceRecords\Schemas\CgAttendanceRecordInfolist;
use App\Filament\Resources\CgAttendanceRecords\Tables\CgAttendanceRecordsTable;
use App\Models\CgAttendanceRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CgAttendanceRecordResource extends Resource
{
    protected static ?string $model = CgAttendanceRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Cell Group Attendance';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return CgAttendanceRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CgAttendanceRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CgAttendanceRecordsTable::configure($table);
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
            'index' => ListCgAttendanceRecords::route('/'),
            'create' => CreateCgAttendanceRecord::route('/create'),
            'view' => ViewCgAttendanceRecord::route('/{record}'),
            'edit' => EditCgAttendanceRecord::route('/{record}/edit'),
        ];
    }
}
