<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('leader_id')
                    ->numeric(),
                TextEntry::make('leader_type'),
                TextEntry::make('first_name'),
                TextEntry::make('middle_name'),
                TextEntry::make('last_name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('phone_number'),
                TextEntry::make('birthday')
                    ->date(),
                TextEntry::make('address'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('civil_status_id')
                    ->numeric(),
                TextEntry::make('sex_id')
                    ->numeric(),
            ]);
    }
}
