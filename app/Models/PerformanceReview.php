<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'review_cycle_id',
        'reviewer_id',
        'rating',
        'strengths',
        'areas_of_improvement',
        'goals_achieved',
        'comments',
        'employee_comments',
        'status',
        'submitted_at',
        'acknowledged_at',
    ];

    protected $appends = [
        'cycle_id',
        'overall_rating',
        'areas_for_improvement',
        'goals_for_next_period',
        'reviewer_comments',
        'review_date',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function getCycleIdAttribute(): ?int
    {
        return isset($this->attributes['review_cycle_id'])
            ? (int) $this->attributes['review_cycle_id']
            : null;
    }

    public function getOverallRatingAttribute(): ?float
    {
        return isset($this->attributes['rating']) ? (float) $this->attributes['rating'] : null;
    }

    public function getAreasForImprovementAttribute(): ?string
    {
        return $this->attributes['areas_of_improvement'] ?? null;
    }

    public function getGoalsForNextPeriodAttribute(): ?string
    {
        return $this->attributes['goals_achieved'] ?? null;
    }

    public function getReviewerCommentsAttribute(): ?string
    {
        return $this->attributes['comments'] ?? null;
    }

    public function getReviewDateAttribute(): ?string
    {
        $date = $this->attributes['submitted_at'] ?? $this->attributes['created_at'] ?? null;

        return $date ? substr((string) $date, 0, 10) : null;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewCycle()
    {
        return $this->belongsTo(PerformanceReviewCycle::class, 'review_cycle_id');
    }

    public function cycle()
    {
        return $this->reviewCycle();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
