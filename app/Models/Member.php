<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Member extends Model
{
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'phone_number', 'birthday', 'address',
        'civil_status_id', 'sex_id', 'leader_id', 'leader_type'
    ];

    public function leader(): MorphTo
    {
        return $this->morphTo();
    }
}
