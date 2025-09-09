<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consolidator extends Model
{
    protected $fillable = [
        'member_id',
    ];

    /**
     * Get the member that is a consolidator
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get all attenders assigned to this consolidator
     */
    public function attenders(): HasMany
    {
        return $this->hasMany(Attender::class);
    }

    /**
     * Get the full name of the consolidator
     */
    public function getFullNameAttribute(): string
    {
        return $this->member ? $this->member->full_name : '';
    }
}
