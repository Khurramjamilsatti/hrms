<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'project_id', 'task_id', 'date',
        'start_time', 'end_time', 'hours_worked', 'description',
        'billable', 'status', 'approved_by', 'approved_at', 'rejection_reason'
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'billable' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'task_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getHoursDecimalAttribute()
    {
        return round($this->hours_worked / 60, 2);
    }
}
