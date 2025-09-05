<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NetworkLeader extends Model
{
    protected $table = 'network_leaders';
    
    protected $fillable = [
        'member_id',
        'user_id',
    ];

    public function members(): MorphMany
    {
        return $this->morphMany(Member::class, 'leader');
    }
        public function attenders(): MorphMany
        {
            return $this->morphMany(Attender::class, 'leader');
        }
}
