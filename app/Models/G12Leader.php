<?php

namespace App\Models;

use App\Traits\HasCellGroups;
use App\Traits\HasDirectLeader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class G12Leader extends Model
{
    use HasCellGroups, HasDirectLeader;

    protected $table = 'g12_leaders';
    
    protected $fillable = [
        'member_id',
        'leader_id',
        'leader_type',
    ];

    public function member(): BelongsTo
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
