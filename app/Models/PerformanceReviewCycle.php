<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'year',
        'period',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function reviews()
    {
        return $this->hasMany(PerformanceReview::class, 'review_cycle_id');
    }
}
