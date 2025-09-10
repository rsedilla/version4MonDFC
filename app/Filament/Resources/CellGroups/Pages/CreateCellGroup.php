<?php

namespace App\Filament\Resources\CellGroups\Pages;

use App\Filament\Resources\CellGroups\CellGroupResource;
use App\Services\CellGroupIdService;
use App\Services\LeaderSearchService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
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
            // Debug: Log what data we're receiving
            Log::info('CELL_GROUP_CREATION: Form Data Received:', [
                'leader_id' => $data['leader_id'] ?? 'MISSING',
                'leader_type' => $data['leader_type'] ?? 'MISSING',
                'leader_info' => $data['leader_info'] ?? 'MISSING',
                'info_data' => $data['info'] ?? 'MISSING',
                'all_keys' => array_keys($data)
            ]);
            
            // Parse leader_info if leader_id/leader_type are missing
            if (empty($data['leader_id']) || empty($data['leader_type'])) {
                if (!empty($data['leader_info'])) {
                    $leaderSearchService = app(LeaderSearchService::class);
                    $parsed = $leaderSearchService->parseCompositeKey($data['leader_info']);
                    
                    if ($parsed) {
                        $data['leader_id'] = $parsed['leader_id'];
                        $data['leader_type'] = $parsed['leader_type'];
                        Log::info('CELL_GROUP_CREATION: Successfully parsed leader_info:', $parsed);
                    } else {
                        Log::warning('CELL_GROUP_CREATION: Failed to parse leader_info:', ['leader_info' => $data['leader_info']]);
                    }
                }
            }
            
            // Validate that leader information is provided
            if (empty($data['leader_id']) || empty($data['leader_type'])) {
                Log::warning('CELL_GROUP_CREATION: Leader validation failed', [
                    'leader_id' => $data['leader_id'] ?? 'null',
                    'leader_type' => $data['leader_type'] ?? 'null',
                    'leader_info' => $data['leader_info'] ?? 'null'
                ]);
                
                Notification::make()
                    ->title('Leader Selection Required')
                    ->body('Please search and select a leader. Start typing a name to see available leaders.')
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
            
            Log::info('CELL_GROUP_CREATION: Final data before creation:', [
                'cell_group_data' => Arr::except($data, ['info']),
                'info_data' => $data['info'] ?? null
            ]);
                
        } catch (\Exception $e) {
            Log::error('CELL_GROUP_CREATION: Error in mutateFormDataBeforeCreate:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
     * Handle record creation and related models
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            // Extract info data before creating the main record
            $infoData = $data['info'] ?? [];
            unset($data['info']);
            
            Log::info('CELL_GROUP_CREATION: Creating CellGroup with data:', $data);
            
            // Create the main CellGroup record
            $record = static::getModel()::create($data);
            
            Log::info('CELL_GROUP_CREATION: CellGroup created with ID:', ['id' => $record->id]);
            
            // Create the related CellGroupInfo record if info data exists
            if (!empty($infoData)) {
                $infoData['cell_group_id'] = $record->id;
                
                Log::info('CELL_GROUP_CREATION: Creating CellGroupInfo with data:', $infoData);
                
                $infoRecord = $record->info()->create($infoData);
                
                Log::info('CELL_GROUP_CREATION: CellGroupInfo created with ID:', ['id' => $infoRecord->id]);
                
                // Show success notification with the generated ID
                Notification::make()
                    ->title('Cell Group Created Successfully!')
                    ->body("Group: {$record->name} | ID: {$infoData['cell_group_idnum']}")
                    ->success()
                    ->send();
            } else {
                Log::warning('CELL_GROUP_CREATION: No info data provided');
                
                Notification::make()
                    ->title('Cell Group Created')
                    ->body("Group: {$record->name} (without meeting info)")
                    ->warning()
                    ->send();
            }
            
            return $record;
            
        } catch (\Exception $e) {
            Log::error('CELL_GROUP_CREATION: Error in handleRecordCreation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::make()
                ->title('Error Creating Cell Group')
                ->body('Database error: ' . $e->getMessage())
                ->danger()
                ->send();
                
            throw $e;
        }
    }
    
    /**
     * Get the available slots information for display
     */
    public function getAvailableSlots(): int
    {
        return CellGroupIdService::getAvailableSlots();
    }
}
