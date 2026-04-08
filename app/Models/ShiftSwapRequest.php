<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftSwapRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id', 'requester_assignment_id', 'swapper_id', 'swapper_assignment_id',
        'reason', 'status', 'approved_by', 'approved_at', 'rejection_reason'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(Employee::class, 'requester_id');
    }

    public function requesterAssignment()
    {
        return $this->belongsTo(ShiftAssignment::class, 'requester_assignment_id');
    }

    public function swapper()
    {
        return $this->belongsTo(Employee::class, 'swapper_id');
    }

    public function swapperAssignment()
    {
        return $this->belongsTo(ShiftAssignment::class, 'swapper_assignment_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
