<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboardingTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'onboarding_id', 'template_task_id', 'title', 'description',
        'due_date', 'completed_date', 'completed_by', 'status', 'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_date' => 'date',
    ];

    public function onboarding()
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'onboarding_id');
    }

    public function templateTask()
    {
        return $this->belongsTo(OnboardingTemplateTask::class, 'template_task_id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
