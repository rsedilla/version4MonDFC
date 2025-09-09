<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergingLeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'leadership_area',
        'notes',
        'identified_date',
        'is_active',
    ];

    protected $casts = [
        'identified_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
