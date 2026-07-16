<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::with('department')
            ->orderBy('level')
            ->get();

        return response()->json($designations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|integer|min:1',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $designation = Designation::create($validated);

        return response()->json($designation->load('department'), 201);
    }

    public function show(Designation $designation)
    {
        return response()->json($designation->load(['department', 'employees']));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|integer|min:1',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $designation->update($validated);

        return response()->json($designation->load('department'));
    }

    public function destroy(Designation $designation)
    {
        if ($designation->employees()->exists()) {
            return response()->json([
                'message' => 'Cannot delete designation with assigned employees',
            ], 400);
        }

        $designation->delete();

        return response()->json(['message' => 'Designation deleted successfully']);
    }
}
