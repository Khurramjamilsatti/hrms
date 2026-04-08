<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'department_id', 'start_date', 'end_date',
        'status', 'created_by', 'published_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'published_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(ShiftAssignment::class, 'roster_id');
    }
}
