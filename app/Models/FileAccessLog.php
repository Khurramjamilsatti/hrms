<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id', 'user_id', 'action', 'accessed_at'
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public function file()
    {
        return $this->belongsTo(EmployeeFile::class, 'file_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
