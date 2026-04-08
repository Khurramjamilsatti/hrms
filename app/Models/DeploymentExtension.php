<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'deployment_id',
        'extension_number',
        'previous_end_date',
        'new_end_date',
        'reason',
        'approved_by',
        'approved_at',
        'requested_by',
        'status',
    ];

    protected $casts = [
        'previous_end_date' => 'date',
        'new_end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function deployment()
    {
        return $this->belongsTo(EmployeeDeployment::class, 'deployment_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
