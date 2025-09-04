<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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

    public function type()
    {
        return $this->belongsTo(CellGroupType::class, 'cell_group_type_id');
    }
}
