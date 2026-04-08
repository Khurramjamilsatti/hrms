<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id', 'employee_id', 'enrolled_by', 'enrolled_date',
        'status', 'completion_date', 'score', 'attendance_percentage',
        'feedback', 'rating', 'certificate_issued'
    ];

    protected $casts = [
        'enrolled_date' => 'date',
        'completion_date' => 'date',
        'score' => 'decimal:2',
        'certificate_issued' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(TrainingSession::class, 'session_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function enrolledBy()
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }

    public function certificate()
    {
        return $this->hasOne(TrainingCertificate::class, 'enrollment_id');
    }
}
