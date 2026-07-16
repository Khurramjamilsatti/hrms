<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'total_earnings',
        'total_deductions',
        'absent_deduction',
        'unpaid_leave_deduction',
        'overtime_amount',
        'bonus_amount',
        'sunday_allowance',
        'holiday_allowance',
        'net_salary',
        'working_days',
        'present_days',
        'absent_days',
        'leave_days',
        'unpaid_leave_days',
        'half_days',
        'overtime_hours',
        'timesheet_hours',
        'status',
        'payment_date',
        'processed_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'total_earnings' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'absent_deduction' => 'decimal:2',
            'unpaid_leave_deduction' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'bonus_amount' => 'decimal:2',
            'sunday_allowance' => 'decimal:2',
            'holiday_allowance' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'unpaid_leave_days' => 'decimal:2',
            'half_days' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'timesheet_hours' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
