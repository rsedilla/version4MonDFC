<?php

namespace App\Models;

use App\Traits\HasCellGroups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CellLeader extends Model
{
    use HasCellGroups;

    protected $table = 'cell_leaders';

    public function members(): MorphMany
    {
        return $this->morphMany(Member::class, 'leader');
    }

    public function attenders(): MorphMany
    {
        return $this->morphMany(Attender::class, 'leader');
    }
}
