<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'travel_request_id', 'claim_number', 'category_id',
        'expense_date', 'merchant_name', 'description', 'amount', 'currency',
        'receipt_file', 'status', 'approved_by', 'approved_at',
        'rejection_reason', 'payment_date',
        // New comprehensive fields
        'payment_method', 'payment_reference', 'project_code', 'cost_center',
        'is_billable', 'client_name', 'tax_amount', 'total_amount',
        'distance_km', 'rate_per_km', 'mileage_amount',
        'vehicle_type', 'vehicle_registration', 'start_location',
        'end_location', 'secondary_approver_id', 'secondary_approved_at',
        'secondary_approval_remarks', 'reimbursement_method',
        'reimbursement_date', 'reimbursement_reference', 'admin_notes'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'rate_per_km' => 'decimal:2',
        'mileage_amount' => 'decimal:2',
        'is_billable' => 'boolean',
        'secondary_approved_at' => 'datetime',
        'reimbursement_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function secondaryApprover()
    {
        return $this->belongsTo(User::class, 'secondary_approver_id');
    }

    public function attachments()
    {
        return $this->hasMany(ExpenseAttachment::class, 'expense_claim_id');
    }

    public function mileageClaim()
    {
        return $this->hasOne(MileageClaim::class, 'expense_claim_id');
    }
}
