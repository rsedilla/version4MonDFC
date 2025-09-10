<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CellGroup extends Model
{
    protected $fillable = [
        'name',
        'leader_id',
        'leader_type',
        'description',
        'cell_group_type_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
     * Alias for type() relationship for better readability in table columns
     */
    public function cellGroupType(): BelongsTo
    {
        return $this->type();
    }

    /**
     * Get the leader of this cell group (polymorphic relationship).
     */
    public function leader()
    {
        return $this->morphTo();
    }

    /**
     * Get the cell group information (meeting details).
     */
    public function info(): HasOne
    {
        return $this->hasOne(CellGroupInfo::class, 'cell_group_id');
    }
}
