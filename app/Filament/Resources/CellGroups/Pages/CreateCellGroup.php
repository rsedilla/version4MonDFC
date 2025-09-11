<?php

namespace App\Filament\Resources\CellGroups\Pages;

use App\Filament\Resources\CellGroups\CellGroupResource;
use App\Models\CellGroup;
use App\Models\CellGroupInfo;
use App\Services\CellGroupIdService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;

class CreateCellGroup extends CreateRecord
{
    protected static string $resource = CellGroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Parse leader information from DirectLeaderActionHelper format
        if (isset($data['leader_info'])) {
            if (str_contains($data['leader_info'], ':')) {
                [$leaderType, $leaderId] = explode(':', $data['leader_info']);
                $data['leader_id'] = (int) $leaderId;
                $data['leader_type'] = $leaderType;
            }
            unset($data['leader_info']);
        }

        // Generate cell group ID number if service exists
        if (class_exists(CellGroupIdService::class)) {
            try {
                $data['cell_group_idnum'] = CellGroupIdService::generateCellGroupIdNum();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Monthly Limit Reached')
                    ->body('Cannot create more than 300 cell groups this month.')
                    ->danger()
                    ->send();
                
                $this->halt();
            }
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): CellGroup
    {
        // Extract nested data
        $infoData = Arr::pull($data, 'info', []);
        $attendeeIds = Arr::pull($data, 'attendees', []);
        
        // Create the CellGroup record
        $cellGroup = CellGroup::create($data);
        
        // Create the CellGroupInfo record if info data exists
        if (!empty($infoData)) {
            $infoData['cell_group_id'] = $cellGroup->id;
            if (isset($cellGroup->cell_group_idnum)) {
                $infoData['cell_group_idnum'] = $cellGroup->cell_group_idnum;
            }
            CellGroupInfo::create($infoData);
        }
        
        // Attach attendees if any were selected
        if (!empty($attendeeIds)) {
            $cellGroup->attendees()->sync($attendeeIds);
        }
        
        return $cellGroup;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cell Group Created Successfully!';
    }
}
