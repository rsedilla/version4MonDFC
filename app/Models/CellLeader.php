<?php

namespace App\Models;

use App\Traits\HasCellGroups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CellLeader extends Model
{
    use HasCellGroups;

    protected $table = 'cell_leaders';
    
    protected $fillable = [
        'member_id',
        'user_id',
        // Add other fields as needed
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
