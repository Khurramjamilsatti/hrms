<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'effective_from',
        'effective_to',
        'payment_mode',
        'bank_name',
        'account_number',
        'remarks',
    ];

    protected $appends = ['gross_salary', 'net_salary', 'total_earnings', 'total_deductions'];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function components()
    {
        return $this->hasMany(EmployeeSalaryComponent::class);
    }

    /**
     * Calculate total earnings (allowances)
     */
    protected function totalEarnings(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->components()
                    ->whereHas('salaryComponent', function ($query) {
                        $query->where('type', 'earning');
                    })
                    ->sum('amount');
            }
        );
    }

    /**
     * Calculate total deductions
     */
    protected function totalDeductions(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->components()
                    ->whereHas('salaryComponent', function ($query) {
                        $query->where('type', 'deduction');
                    })
                    ->sum('amount');
            }
        );
    }

    /**
     * Calculate gross salary (Basic + Earnings)
     */
    protected function grossSalary(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->basic_salary + $this->total_earnings;
            }
        );
    }

    /**
     * Calculate net salary (Gross - Deductions)
     */
    protected function netSalary(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->gross_salary - $this->total_deductions;
            }
        );
    }
}

