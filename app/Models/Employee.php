<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_code',
        'department_id',
        'designation_id',
        'manager_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'emergency_contact',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'national_id',
        'joining_date',
        'leaving_date',
        'employment_type',
        'employment_status',
        'profile_picture',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joining_date' => 'date',
            'leaving_date' => 'date',
        ];
    }

    protected $appends = ['full_name'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function cvs()
    {
        return $this->hasMany(EmployeeCv::class);
    }

    public function currentCv()
    {
        return $this->hasOne(EmployeeCv::class)->where('is_current', true);
    }

    public function deployments()
    {
        return $this->hasMany(EmployeeDeployment::class);
    }

    public function activeDeployment()
    {
        return $this->hasOne(EmployeeDeployment::class)->where('status', 'active');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getCurrentSalaryAttribute()
    {
        return $this->salaries()
            ->where('effective_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', now());
            })
            ->first();
    }

    public function getCurrentContractAttribute()
    {
        return $this->contracts()
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->where('status', 'active')
            ->first();
    }
}
