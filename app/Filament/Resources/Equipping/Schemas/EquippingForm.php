<?php

namespace App\Filament\Resources\Equipping\Schemas;

use App\Models\Member;
use App\Models\TrainingType;
use App\Models\TrainingStatus;
use App\Services\EquippingValidationService;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Livewire\Component as LivewireComponent;

class EquippingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->label('Member')
                    ->options(Member::all()->pluck('full_name', 'id'))
                    ->searchable()
                    ->required()
                    ->helperText('Select the member to assign to training')
                    ->rules([
                        function ($get, $livewire) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $livewire) {
                                $trainingTypeId = $get('training_type_id');
                                $recordId = $livewire->record?->id ?? null; // For edit mode
                                
                                if ($value && $trainingTypeId) {
                                    $validationService = app(EquippingValidationService::class);
                                    
                                    if (!$validationService->isUniqueCombination(
                                        (int) $value, 
                                        (int) $trainingTypeId, 
                                        $recordId
                                    )) {
                                        $fail('This member is already assigned to this training type.');
                                    }
                                }
                            };
                        },
                    ]),
                
                Select::make('training_type_id')
                    ->label('Training Type')
                    ->options(TrainingType::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->helperText('Choose the training program')
                    ->rules([
                        function ($get, $livewire) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $livewire) {
                                $memberId = $get('member_id');
                                $recordId = $livewire->record?->id ?? null; // For edit mode
                                
                                if ($value && $memberId) {
                                    $validationService = app(EquippingValidationService::class);
                                    
                                    if (!$validationService->isUniqueCombination(
                                        (int) $memberId, 
                                        (int) $value, 
                                        $recordId
                                    )) {
                                        $fail('This member is already assigned to this training type.');
                                    }
                                }
                            };
                        },
                    ]),
                
                Select::make('training_status_id')
                    ->label('Training Status')
                    ->options(TrainingStatus::all()->pluck('name', 'id'))
                    ->default(2) // Default to 'Not Enrolled'
                    ->required()
                    ->helperText('Set the member\'s current status for this training'),
            ]);
    }
}
