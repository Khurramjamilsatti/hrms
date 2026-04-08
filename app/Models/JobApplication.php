<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_position_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'resume_path',
        'cover_letter_path',
        'expected_salary',
        'available_from',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expected_salary' => 'decimal:2',
            'available_from' => 'date',
        ];
    }

    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function offer()
    {
        return $this->hasOne(Offer::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
