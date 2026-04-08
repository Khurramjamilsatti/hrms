<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'travel_request_id', 'request_number', 'purpose',
        'amount', 'required_date', 'status', 'approved_by', 'approved_at',
        'payment_date', 'settlement_date', 'settled_amount',
        // New comprehensive fields
        'advance_type', 'project_code', 'cost_center', 'payment_method',
        'payment_reference', 'installments', 'installment_amount',
        'first_deduction_date', 'deducted_amount', 'balance_amount',
        'deduction_notes', 'rejection_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'settled_amount' => 'decimal:2',
        'required_date' => 'date',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
        'settlement_date' => 'date',
        'installment_amount' => 'decimal:2',
        'first_deduction_date' => 'date',
        'deducted_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'installments' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function deductions()
    {
        return $this->hasMany(AdvanceDeduction::class, 'advance_request_id');
    }
}
