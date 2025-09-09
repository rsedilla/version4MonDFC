<?php

namespace App\Filament\Resources\EmergingLeaders\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmergingLeaderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('member.first_name')
                    ->label('First Name')
                    ->size('lg')
                    ->weight('bold'),
                
                TextEntry::make('member.last_name')
                    ->label('Last Name')
                    ->size('lg')
                    ->weight('bold'),
                
                TextEntry::make('leadership_area')
                    ->label('Leadership Area')
                    ->placeholder('Not specified')
                    ->badge()
                    ->color('primary'),
                
                TextEntry::make('identified_date')
                    ->label('Date Identified')
                    ->date()
                    ->icon('heroicon-o-calendar'),
                
                IconEntry::make('is_active')
                    ->label('Active Status')
                    ->boolean(),
                
                TextEntry::make('notes')
                    ->label('Notes')
                    ->placeholder('No notes available')
                    ->columnSpanFull(),
                
                TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->color('gray'),
                    
                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->color('gray'),
            ]);
    }
}
