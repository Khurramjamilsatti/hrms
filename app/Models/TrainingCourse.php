<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'department_id', 'description', 'type', 'duration_hours', 'instructor_id',
        'external_provider', 'cost', 'delivery_mode', 'max_participants', 'is_active'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Employee::class, 'instructor_id');
    }

    public function sessions()
    {
        return $this->hasMany(TrainingSession::class, 'course_id');
    }
}
