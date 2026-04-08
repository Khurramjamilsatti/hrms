<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboarding extends Model
{
    use HasFactory;

    protected $table = 'employee_onboarding';
    protected $fillable = [
        'employee_id',
        'template_id',
        'start_date',
        'expected_completion_date',
        'actual_completion_date',
        'buddy_id',
        'status',
        'completion_percentage',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'completion_percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function template()
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }

    public function buddy()
    {
        return $this->belongsTo(Employee::class, 'buddy_id');
    }

    public function tasks()
    {
        return $this->hasMany(EmployeeOnboardingTask::class, 'onboarding_id');
    }
}
