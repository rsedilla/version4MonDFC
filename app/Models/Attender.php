<?php

namespace App\Models;

use App\Traits\AttenderProgressCalculation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attender extends Model
{
    use AttenderProgressCalculation;
    protected $fillable = [
        'member_id',
        'consolidator_id',
        'leader_id',
        'leader_type',
        'suyln_lesson_1',
        'suyln_lesson_2',
        'suyln_lesson_3',
        'suyln_lesson_4',
        'suyln_lesson_5',
        'suyln_lesson_6',
        'suyln_lesson_7',
        'suyln_lesson_8',
        'suyln_lesson_9',
        'suyln_lesson_10',
        'sunday_service_1',
        'sunday_service_2',
        'sunday_service_3',
        'sunday_service_4',
        'cell_group_1',
        'cell_group_2',
        'cell_group_3',
        'cell_group_4',
    ];

    protected $casts = [
        'suyln_lesson_1' => 'date',
        'suyln_lesson_2' => 'date',
        'suyln_lesson_3' => 'date',
        'suyln_lesson_4' => 'date',
        'suyln_lesson_5' => 'date',
        'suyln_lesson_6' => 'date',
        'suyln_lesson_7' => 'date',
        'suyln_lesson_8' => 'date',
        'suyln_lesson_9' => 'date',
        'suyln_lesson_10' => 'date',
        'sunday_service_1' => 'date',
        'sunday_service_2' => 'date',
        'sunday_service_3' => 'date',
        'sunday_service_4' => 'date',
        'cell_group_1' => 'date',
        'cell_group_2' => 'date',
        'cell_group_3' => 'date',
        'cell_group_4' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function consolidator()
    {
        return $this->belongsTo(Member::class, 'consolidator_id');
    }

    public function getMemberNameAttribute()
    {
        return $this->member ? ($this->member->first_name . ' ' . $this->member->last_name) : 'No member assigned';
    }

    public function getConsolidatorNameAttribute()
    {
        return $this->consolidator ? $this->consolidator->full_name : 'No consolidator assigned';
    }

    public function leader(): MorphTo
    {
        return $this->morphTo();
    }
}
