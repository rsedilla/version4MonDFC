<?php

namespace App\Filament\Resources\CellGroups\Pages;

use App\Filament\Resources\CellGroups\CellGroupResource;
use App\Services\CellGroupIdService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CreateCellGroup extends CreateRecord
{
    protected static string $resource = CellGroupResource::class;
    
    /**
     * Mutate form data before creating the record
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            // Validate that leader information is provided (new button-based system)
            if (empty($data['leader_id']) || empty($data['leader_type'])) {
                Notification::make()
                    ->title('Leader Selection Required')
                    ->body('Please select both leader type and leader from the dropdowns.')
                    ->warning()
                    ->send();
                    
                $this->halt();
            }
            
            // Validate that the selected leader exists in the specified table
            $leaderModel = $data['leader_type'];
            if (!class_exists($leaderModel)) {
                Notification::make()
                    ->title('Invalid Leader Type')
                    ->body('The selected leader type is not valid.')
                    ->danger()
                    ->send();
                    
                $this->halt();
            }
            
            $leader = $leaderModel::find($data['leader_id']);
            if (!$leader) {
                Notification::make()
                    ->title('Leader Not Found')
                    ->body('The selected leader could not be found. Please select a different leader.')
                    ->warning()
                    ->send();
                    
                $this->halt();
            }
            
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
                ->title('Cell Group Created Successfully!')
                ->body("Group: {$data['name']} | ID: {$data['info']['cell_group_idnum']}")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Creating Cell Group')
                ->body('Please check all required fields and try again. Contact support if the problem persists.')
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
