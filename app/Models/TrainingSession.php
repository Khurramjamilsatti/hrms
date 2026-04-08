<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'session_name', 'start_date', 'end_date',
        'start_time', 'end_time', 'location', 'meeting_link',
        'available_seats', 'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }

    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class, 'session_id');
    }
}
