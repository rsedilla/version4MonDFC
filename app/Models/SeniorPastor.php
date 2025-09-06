<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SeniorPastor extends Model
{
    protected $table = 'senior_pastors';
    
    protected $fillable = [
        'member_id',
        'user_id',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function members(): MorphMany
    {
        return $this->morphMany(Member::class, 'leader');
    }

    public function attenders(): MorphMany
    {
        return $this->morphMany(Attender::class, 'leader');
    }
}
