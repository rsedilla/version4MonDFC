<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingStatus extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get all member training assignments with this status.
     */
    public function memberTrainingTypes(): HasMany
    {
        return $this->hasMany(MemberTrainingType::class);
    }
}
