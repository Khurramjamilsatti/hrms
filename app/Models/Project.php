<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'client_id', 'manager_id',
        'start_date', 'end_date', 'budget', 'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Employee::class, 'client_id');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }
}
