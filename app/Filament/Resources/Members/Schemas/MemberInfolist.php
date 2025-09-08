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
                TextEntry::make('full_name')
                    ->label('Full Name')
                    ->size('lg')
                    ->weight('bold'),
                
                TextEntry::make('leader')
                    ->label('Direct Leader')
                    ->formatStateUsing(function ($record) {
                        if (!$record->leader) return 'No direct leader assigned';
                        
                        // Get leader name based on leader type
                        $leaderName = null;
                        if ($record->leader->member) {
                            // If leader has a member relationship (SeniorPastor, NetworkLeader, etc.)
                            $leaderName = $record->leader->member->full_name;
                        } elseif (method_exists($record->leader, 'getFullNameAttribute')) {
                            // If leader itself has full_name attribute
                            $leaderName = $record->leader->full_name;
                        } elseif (isset($record->leader->name)) {
                            // If leader has a name field
                            $leaderName = $record->leader->name;
                        } else {
                            $leaderName = 'Unknown';
                        }
                        
                        $leaderRole = match(class_basename($record->leader_type)) {
                            'SeniorPastor' => 'Senior Pastor',
                            'NetworkLeader' => 'Network Leader', 
                            'CellLeader' => 'Cell Leader',
                            'G12Leader' => 'G12 Leader',
                            default => 'Leader'
                        };
                        
                        return "{$leaderName} ({$leaderRole})";
                    })
                    ->badge()
                    ->color('primary'),
                
                TextEntry::make('email')
                    ->label('Email Address')
                    ->icon('heroicon-o-envelope')
                    ->placeholder('Not provided'),
                
                TextEntry::make('phone_number')
                    ->label('Phone Number')
                    ->icon('heroicon-o-phone')
                    ->placeholder('Not provided'),
                
                TextEntry::make('birthday')
                    ->label('Birthday')
                    ->date()
                    ->icon('heroicon-o-calendar')
                    ->placeholder('Not provided'),
                
                TextEntry::make('address')
                    ->label('Address')
                    ->icon('heroicon-o-map-pin')
                    ->placeholder('Not provided'),
                
                TextEntry::make('civilStatus.name')
                    ->label('Civil Status')
                    ->placeholder('Not specified'),
                
                TextEntry::make('sex.name')
                    ->label('Gender')
                    ->placeholder('Not specified'),
                
                TextEntry::make('created_at')
                    ->label('Member Since')
                    ->date()
                    ->color('gray'),
            ]);
    }
}
