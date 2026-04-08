<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobPosition;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class RecruitmentController extends Controller
{
    // Job Positions
    public function getPositions(Request $request)
    {
        $query = JobPosition::with(['department']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $positions = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($positions);
    }

    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'employment_type' => 'required|in:full_time,part_time,contract,internship',
            'salary_range_min' => 'nullable|numeric',
            'salary_range_max' => 'nullable|numeric',
            'positions_available' => 'required|integer|min:1',
            'status' => 'required|in:draft,open,closed',
        ]);

        $position = JobPosition::create($validated);
        return response()->json($position, 201);
    }

    public function updatePosition(Request $request, JobPosition $position)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'department_id' => 'sometimes|exists:departments,id',
            'description' => 'sometimes|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'employment_type' => 'sometimes|in:full_time,part_time,contract,internship',
            'salary_range_min' => 'nullable|numeric',
            'salary_range_max' => 'nullable|numeric',
            'positions_available' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:draft,open,closed',
        ]);

        $position->update($validated);
        return response()->json($position);
    }

    public function deletePosition(JobPosition $position)
    {
        $position->delete();
        return response()->json(['message' => 'Position deleted successfully']);
    }

    // Job Applications
    public function getApplications(Request $request)
    {
        $query = JobApplication::with(['jobPosition', 'interviews']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('position_id')) {
            $query->where('job_position_id', $request->position_id);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($applications);
    }

    public function storeApplication(Request $request)
    {
        $validated = $request->validate([
            'job_position_id' => 'required|exists:job_positions,id',
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'resume_path' => 'nullable|string',
            'cover_letter' => 'nullable|string',
            'status' => 'required|in:applied,screening,interview,offered,hired,rejected',
        ]);

        $application = JobApplication::create($validated);
        return response()->json($application, 201);
    }

    public function updateApplicationStatus(Request $request, JobApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:applied,screening,interview,offered,hired,rejected',
            'notes' => 'nullable|string',
        ]);

        $application->update($validated);
        return response()->json($application);
    }
}
