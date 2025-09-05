<?php

namespace App\Filament\Resources\CellMembers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CellMemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('member.full_name')
                    ->label('Member Name'),
                TextEntry::make('cellGroup.name')
                    ->label('Cell Group'),
                TextEntry::make('joined_date')
                    ->label('Joined Date')
                    ->date(),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'transferred' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
