<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'offered_salary',
        'joining_date',
        'terms',
        'offer_letter_path',
        'status',
        'valid_until',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'offered_salary' => 'decimal:2',
            'joining_date' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class);
    }
}
