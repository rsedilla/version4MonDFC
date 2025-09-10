<?php

namespace App\Filament\Resources\CellGroups\Pages;

use App\Filament\Resources\CellGroups\CellGroupResource;
use App\Services\LeaderSearchService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class EditCellGroup extends EditRecord
{
    protected static string $resource = CellGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Mutate form data before filling the form (for editing)
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        try {
            Log::info('CELL_GROUP_EDIT: Loading data for edit form:', [
                'cell_group_id' => $this->record->id,
                'has_info' => $this->record->info ? 'YES' : 'NO'
            ]);

            // Load the related CellGroupInfo data if it exists
            if ($this->record->info) {
                $data['info'] = [
                    'day' => $this->record->info->day,
                    'time' => $this->record->info->time?->format('H:i'),
                    'location' => $this->record->info->location,
                    'cell_group_idnum' => $this->record->info->cell_group_idnum,
                ];

                Log::info('CELL_GROUP_EDIT: Loaded info data:', $data['info']);
            } else {
                Log::warning('CELL_GROUP_EDIT: No info record found for cell group:', ['id' => $this->record->id]);
                $data['info'] = [
                    'day' => null,
                    'time' => null,
                    'location' => null,
                    'cell_group_idnum' => null,
                ];
            }

            // Set up leader_info field for the search component
            if ($this->record->leader_id && $this->record->leader_type) {
                $modelShortName = class_basename($this->record->leader_type);
                $compositeKey = "{$modelShortName}:{$this->record->leader_id}";
                $data['leader_info'] = $compositeKey;

                Log::info('CELL_GROUP_EDIT: Set leader_info:', [
                    'leader_id' => $this->record->leader_id,
                    'leader_type' => $this->record->leader_type,
                    'composite_key' => $compositeKey
                ]);
            }

            Log::info('CELL_GROUP_EDIT: Final form data prepared:', [
                'has_info' => isset($data['info']),
                'has_leader_info' => isset($data['leader_info']),
                'info_keys' => isset($data['info']) ? array_keys($data['info']) : []
            ]);

        } catch (\Exception $e) {
            Log::error('CELL_GROUP_EDIT: Error loading form data:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $data;
    }

    /**
     * Mutate form data before saving the record
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        try {
            Log::info('CELL_GROUP_EDIT: Form data before save:', [
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
                        Log::info('CELL_GROUP_EDIT: Successfully parsed leader_info:', $parsed);
                    } else {
                        Log::warning('CELL_GROUP_EDIT: Failed to parse leader_info:', ['leader_info' => $data['leader_info']]);
                    }
                }
            }

            // Validate that leader information is provided
            if (empty($data['leader_id']) || empty($data['leader_type'])) {
                Log::warning('CELL_GROUP_EDIT: Leader validation failed', [
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

            Log::info('CELL_GROUP_EDIT: Final data before save:', [
                'cell_group_data' => Arr::except($data, ['info']),
                'info_data' => $data['info'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('CELL_GROUP_EDIT: Error in mutateFormDataBeforeSave:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::make()
                ->title('Error Updating Cell Group')
                ->body('Please check all required fields and try again.')
                ->danger()
                ->send();
                
            $this->halt();
        }

        return $data;
    }

    /**
     * Handle record updating and related models
     */
    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            // Extract info data before updating the main record
            $infoData = $data['info'] ?? [];
            unset($data['info']);
            
            Log::info('CELL_GROUP_EDIT: Updating CellGroup with data:', [
                'id' => $record->id,
                'data' => $data
            ]);
            
            // Update the main CellGroup record
            $record->update($data);
            
            Log::info('CELL_GROUP_EDIT: CellGroup updated successfully');
            
            // Update or create the related CellGroupInfo record
            if (!empty($infoData)) {
                if ($record->info) {
                    // Update existing info record
                    $record->info->update($infoData);
                    Log::info('CELL_GROUP_EDIT: CellGroupInfo updated:', ['id' => $record->info->id]);
                } else {
                    // Create new info record if it doesn't exist
                    $infoData['cell_group_id'] = $record->id;
                    $infoRecord = $record->info()->create($infoData);
                    Log::info('CELL_GROUP_EDIT: New CellGroupInfo created:', ['id' => $infoRecord->id]);
                }
                
                Notification::make()
                    ->title('Cell Group Updated Successfully!')
                    ->body("Group: {$record->name}")
                    ->success()
                    ->send();
            } else {
                Log::warning('CELL_GROUP_EDIT: No info data to save');
                
                Notification::make()
                    ->title('Cell Group Updated')
                    ->body("Group: {$record->name} (meeting info may be incomplete)")
                    ->warning()
                    ->send();
            }
            
            return $record;
            
        } catch (\Exception $e) {
            Log::error('CELL_GROUP_EDIT: Error in handleRecordUpdate:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::make()
                ->title('Error Updating Cell Group')
                ->body('Database error: ' . $e->getMessage())
                ->danger()
                ->send();
                
            throw $e;
        }
    }
}
