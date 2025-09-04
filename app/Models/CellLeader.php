<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CellLeader extends Model
{
    protected $table = 'cell_leaders';

    public function members(): MorphMany
    {
        return $this->morphMany(Member::class, 'leader');
    }
}
