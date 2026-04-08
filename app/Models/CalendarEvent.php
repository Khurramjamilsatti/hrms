<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'event_type', 'start_datetime', 'end_datetime',
        'location', 'meeting_link', 'is_all_day', 'is_recurring',
        'recurrence_rule', 'created_by'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_all_day' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendees()
    {
        return $this->hasMany(EventAttendee::class, 'event_id');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'event_id');
    }
}
