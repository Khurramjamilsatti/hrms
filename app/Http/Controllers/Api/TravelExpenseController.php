<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TravelRequest;
use App\Models\ExpenseCategory;
use App\Models\ExpenseClaim;
use App\Models\AdvanceRequest;
use App\Models\MileageClaim;
use App\Models\TravelPolicy;
use Illuminate\Http\Request;

class TravelExpenseController extends Controller
{
    // Travel Requests
    public function getTravelRequests(Request $request)
    {
        $query = TravelRequest::with(['employee', 'approver']);

        if ($request->user()->role === 'employee') {
            $query->where('employee_id', $request->user()->employee->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(50);
        return response()->json($requests);
    }

    public function storeTravelRequest(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'purpose' => 'required|string|max:255',
            'description' => 'nullable|string',
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'travel_mode' => 'required|in:flight,train,bus,car,other',
            'estimated_cost' => 'required|numeric|min:0',
        ]);

        $validated['request_number'] = 'TR-' . strtoupper(uniqid());
        $validated['status'] = 'draft';

        $travelRequest = TravelRequest::create($validated);
        return response()->json($travelRequest->load('employee'), 201);
    }

    public function updateTravelRequest(Request $request, $id)
    {
        $travelRequest = TravelRequest::findOrFail($id);
        
        $validated = $request->validate([
            'purpose' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'from_location' => 'sometimes|string|max:255',
            'to_location' => 'sometimes|string|max:255',
            'departure_date' => 'sometimes|date',
            'return_date' => 'sometimes|date',
            'travel_mode' => 'sometimes|in:flight,train,bus,car,other',
            'estimated_cost' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:draft,submitted,approved,rejected,completed,cancelled',
        ]);

        $travelRequest->update($validated);
        return response()->json($travelRequest->load('employee'));
    }

    public function submitTravelRequest($id)
    {
        $travelRequest = TravelRequest::findOrFail($id);
        $travelRequest->update(['status' => 'submitted']);
        return response()->json($travelRequest);
    }

    public function approveTravelRequest(Request $request, $id)
    {
        $travelRequest = TravelRequest::findOrFail($id);
        $travelRequest->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);
        return response()->json($travelRequest);
    }

    public function rejectTravelRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $travelRequest = TravelRequest::findOrFail($id);
        $travelRequest->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);
        return response()->json($travelRequest);
    }

    // Expense Categories
    public function getExpenseCategories()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return response()->json($categories);
    }

    public function storeExpenseCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_receipt' => 'nullable|boolean',
            'max_amount' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $category = ExpenseCategory::create($validated);
        return response()->json($category, 201);
    }

    // Expense Claims
    public function getExpenseClaims(Request $request)
    {
        $query = ExpenseClaim::with(['employee', 'travelRequest', 'category', 'approver']);

        if ($request->user()->role === 'employee') {
            $query->where('employee_id', $request->user()->employee->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('travel_request_id')) {
            $query->where('travel_request_id', $request->travel_request_id);
        }

        $claims = $query->latest()->paginate(50);
        return response()->json($claims);
    }

    public function storeExpenseClaim(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'travel_request_id' => 'nullable|exists:travel_requests,id',
            'category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'merchant_name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'receipt_file' => 'nullable|string',
        ]);

        $validated['claim_number'] = 'EXP-' . strtoupper(uniqid());
        $validated['status'] = 'draft';
        $validated['currency'] = $validated['currency'] ?? 'PKR';

        $claim = ExpenseClaim::create($validated);
        return response()->json($claim->load(['employee', 'category']), 201);
    }

    public function updateExpenseClaim(Request $request, $id)
    {
        $claim = ExpenseClaim::findOrFail($id);
        
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:expense_categories,id',
            'expense_date' => 'sometimes|date',
            'merchant_name' => 'nullable|string|max:255',
            'description' => 'sometimes|string',
            'amount' => 'sometimes|numeric|min:0',
            'receipt_file' => 'nullable|string',
        ]);

        $claim->update($validated);
        return response()->json($claim->load(['employee', 'category']));
    }

    public function submitExpenseClaim($id)
    {
        $claim = ExpenseClaim::findOrFail($id);
        $claim->update(['status' => 'submitted']);
        return response()->json($claim);
    }

    public function approveExpenseClaim(Request $request, $id)
    {
        $claim = ExpenseClaim::findOrFail($id);
        $claim->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);
        return response()->json($claim);
    }

    public function rejectExpenseClaim(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $claim = ExpenseClaim::findOrFail($id);
        $claim->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);
        return response()->json($claim);
    }

    public function markExpensePaid(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
        ]);

        $claim = ExpenseClaim::findOrFail($id);
        $claim->update([
            'status' => 'paid',
            'payment_date' => $validated['payment_date'],
        ]);
        return response()->json($claim);
    }

    // Advance Requests
    public function getAdvanceRequests(Request $request)
    {
        $query = AdvanceRequest::with(['employee', 'travelRequest', 'approver']);

        if ($request->user()->role === 'employee') {
            $query->where('employee_id', $request->user()->employee->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $advances = $query->latest()->paginate(50);
        return response()->json($advances);
    }

    public function storeAdvanceRequest(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'travel_request_id' => 'nullable|exists:travel_requests,id',
            'purpose' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'required_date' => 'required|date',
        ]);

        $validated['request_number'] = 'ADV-' . strtoupper(uniqid());
        $validated['status'] = 'pending';

        $advance = AdvanceRequest::create($validated);
        return response()->json($advance->load('employee'), 201);
    }

    public function approveAdvanceRequest(Request $request, $id)
    {
        $advance = AdvanceRequest::findOrFail($id);
        $advance->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);
        return response()->json($advance);
    }

    public function rejectAdvanceRequest($id)
    {
        $advance = AdvanceRequest::findOrFail($id);
        $advance->update(['status' => 'rejected']);
        return response()->json($advance);
    }

    public function markAdvancePaid(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
        ]);

        $advance = AdvanceRequest::findOrFail($id);
        $advance->update([
            'status' => 'paid',
            'payment_date' => $validated['payment_date'],
        ]);
        return response()->json($advance);
    }

    public function settleAdvance(Request $request, $id)
    {
        $validated = $request->validate([
            'settled_amount' => 'required|numeric|min:0',
        ]);

        $advance = AdvanceRequest::findOrFail($id);
        $advance->update([
            'status' => 'settled',
            'settlement_date' => now(),
            'settled_amount' => $validated['settled_amount'],
        ]);
        return response()->json($advance);
    }

    // Mileage Claims
    public function getMileageClaims(Request $request)
    {
        $query = MileageClaim::with(['employee', 'approver']);

        if ($request->user()->role === 'employee') {
            $query->where('employee_id', $request->user()->employee->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->latest()->paginate(50);
        return response()->json($claims);
    }

    public function storeMileageClaim(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'travel_date' => 'required|date',
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0',
            'purpose' => 'required|string',
            'vehicle_type' => 'required|in:car,motorcycle,bicycle',
        ]);

        // Get mileage rate from active policy (default to 10 PKR/km if no policy)
        $policy = TravelPolicy::where('is_active', true)->first();

        $mileageRate = $policy ? $policy->mileage_rate_per_km : 10;
        $totalAmount = $validated['distance_km'] * $mileageRate;

        // Database column names match frontend field names
        $validated['claim_number'] = 'ML-' . strtoupper(uniqid());
        $validated['status'] = 'draft';
        $validated['rate_per_km'] = $mileageRate;
        $validated['total_amount'] = $totalAmount;

        $claim = MileageClaim::create($validated);
        return response()->json($claim->load('employee'), 201);
    }

    public function updateMileageClaim(Request $request, $id)
    {
        $claim = MileageClaim::findOrFail($id);
        
        $validated = $request->validate([
            'travel_date' => 'sometimes|date',
            'from_location' => 'sometimes|string|max:255',
            'to_location' => 'sometimes|string|max:255',
            'distance_km' => 'sometimes|numeric|min:0',
            'purpose' => 'sometimes|string',
            'vehicle_type' => 'sometimes|in:car,motorcycle,bicycle',
        ]);

        // Recalculate total_amount if distance changed
        if (isset($validated['distance_km'])) {
            $validated['total_amount'] = $validated['distance_km'] * $claim->rate_per_km;
        }

        $claim->update($validated);
        return response()->json($claim->load('employee'));
    }

    public function submitMileageClaim($id)
    {
        $claim = MileageClaim::findOrFail($id);
        $claim->update(['status' => 'submitted']);
        return response()->json($claim);
    }

    public function approveMileageClaim(Request $request, $id)
    {
        $claim = MileageClaim::findOrFail($id);
        $claim->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);
        return response()->json($claim);
    }

    public function rejectMileageClaim(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $claim = MileageClaim::findOrFail($id);
        $claim->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);
        return response()->json($claim);
    }

    public function markMileagePaid(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
        ]);

        $claim = MileageClaim::findOrFail($id);
        $claim->update([
            'status' => 'paid',
            'payment_date' => $validated['payment_date'],
        ]);
        return response()->json($claim);
    }

    // Travel Policies
    public function getTravelPolicies(Request $request)
    {
        $policies = TravelPolicy::latest()->get();
        return response()->json($policies);
    }

    public function storeTravelPolicy(Request $request)
    {
        $validated = $request->validate([
            'policy_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'travel_type' => 'nullable|in:domestic,international',
            'designation_level' => 'nullable|in:executive,senior,mid,junior,all',
            'max_flight_cost' => 'nullable|numeric|min:0',
            'max_hotel_per_night' => 'nullable|numeric|min:0',
            'per_diem_rate' => 'nullable|numeric|min:0',
            'mileage_rate_per_km' => 'nullable|numeric|min:0',
            'advance_allowed' => 'boolean',
            'max_advance_percentage' => 'nullable|numeric|min:0|max:100',
            'advance_days_before_travel' => 'nullable|integer|min:0',
            'settlement_days_after_return' => 'nullable|integer|min:0',
            'requires_manager_approval' => 'boolean',
            'requires_finance_approval' => 'boolean',
            'finance_approval_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $policy = TravelPolicy::create($validated);
        return response()->json($policy, 201);
    }

    public function updateTravelPolicy(Request $request, $id)
    {
        $policy = TravelPolicy::findOrFail($id);
        
        $validated = $request->validate([
            'policy_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'travel_type' => 'nullable|in:domestic,international',
            'designation_level' => 'nullable|in:executive,senior,mid,junior,all',
            'max_flight_cost' => 'nullable|numeric|min:0',
            'max_hotel_per_night' => 'nullable|numeric|min:0',
            'per_diem_rate' => 'nullable|numeric|min:0',
            'mileage_rate_per_km' => 'nullable|numeric|min:0',
            'advance_allowed' => 'boolean',
            'max_advance_percentage' => 'nullable|numeric|min:0|max:100',
            'advance_days_before_travel' => 'nullable|integer|min:0',
            'settlement_days_after_return' => 'nullable|integer|min:0',
            'requires_manager_approval' => 'boolean',
            'requires_finance_approval' => 'boolean',
            'finance_approval_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $policy->update($validated);
        return response()->json($policy);
    }
}
