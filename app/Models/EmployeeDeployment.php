<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'deployment_number',
        'deployment_type',
        'project_name',
        'client_name',
        'location',
        'country',
        'city',
        'start_date',
        'end_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'purpose',
        'role',
        'reporting_manager',
        'accommodation_type',
        'accommodation_details',
        'transport_details',
        'allowance_amount',
        'travel_ticket_status',
        'visa_status',
        'insurance_status',
        'departure_from_long_leave',
        'long_leave_start_date',
        'long_leave_end_date',
        'approved_by',
        'approved_at',
        'created_by',
        'notes',
        'extension_count',
        'current_extension_end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'allowance_amount' => 'decimal:2',
        'departure_from_long_leave' => 'boolean',
        'long_leave_start_date' => 'date',
        'long_leave_end_date' => 'date',
        'approved_at' => 'datetime',
        'extension_count' => 'integer',
        'current_extension_end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function extensions()
    {
        return $this->hasMany(DeploymentExtension::class, 'deployment_id');
    }
}
