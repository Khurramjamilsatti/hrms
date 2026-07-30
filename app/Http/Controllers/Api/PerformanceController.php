<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\PerformanceReview;
use App\Models\PerformanceReviewCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PerformanceController extends Controller
{
    // Performance Reviews
    public function getReviews(Request $request)
    {
        $query = PerformanceReview::with(['employee.user', 'reviewer', 'cycle']);

        $user = $request->user();
        if (! ($user->isAdmin() || $user->isHRAdmin() || $user->isSuperAdmin())) {
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('cycle_id')) {
            $query->where('review_cycle_id', $request->cycle_id);
        }

        $reviews = $query->orderByDesc('created_at')->paginate(10);

        return response()->json($reviews);
    }

    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'cycle_id' => 'required|exists:performance_review_cycles,id',
            'review_date' => 'nullable|date',
            'overall_rating' => 'required|numeric|min:1|max:5',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'goals_for_next_period' => 'nullable|string',
            'reviewer_comments' => 'nullable|string',
            'status' => 'required|in:draft,submitted,acknowledged',
        ]);

        $review = PerformanceReview::create([
            'employee_id' => $validated['employee_id'],
            'review_cycle_id' => $validated['cycle_id'],
            'reviewer_id' => $request->user()->id,
            'rating' => (int) round($validated['overall_rating']),
            'strengths' => $validated['strengths'] ?? null,
            'areas_of_improvement' => $validated['areas_for_improvement'] ?? null,
            'goals_achieved' => $validated['goals_for_next_period'] ?? null,
            'comments' => $validated['reviewer_comments'] ?? null,
            'status' => $validated['status'],
            'submitted_at' => $validated['status'] === 'submitted' ? now() : null,
            'acknowledged_at' => $validated['status'] === 'acknowledged' ? now() : null,
        ]);

        return response()->json($review->load(['employee.user', 'reviewer', 'cycle']), 201);
    }

    public function updateReview(Request $request, PerformanceReview $review)
    {
        $validated = $request->validate([
            'review_date' => 'nullable|date',
            'overall_rating' => 'sometimes|numeric|min:1|max:5',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'goals_for_next_period' => 'nullable|string',
            'reviewer_comments' => 'nullable|string',
            'status' => 'sometimes|in:draft,submitted,acknowledged',
        ]);

        $payload = [];

        if (array_key_exists('overall_rating', $validated)) {
            $payload['rating'] = (int) round($validated['overall_rating']);
        }
        if (array_key_exists('strengths', $validated)) {
            $payload['strengths'] = $validated['strengths'];
        }
        if (array_key_exists('areas_for_improvement', $validated)) {
            $payload['areas_of_improvement'] = $validated['areas_for_improvement'];
        }
        if (array_key_exists('goals_for_next_period', $validated)) {
            $payload['goals_achieved'] = $validated['goals_for_next_period'];
        }
        if (array_key_exists('reviewer_comments', $validated)) {
            $payload['comments'] = $validated['reviewer_comments'];
        }
        if (array_key_exists('status', $validated)) {
            $payload['status'] = $validated['status'];
            if ($validated['status'] === 'submitted' && ! $review->submitted_at) {
                $payload['submitted_at'] = now();
            }
            if ($validated['status'] === 'acknowledged' && ! $review->acknowledged_at) {
                $payload['acknowledged_at'] = now();
            }
        }

        $review->update($payload);

        return response()->json($review->fresh()->load(['employee.user', 'reviewer', 'cycle']));
    }

    // Goals
    public function getGoals(Request $request)
    {
        $query = Goal::with(['employee.user']);

        $user = $request->user();
        if (! ($user->isAdmin() || $user->isHRAdmin() || $user->isSuperAdmin())) {
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
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
            'start_date' => 'nullable|date',
            'target_date' => 'required|date',
            'status' => 'required|in:not_started,in_progress,completed,cancelled',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $progress = $validated['progress_percentage']
            ?? $validated['progress']
            ?? 0;

        $goal = Goal::create([
            'employee_id' => $validated['employee_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'start_date' => $validated['start_date'] ?? now()->toDateString(),
            'target_date' => $validated['target_date'],
            'status' => $validated['status'],
            'progress' => $progress,
            'set_by' => $request->user()->id,
        ]);

        return response()->json($goal->load(['employee.user']), 201);
    }

    public function updateGoal(Request $request, Goal $goal)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'sometimes|date',
            'status' => 'sometimes|in:not_started,in_progress,completed,cancelled',
            'progress_percentage' => 'sometimes|integer|min:0|max:100',
            'progress' => 'sometimes|integer|min:0|max:100',
        ]);

        $payload = [];

        if (array_key_exists('title', $validated)) {
            $payload['title'] = $validated['title'];
        }
        if (array_key_exists('description', $validated)) {
            $payload['description'] = $validated['description'] ?? '';
        }
        if (array_key_exists('target_date', $validated)) {
            $payload['target_date'] = $validated['target_date'];
        }
        if (array_key_exists('status', $validated)) {
            $payload['status'] = $validated['status'];
        }
        if (array_key_exists('progress_percentage', $validated)) {
            $payload['progress'] = $validated['progress_percentage'];
        } elseif (array_key_exists('progress', $validated)) {
            $payload['progress'] = $validated['progress'];
        }

        $goal->update($payload);

        return response()->json($goal->fresh()->load(['employee.user']));
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
            'name' => 'required_without:title|nullable|string|max:255',
            'title' => 'required_without:name|nullable|string|max:255',
            'year' => 'nullable|integer|min:2000|max:2100',
            'period' => 'nullable|in:quarterly,half_yearly,annual',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:upcoming,active,completed',
        ]);

        $title = $validated['title'] ?? $validated['name'];
        $start = Carbon::parse($validated['start_date']);

        $cycle = PerformanceReviewCycle::create([
            'title' => $title,
            'year' => $validated['year'] ?? (int) $start->format('Y'),
            'period' => $validated['period'] ?? 'annual',
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
        ]);

        return response()->json($cycle, 201);
    }
}
