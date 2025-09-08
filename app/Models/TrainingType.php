<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingType extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the members with their training status.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_training_type')
                    ->withPivot('training_status_id')
                    ->withTimestamps();
    }
}
