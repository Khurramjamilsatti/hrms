<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'roster_id', 'employee_id', 'shift_id', 'date',
        'start_time', 'end_time', 'is_day_off', 'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'is_day_off' => 'boolean',
    ];

    public function roster()
    {
        return $this->belongsTo(ShiftRoster::class, 'roster_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
