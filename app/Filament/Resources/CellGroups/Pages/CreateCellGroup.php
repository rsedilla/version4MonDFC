<?php

namespace App\Filament\Resources\CellGroups\Pages;

use App\Filament\Resources\CellGroups\CellGroupResource;
use App\Services\CellGroupIdService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCellGroup extends CreateRecord
{
    protected static string $resource = CellGroupResource::class;
    
    /**
     * Mutate form data before creating the record
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            // Check if monthly limit is reached
            if (CellGroupIdService::isMonthlyLimitReached()) {
                Notification::make()
                    ->title('Monthly Limit Reached')
                    ->body('Cannot create more cell groups this month. Maximum of 300 per month.')
                    ->danger()
                    ->send();
                    
                $this->halt();
            }
            
            // Auto-generate cell_group_idnum for the info relationship
            if (!isset($data['info']['cell_group_idnum']) || empty($data['info']['cell_group_idnum'])) {
                $data['info']['cell_group_idnum'] = CellGroupIdService::generateCellGroupIdNum();
            }
            
            // Show success notification with the generated ID
            Notification::make()
                ->title('Cell Group ID Generated')
                ->body("Generated ID: {$data['info']['cell_group_idnum']}")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Generating Cell Group ID')
                ->body($e->getMessage())
                ->danger()
                ->send();
                
            $this->halt();
        }
        
        return $data;
    }
    
    /**
     * Get the available slots information for display
     */
    public function getAvailableSlots(): int
    {
        return CellGroupIdService::getAvailableSlots();
    }
}
