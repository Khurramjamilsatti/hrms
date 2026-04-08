<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingTemplateTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'title',
        'description',
        'day_number',
        'task_type',
        'assigned_to_role',
        'is_mandatory',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'day_number' => 'integer',
        'assigned_to_role' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }

    public function employeeTasks()
    {
        return $this->hasMany(EmployeeOnboardingTask::class, 'template_task_id');
    }
}
