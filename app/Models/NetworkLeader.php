<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NetworkLeader extends Model
{
    protected $table = 'network_leaders';

    public function members(): MorphMany
    {
        return $this->morphMany(Member::class, 'leader');
    }
}
