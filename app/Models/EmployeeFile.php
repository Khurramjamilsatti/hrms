<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'category_id', 'title', 'description', 'file_name',
        'file_path', 'file_type', 'file_size', 'uploaded_by', 'is_confidential',
        'expiry_date', 'version', 'parent_file_id'
    ];

    protected $casts = [
        'is_confidential' => 'boolean',
        'expiry_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function category()
    {
        return $this->belongsTo(FileCategory::class, 'category_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function parentFile()
    {
        return $this->belongsTo(EmployeeFile::class, 'parent_file_id');
    }

    public function versions()
    {
        return $this->hasMany(EmployeeFile::class, 'parent_file_id');
    }

    public function accessLogs()
    {
        return $this->hasMany(FileAccessLog::class, 'file_id');
    }
}
