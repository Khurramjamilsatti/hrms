<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'title',
        'description',
        'start_date',
        'target_date',
        'weight',
        'status',
        'progress',
        'achievement_notes',
        'set_by',
    ];

    protected $appends = ['progress_percentage'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'target_date' => 'date',
        ];
    }

    public function getProgressPercentageAttribute(): int
    {
        return (int) ($this->attributes['progress'] ?? 0);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function setter()
    {
        return $this->belongsTo(User::class, 'set_by');
    }
}
