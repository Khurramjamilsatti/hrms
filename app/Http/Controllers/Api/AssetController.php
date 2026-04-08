<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetAssignment;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                  ->orWhere('asset_code', 'ilike', "%{$request->search}%");
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($assets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => 'required|string|unique:assets,asset_code|max:255',
            'name' => 'required|string|max:255',
            'category' => 'required|in:laptop,desktop,phone,tablet,monitor,keyboard,mouse,other',
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'warranty_expiry' => 'nullable|date',
            'status' => 'required|in:available,assigned,maintenance,retired',
        ]);

        $asset = Asset::create($validated);
        return response()->json($asset, 201);
    }

    public function show(Asset $asset)
    {
        $asset->load(['currentAssignment.employee.user']);
        return response()->json($asset);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|in:laptop,desktop,phone,tablet,monitor,keyboard,mouse,other',
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'warranty_expiry' => 'nullable|date',
            'status' => 'sometimes|in:available,assigned,maintenance,retired',
        ]);

        $asset->update($validated);
        return response()->json($asset);
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(['message' => 'Asset deleted successfully']);
    }

    // Asset Assignments
    public function assignAsset(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $assignment = AssetAssignment::create($validated);
        
        // Update asset status
        Asset::find($validated['asset_id'])->update(['status' => 'assigned']);

        return response()->json($assignment, 201);
    }

    public function returnAsset(Request $request, AssetAssignment $assignment)
    {
        $validated = $request->validate([
            'returned_date' => 'required|date',
            'condition_on_return' => 'required|in:good,fair,damaged',
            'return_notes' => 'nullable|string',
        ]);

        $assignment->update($validated);
        
        // Update asset status to available
        $assignment->asset->update(['status' => 'available']);

        return response()->json($assignment);
    }

    public function getAssignments(Request $request)
    {
        $query = AssetAssignment::with(['asset', 'employee.user']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('returned_date');
            } else {
                $query->whereNotNull('returned_date');
            }
        }

        $assignments = $query->orderBy('assigned_date', 'desc')->paginate(15);
        return response()->json($assignments);
    }
}
