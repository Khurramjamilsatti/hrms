<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCv extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'version',
        'is_current',
        'uploaded_by',
        'uploaded_at',
        'summary',
        'experience_years',
        'education_level',
        'skills',
        'certifications',
        'languages',
        'last_updated_by',
        'notes',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'uploaded_at' => 'datetime',
        'experience_years' => 'integer',
        'file_size' => 'integer',
        'skills' => 'array',
        'certifications' => 'array',
        'languages' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
