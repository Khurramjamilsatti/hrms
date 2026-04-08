<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceReview;
use App\Models\PerformanceReviewCycle;
use App\Models\Goal;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    // Performance Reviews
    public function getReviews(Request $request)
    {
        $query = PerformanceReview::with(['employee.user', 'reviewer', 'cycle']);

        if (!auth()->user()->role === 'admin') {
            $query->where('employee_id', auth()->user()->employee->id);
        }

        if ($request->filled('cycle_id')) {
            $query->where('cycle_id', $request->cycle_id);
        }

        $reviews = $query->orderBy('review_date', 'desc')->paginate(10);
        return response()->json($reviews);
    }

    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'cycle_id' => 'required|exists:performance_review_cycles,id',
            'review_date' => 'required|date',
            'overall_rating' => 'required|numeric|min:1|max:5',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'goals_for_next_period' => 'nullable|string',
            'reviewer_comments' => 'nullable|string',
            'status' => 'required|in:draft,submitted,acknowledged',
        ]);

        $validated['reviewer_id'] = auth()->id();

        $review = PerformanceReview::create($validated);
        return response()->json($review, 201);
    }

    public function updateReview(Request $request, PerformanceReview $review)
    {
        $validated = $request->validate([
            'review_date' => 'sometimes|date',
            'overall_rating' => 'sometimes|numeric|min:1|max:5',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'goals_for_next_period' => 'nullable|string',
            'reviewer_comments' => 'nullable|string',
            'status' => 'sometimes|in:draft,submitted,acknowledged',
        ]);

        $review->update($validated);
        return response()->json($review);
    }

    // Goals
    public function getGoals(Request $request)
    {
        $query = Goal::with(['employee.user']);

        if (!auth()->user()->role === 'admin') {
            $query->where('employee_id', auth()->user()->employee->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $goals = $query->orderBy('target_date', 'desc')->get();
        return response()->json($goals);
    }

    public function storeGoal(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'required|date',
            'status' => 'required|in:not_started,in_progress,completed,cancelled',
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        $goal = Goal::create($validated);
        return response()->json($goal, 201);
    }

    public function updateGoal(Request $request, Goal $goal)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'sometimes|date',
            'status' => 'sometimes|in:not_started,in_progress,completed,cancelled',
            'progress_percentage' => 'sometimes|integer|min:0|max:100',
        ]);

        $goal->update($validated);
        return response()->json($goal);
    }

    // Review Cycles
    public function getCycles()
    {
        $cycles = PerformanceReviewCycle::orderBy('start_date', 'desc')->get();
        return response()->json($cycles);
    }

    public function storeCycle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:upcoming,active,completed',
        ]);

        $cycle = PerformanceReviewCycle::create($validated);
        return response()->json($cycle, 201);
    }
}
