<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'employee_id', 'status', 'is_organizer', 'response_note', 'responded_at'
    ];

    protected $casts = [
        'is_organizer' => 'boolean',
        'responded_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(CalendarEvent::class, 'event_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
