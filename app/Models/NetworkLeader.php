<?php

namespace App\Models;

use App\Traits\HasDirectLeader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NetworkLeader extends Model
{
    use HasDirectLeader;
    
    protected $table = 'network_leaders';
    
    protected $fillable = [
        'member_id',
        'user_id',
        'leader_id',
        'leader_type',
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
