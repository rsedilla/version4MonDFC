<?php

namespace App\Filament\Resources\Equipping\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EquippingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('member.full_name')
                    ->label('Member')
                    ->size('lg')
                    ->weight('bold'),
                
                TextEntry::make('trainingType.name')
                    ->label('Training Type')
                    ->badge()
                    ->color('primary'),
                
                TextEntry::make('trainingStatus.name')
                    ->label('Training Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Enrolled' => 'success',
                        'Graduate' => 'info',
                        'Not Enrolled' => 'gray',
                        default => 'gray',
                    }),
                
                TextEntry::make('created_at')
                    ->label('Assigned Date')
                    ->date(),
                
                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime(),
            ]);
    }
}
