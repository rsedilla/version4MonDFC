<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CgAttendanceRecord extends Model
{
    protected $table = 'cg_attendance_records';

    protected $fillable = [
        'cell_group_id',
        'attendee_id',
        'attendee_type',
        'year',
        'month',
        'week_number',
        'present',
    ];

    // Relationships
    public function cellGroup()
    {
        return $this->belongsTo(CellGroup::class, 'cell_group_id');
    }

    public function attendee()
    {
        return $this->morphTo();
    }
}
