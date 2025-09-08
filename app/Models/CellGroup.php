<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CellGroup extends Model
{
    protected $fillable = [
        'name',
        'leader_id',
        'leader_type',
        'description',
        'cell_group_type_id',
    ];

    // Example: fetch all attendees (members) of this cell group
    public function attendees(): MorphToMany
    {
        return $this->morphToMany(Member::class, 'attendee', 'cell_group_attendees');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CellGroupType::class, 'cell_group_type_id');
    }

    /**
     * Get the cell group information (meeting details).
     */
    public function info(): HasOne
    {
        return $this->hasOne(CellGroupInfo::class, 'cell_group_id');
    }
}
