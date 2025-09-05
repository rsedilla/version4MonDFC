<?php

namespace App\Models;

use App\Models\Sex;
use App\Models\CivilStatus;
use App\Traits\ManagesLeaderTypeTransitions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    use ManagesLeaderTypeTransitions;
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'phone_number', 'birthday', 'address',
        'civil_status_id', 'sex_id', 'leader_id', 'leader_type'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saved(function (Member $member) {
            // Handle both new records and updates where leader_type changed
            if ($member->wasRecentlyCreated || $member->wasChanged('leader_type')) {
                
                // Clean up old records if leader_type changed (only for updates, not new records)
                if (!$member->wasRecentlyCreated) {
                    static::cleanupOldLeaderRecords($member);
                }
                
                // Create new records based on leader_type
                static::createLeaderRecord($member);
            }
        });
    }

    /**
     * Clean up old leader records when leader_type changes
     */
    private static function cleanupOldLeaderRecords(Member $member): void
    {
        $originalType = $member->getOriginal('leader_type');
        $member->cleanupLeaderTypeRecord($originalType);
    }

    /**
     * Create leader record based on member's leader_type
     */
    private static function createLeaderRecord(Member $member): void
    {
        $member->createLeaderTypeRecord($member->leader_type);
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
}
