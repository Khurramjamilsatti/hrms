<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'document_path',
        'status',
        'approval_level',
        'first_approved_by',
        'first_approval_remarks',
        'first_approved_at',
        'final_approved_by',
        'final_approval_remarks',
        'final_approved_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'decimal:2',
            'first_approved_at' => 'datetime',
            'final_approved_at' => 'datetime',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function firstApprover()
    {
        return $this->belongsTo(User::class, 'first_approved_by');
    }

    public function finalApprover()
    {
        return $this->belongsTo(User::class, 'final_approved_by');
    }

    // Legacy relationship for backwards compatibility
    public function approver()
    {
        return $this->belongsTo(User::class, 'final_approved_by');
    }
}
