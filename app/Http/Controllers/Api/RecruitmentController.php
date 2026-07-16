<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\JobPosition;
use App\Models\Offer;
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

    // Interviews
    public function getInterviews(Request $request)
    {
        $query = Interview::with(['jobApplication.jobPosition', 'interviewer']);

        if ($request->filled('job_application_id')) {
            $query->where('job_application_id', $request->job_application_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('interviewer_id')) {
            $query->where('interviewer_id', $request->interviewer_id);
        }

        $interviews = $query->orderBy('scheduled_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json($interviews);
    }

    public function storeInterview(Request $request)
    {
        $validated = $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'title' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|string|max:500',
            'agenda' => 'nullable|string',
            'interviewer_id' => 'required|exists:users,id',
            'status' => 'sometimes|in:scheduled,completed,cancelled,rescheduled',
        ]);

        $validated['status'] = $validated['status'] ?? 'scheduled';

        $interview = Interview::create($validated);

        return response()->json(
            $interview->load(['jobApplication.jobPosition', 'interviewer']),
            201
        );
    }

    public function updateInterview(Request $request, Interview $interview)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'scheduled_at' => 'sometimes|date',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|string|max:500',
            'agenda' => 'nullable|string',
            'interviewer_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|in:scheduled,completed,cancelled,rescheduled',
            'rating' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'recommendation' => 'nullable|string',
        ]);

        $interview->update($validated);

        return response()->json($interview->load(['jobApplication.jobPosition', 'interviewer']));
    }

    // Offers
    public function getOffers(Request $request)
    {
        $query = Offer::with(['jobApplication.jobPosition']);

        if ($request->filled('job_application_id')) {
            $query->where('job_application_id', $request->job_application_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $offers = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json($offers);
    }

    public function storeOffer(Request $request)
    {
        $validated = $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'offered_salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'terms' => 'nullable|string',
            'offer_letter_path' => 'nullable|string',
            'status' => 'sometimes|in:sent,accepted,rejected,expired',
            'valid_until' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $validated['status'] = $validated['status'] ?? 'sent';

        $offer = Offer::create($validated);

        return response()->json($offer->load(['jobApplication.jobPosition']), 201);
    }

    public function updateOffer(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'offered_salary' => 'sometimes|numeric|min:0',
            'joining_date' => 'sometimes|date',
            'terms' => 'nullable|string',
            'offer_letter_path' => 'nullable|string',
            'status' => 'sometimes|in:sent,accepted,rejected,expired',
            'valid_until' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $offer->update($validated);

        return response()->json($offer->load(['jobApplication.jobPosition']));
    }
}
