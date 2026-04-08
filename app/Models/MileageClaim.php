<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MileageClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'expense_claim_id', 'claim_number', 'travel_date',
        'from_location', 'to_location', 'purpose', 'distance_km',
        'rate_per_km', 'total_amount', 'vehicle_type', 'vehicle_registration',
        'odometer_start', 'odometer_end', 'status', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'travel_date' => 'date',
        'distance_km' => 'decimal:2',
        'rate_per_km' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'odometer_start' => 'integer',
        'odometer_end' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function expenseClaim()
    {
        return $this->belongsTo(ExpenseClaim::class, 'expense_claim_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
