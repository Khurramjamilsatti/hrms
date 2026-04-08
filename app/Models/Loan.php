<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'loan_number',
        'loan_type',
        'amount',
        'interest_rate',
        'installments',
        'installment_amount',
        'start_date',
        'end_date',
        'purpose',
        'status',
        'approved_by',
        'approved_at',
        'disbursed_date',
        'disbursed_by',
        'total_paid',
        'balance_amount',
        'payment_method',
        'guarantor_name',
        'guarantor_employee_id',
        'guarantor_contact',
        'remarks',
        'rejection_reason',
        'closed_date',
        'closed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'installments' => 'integer',
        'installment_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'disbursed_date' => 'date',
        'total_paid' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'closed_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function disburser()
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function guarantorEmployee()
    {
        return $this->belongsTo(Employee::class, 'guarantor_employee_id');
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }
}
