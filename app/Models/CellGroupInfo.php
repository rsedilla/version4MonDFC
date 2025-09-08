<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CellGroupInfo extends Model
{
    protected $fillable = [
        'cell_group_id',
        'cell_group_idnum',
        'day',
        'time',
        'location',
    ];

    protected $casts = [
        'time' => 'datetime:H:i',
    ];

    /**
     * Get the cell group that owns this info.
     */
    public function cellGroup(): BelongsTo
    {
        return $this->belongsTo(CellGroup::class, 'cell_group_id');
    }
}
