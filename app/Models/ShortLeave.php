<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortLeave extends Model
{
    use HasFactory;

    public const CATEGORIES = ['short_leave', 'exemption'];

    public const EXEMPTION_TYPES = ['late_arrival', 'early_departure', 'missed_punch', 'official_duty', 'other'];

    protected $fillable = [
        'employee_id', 'category', 'exemption_type', 'date',
        'from_time', 'to_time', 'duration_minutes', 'reason',
        'status', 'approved_by', 'approved_at', 'approval_remarks', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function leaveApplication()
    {
        return $this->hasOne(LeaveApplication::class);
    }
}
