<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'employee_id', 'remind_before_minutes', 'is_sent', 'sent_at'
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
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
