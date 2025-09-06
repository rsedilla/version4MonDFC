<?php

namespace App\Models;

use App\Traits\HasDirectLeader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CellMember extends Model
{
    use HasDirectLeader;
    
    protected $fillable = [
        'member_id',
        'attender_id',
        'cell_group_id',
        'joined_date',
        'status',
        'leader_id',
        'leader_type',
    ];

    protected $casts = [
        'joined_date' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function cellGroup(): BelongsTo
    {
        return $this->belongsTo(CellGroup::class);
    }
}