<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'title',
        'scheduled_at',
        'location',
        'meeting_link',
        'agenda',
        'interviewer_id',
        'status',
        'rating',
        'feedback',
        'recommendation',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
