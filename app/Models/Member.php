<?php

namespace App\Models;

use App\Models\Sex;
use App\Models\CivilStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function sex()
    {
        return $this->belongsTo(Sex::class);
    }

    public function civilStatus()
    {
        return $this->belongsTo(CivilStatus::class);
    }

    // Accessor for full name
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return implode(' ', $parts);
    }
}
