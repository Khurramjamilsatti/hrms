<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'working_hours',
        'overtime_hours',
        'status',
        'remarks',
        'approved_by',
        'is_weekend',
        'is_sunday',
        'is_holiday',
        'day_type',
        'sunday_allowance',
        'holiday_allowance',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'working_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'sunday_allowance' => 'decimal:2',
            'holiday_allowance' => 'decimal:2',
            'is_weekend' => 'boolean',
            'is_sunday' => 'boolean',
            'is_holiday' => 'boolean',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
