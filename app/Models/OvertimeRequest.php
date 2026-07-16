<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'hours',
        'reason',
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
            'date' => 'date',
            'hours' => 'decimal:2',
            'first_approved_at' => 'datetime',
            'final_approved_at' => 'datetime',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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
