<?php

namespace App\Models;

use App\Models\Sex;
use App\Models\CivilStatus;
use App\Traits\ManagesLeaderTypeTransitions;
use App\Traits\HasDirectLeader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    use ManagesLeaderTypeTransitions, HasDirectLeader;
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'phone_number', 'birthday', 'address',
        'civil_status_id', 'sex_id', 'leader_id', 'leader_type', 'member_leader_type'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saved(function (Member $member) {
            // Handle both new records and updates where member_leader_type changed
            if ($member->wasRecentlyCreated || $member->wasChanged('member_leader_type')) {
                
                // Clean up old records if member_leader_type changed (only for updates, not new records)
                if (!$member->wasRecentlyCreated) {
                    static::cleanupOldLeaderRecords($member);
                }
                
                // Create new records based on member_leader_type
                static::createLeaderRecord($member);
            }
        });
    }

    /**
     * Clean up old leader records when leader_type changes
     */
    private static function cleanupOldLeaderRecords(Member $member): void
    {
        $originalType = $member->getOriginal('member_leader_type');
        
        // Only cleanup if there was an original leader type (not null for new members)
        if ($originalType) {
            $member->cleanupLeaderTypeRecord($originalType);
        }
    }

    /**
     * Create leader record based on member's member_leader_type
     */
    private static function createLeaderRecord(Member $member): void
    {
        $member->createLeaderTypeRecord($member->member_leader_type);
    }

    public function leader(): MorphTo
    {
        return $this->morphTo();
    }

    public function sex()
    {
        return $this->belongsTo(Sex::class);
    }

    public function civilStatus()
    {
        return $this->belongsTo(CivilStatus::class);
    }

    // Accessor for full name
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return implode(' ', $parts);
    }

    /**
     * Get the training types with status for this member.
     */
    public function trainingTypes(): BelongsToMany
    {
        return $this->belongsToMany(TrainingType::class, 'member_training_type')
                    ->withPivot('training_status_id')
                    ->withTimestamps();
    }
}
