<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'advance_request_id', 'payroll_id', 'deduction_date',
        'installment_number', 'deduction_amount', 'balance_after_deduction',
        'status', 'remarks'
    ];

    protected $casts = [
        'deduction_date' => 'date',
        'deduction_amount' => 'decimal:2',
        'balance_after_deduction' => 'decimal:2',
        'installment_number' => 'integer',
    ];

    public function advanceRequest()
    {
        return $this->belongsTo(AdvanceRequest::class, 'advance_request_id');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }
}
