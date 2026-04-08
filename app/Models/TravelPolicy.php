<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_name', 'description', 'travel_type', 'designation_level',
        'max_flight_cost', 'max_hotel_per_night', 'per_diem_rate', 'mileage_rate_per_km',
        'advance_allowed', 'max_advance_percentage', 'advance_days_before_travel',
        'settlement_days_after_return', 'requires_manager_approval', 'requires_finance_approval',
        'finance_approval_threshold', 'is_active'
    ];

    protected $casts = [
        'max_flight_cost' => 'decimal:2',
        'max_hotel_per_night' => 'decimal:2',
        'per_diem_rate' => 'decimal:2',
        'mileage_rate_per_km' => 'decimal:2',
        'max_advance_percentage' => 'decimal:2',
        'finance_approval_threshold' => 'decimal:2',
        'advance_allowed' => 'boolean',
        'requires_manager_approval' => 'boolean',
        'requires_finance_approval' => 'boolean',
        'is_active' => 'boolean',
        'advance_days_before_travel' => 'integer',
        'settlement_days_after_return' => 'integer',
    ];

    public function travelRequests()
    {
        return $this->hasMany(TravelRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDesignation($query, $level)
    {
        return $query->where(function($q) use ($level) {
            $q->where('designation_level', $level)
              ->orWhere('designation_level', 'all');
        });
    }
}
