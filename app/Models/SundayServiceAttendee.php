<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SundayServiceAttendee extends Model
{
    protected $table = 'sunday_service_attendees';

    protected $fillable = [
        'service_id',
        'attendee_id',
        'attendee_type',
        'service_date',
        'present',
    ];

    // Relationships
    public function attendee()
    {
        return $this->morphTo();
    }
}
