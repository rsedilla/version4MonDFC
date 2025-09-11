<?php

namespace App\Filament\Resources\CellGroups\Pages;

use App\Filament\Resources\CellGroups\CellGroupResource;
use App\Models\CellGroup;
use App\Models\CellGroupInfo;
use App\Services\CellGroupIdService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert stored leader data to form format
        if (isset($data['leader_type']) && isset($data['leader_id'])) {
            $data['leader_info'] = $data['leader_type'] . ':' . $data['leader_id'];
        }

        // Load attendees (members assigned to this cell group)
        if ($this->record) {
            $attendeeIds = $this->record->attendees()->pluck('members.id')->toArray();
            $data['attendees'] = $attendeeIds;
        }

        // Load meeting info from CellGroupInfo
        if ($this->record && $this->record->info) {
            $data['info'] = [
                'day' => $this->record->info->day,
                'time' => $this->record->info->time,
                'location' => $this->record->info->location,
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Parse leader information from form
        if (isset($data['leader_info'])) {
            if (str_contains($data['leader_info'], ':')) {
                [$leaderType, $leaderId] = explode(':', $data['leader_info']);
                $data['leader_id'] = (int) $leaderId;
                $data['leader_type'] = $leaderType;
            }
            unset($data['leader_info']);
        }

        return $data;
    }

    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Extract nested data
        $infoData = Arr::pull($data, 'info', []);
        $attendeeIds = Arr::pull($data, 'attendees', []);
        
        // Update the main CellGroup record
        $record->update($data);
        
        // Update or create CellGroupInfo
        if (!empty($infoData)) {
            if ($record->info) {
                $record->info->update($infoData);
            } else {
                $infoData['cell_group_id'] = $record->id;
                if (isset($record->cell_group_idnum)) {
                    $infoData['cell_group_idnum'] = $record->cell_group_idnum;
                }
                CellGroupInfo::create($infoData);
            }
        }
        
        // Update attendees (sync will add/remove as needed)
        if (is_array($attendeeIds)) {
            $record->attendees()->sync($attendeeIds);
        }
        
        return $record;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Cell Group Updated Successfully!';
    }
}
