<?php

namespace App\Filament\Resources\CgAttendanceRecords\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CgAttendanceRecordInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('cell_group_id')
                    ->numeric(),
                TextEntry::make('attendee_id')
                    ->numeric(),
                TextEntry::make('attendee_type'),
                TextEntry::make('year'),
                TextEntry::make('month')
                    ->numeric(),
                TextEntry::make('week_number')
                    ->numeric(),
                IconEntry::make('present')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
