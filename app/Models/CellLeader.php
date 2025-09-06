<?php

namespace App\Models;

use App\Traits\HasCellGroups;
use App\Traits\HasDirectLeader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CellLeader extends Model
{
    use HasCellGroups, HasDirectLeader;

    protected $table = 'cell_leaders';
    
    protected $fillable = [
        'member_id',
        'user_id',
        'leader_id',
        'leader_type',
        // Add other fields as needed
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
