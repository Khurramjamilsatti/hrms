<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'request_number', 'purpose', 'description',
        'from_location', 'to_location', 'departure_date', 'return_date',
        'travel_mode', 'estimated_cost', 'status', 'approved_by',
        'approved_at', 'rejection_reason',
        // New comprehensive fields
        'travel_class', 'project_code', 'cost_center', 'client_name',
        'is_billable', 'accommodation_required', 'accommodation_type',
        'accommodation_cost', 'visa_required', 'visa_cost', 
        'insurance_required', 'insurance_cost', 'per_diem_rate',
        'number_of_days', 'per_diem_total', 'total_estimated_cost',
        'number_of_travelers', 'traveler_names', 'itinerary',
        'booking_reference', 'travel_agent', 'priority',
        'secondary_approver_id', 'secondary_approved_at', 'secondary_approval_remarks',
        'admin_notes', 'budget_code', 'actual_cost'
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'estimated_cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'accommodation_cost' => 'decimal:2',
        'visa_cost' => 'decimal:2',
        'insurance_cost' => 'decimal:2',
        'per_diem_rate' => 'decimal:2',
        'per_diem_total' => 'decimal:2',
        'total_estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'number_of_travelers' => 'integer',
        'number_of_days' => 'integer',
        'accommodation_required' => 'boolean',
        'visa_required' => 'boolean',
        'insurance_required' => 'boolean',
        'is_billable' => 'boolean',
        'traveler_names' => 'array',
        'secondary_approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function secondaryApprover()
    {
        return $this->belongsTo(User::class, 'secondary_approver_id');
    }

    public function expenseClaims()
    {
        return $this->hasMany(ExpenseClaim::class, 'travel_request_id');
    }

    public function advanceRequest()
    {
        return $this->hasOne(AdvanceRequest::class, 'travel_request_id');
    }

    public function itineraryItems()
    {
        return $this->hasMany(TravelItineraryItem::class, 'travel_request_id');
    }

    public function policy()
    {
        return $this->belongsTo(TravelPolicy::class, 'travel_class', 'travel_type');
    }
}
