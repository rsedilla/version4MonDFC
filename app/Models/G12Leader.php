<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class G12Leader extends Model
{
    protected $table = 'g12_leaders';

    public function members(): MorphMany
    {
        return $this->morphMany(Member::class, 'leader');
    }
        public function attenders(): MorphMany
        {
            return $this->morphMany(Attender::class, 'leader');
        }
}
