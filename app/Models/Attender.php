<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attender extends Model
{
    protected $fillable = [
        'member_id',
        'leader_id',
        'leader_type',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function leader(): MorphTo
    {
        return $this->morphTo();
    }
}
