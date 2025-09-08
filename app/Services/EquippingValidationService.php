<?php

namespace App\Services;

use App\Models\MemberTrainingType;
use Illuminate\Support\Collection;

class EquippingValidationService
{
    /**
     * Check if a member is already assigned to a specific training type
     */
    public function memberHasTrainingType(int $memberId, int $trainingTypeId, ?int $excludeId = null): bool
    {
        $query = MemberTrainingType::where('member_id', $memberId)
            ->where('training_type_id', $trainingTypeId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Get all members who are already assigned to a specific training type
     */
    public function getMembersWithTrainingType(int $trainingTypeId): Collection
    {
        return MemberTrainingType::where('training_type_id', $trainingTypeId)
            ->pluck('member_id');
    }
    
    /**
     * Get all training types a member is assigned to
     */
    public function getMemberTrainingTypes(int $memberId): Collection
    {
        return MemberTrainingType::where('member_id', $memberId)
            ->pluck('training_type_id');
    }
    
    /**
     * Check if the combination is unique (for form validation)
     */
    public function isUniqueCombination(int $memberId, int $trainingTypeId, ?int $excludeId = null): bool
    {
        return !$this->memberHasTrainingType($memberId, $trainingTypeId, $excludeId);
    }
}
