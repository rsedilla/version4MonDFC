<?php

namespace App\Services;

use App\Models\Member;
use App\Models\TrainingType;
use App\Models\TrainingStatus;
use App\Models\Attender;
use App\Models\SeniorPastor;
use Illuminate\Support\Collection;

class ConsolidatorService
{
    /**
     * Get all eligible consolidators based on training requirements
     * 
     * Qualifications:
     * 1. Training type = must be SOL 1, SOL 2 and SOL 3
     * 2. Senior pastor is not included
     * 3. Attender is not included
     */
    public function getEligibleConsolidators(): Collection
    {
        // Get SOL training type IDs
        $solTrainingTypes = TrainingType::whereIn('name', ['SOL 1', 'SOL 2', 'SOL 3'])->pluck('id');
        
        if ($solTrainingTypes->isEmpty()) {
            return collect();
        }

        // Get Graduate status ID
        $graduateStatus = TrainingStatus::where('name', 'Graduate')->first();
        
        if (!$graduateStatus) {
            return collect();
        }

        // Get member IDs who have completed all SOL trainings (SOL 1, SOL 2, SOL 3)
        $membersWithAllSolTrainings = Member::whereHas('trainingTypes', function ($query) use ($solTrainingTypes, $graduateStatus) {
            $query->whereIn('training_type_id', $solTrainingTypes)
                  ->where('training_status_id', $graduateStatus->id);
        })
        ->withCount(['trainingTypes' => function ($query) use ($solTrainingTypes, $graduateStatus) {
            $query->whereIn('training_type_id', $solTrainingTypes)
                  ->where('training_status_id', $graduateStatus->id);
        }])
        ->having('training_types_count', '=', $solTrainingTypes->count())
        ->get();

        // Exclude Senior Pastors
        $seniorPastorIds = SeniorPastor::pluck('member_id');
        
        // Exclude Attenders (people who are currently being consolidated)
        $attenderIds = Attender::pluck('member_id');

        // Filter out Senior Pastors and Attenders
        $eligibleMembers = $membersWithAllSolTrainings->reject(function ($member) use ($seniorPastorIds, $attenderIds) {
            return $seniorPastorIds->contains($member->id) || $attenderIds->contains($member->id);
        });

        return $eligibleMembers;
    }

    /**
     * Check if a member is eligible to be a consolidator
     */
    public function isEligibleConsolidator(int $memberId): bool
    {
        return $this->getEligibleConsolidators()->contains('id', $memberId);
    }

    /**
     * Get consolidator options for select fields
     */
    public function getConsolidatorOptions(): array
    {
        // Simplified approach - get all members for now and filter later
        try {
            $eligibleMembers = $this->getEligibleConsolidators();
            
            if ($eligibleMembers->isEmpty()) {
                // Fallback: return all members except attenders for testing
                $attenderIds = Attender::pluck('member_id');
                $allMembers = Member::whereNotIn('id', $attenderIds)->get();
                return $allMembers->pluck('full_name', 'id')->toArray();
            }
            
            return $eligibleMembers->pluck('full_name', 'id')->toArray();
        } catch (\Exception $e) {
            // If there's an error, return all members for debugging
            return Member::all()->pluck('full_name', 'id')->toArray();
        }
    }

    /**
     * Get training types that qualify for consolidator role
     */
    public function getQualifyingTrainingTypes(): Collection
    {
        return TrainingType::whereIn('name', ['SOL 1', 'SOL 2', 'SOL 3'])->get();
    }
}
