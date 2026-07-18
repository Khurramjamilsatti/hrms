<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\JobPosition;
use App\Models\Offer;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class RecruitmentController extends Controller
{
    private const RECRUITMENT_ROLES = ['hr_admin', 'super_admin', 'admin'];

    public function __construct(protected NotificationService $notifier)
    {
    }
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
            'applicant_name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:120',
            'last_name' => 'nullable|string|max:120',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:40',
            'resume_path' => 'nullable|string|max:500',
            'cover_letter' => 'nullable|string|max:10000',
            'cover_letter_path' => 'nullable|string|max:500',
            'expected_salary' => 'nullable|numeric',
            'available_from' => 'nullable|date',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:5000',
            'status' => 'nullable|in:applied,screening,interview,offered,hired,rejected',
        ]);

        [$firstName, $lastName] = $this->splitApplicantName(
            $validated['applicant_name'] ?? null,
            $validated['first_name'] ?? null,
            $validated['last_name'] ?? null
        );

        if ($firstName === '') {
            return response()->json([
                'message' => 'Applicant name is required.',
                'errors' => ['applicant_name' => ['Applicant name is required.']],
            ], 422);
        }

        $application = JobApplication::create([
            'job_position_id' => $validated['job_position_id'],
            'first_name' => $firstName,
            'last_name' => $lastName !== '' ? $lastName : '-',
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
            'resume_path' => $validated['resume_path'] ?? null,
            'cover_letter_path' => $validated['cover_letter_path'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'expected_salary' => $validated['expected_salary'] ?? null,
            'available_from' => $validated['available_from'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'] ?? 'applied',
        ]);

        $application->load('jobPosition');

        $this->notifier->notifyRoles(
            self::RECRUITMENT_ROLES,
            'recruitment_application',
            'New Job Application',
            "{$application->first_name} {$application->last_name} applied for {$application->jobPosition?->title}",
            [
                'application_id' => $application->id,
                'position_id' => $application->job_position_id,
                'applicant_email' => $application->email,
            ],
            '/recruitment',
            'normal',
            [$request->user()?->id]
        );

        return response()->json($application, 201);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitApplicantName(?string $fullName, ?string $firstName, ?string $lastName): array
    {
        if (filled($firstName) || filled($lastName)) {
            return [trim((string) $firstName), trim((string) $lastName)];
        }

        $parts = preg_split('/\s+/', trim((string) $fullName), 2) ?: [];

        return [
            trim($parts[0] ?? ''),
            trim($parts[1] ?? ''),
        ];
    }

    public function updateApplicationStatus(Request $request, JobApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:applied,screening,interview,offered,hired,rejected',
            'notes' => 'nullable|string',
        ]);

        $previousStatus = $application->status;
        $application->update($validated);

        if ($previousStatus !== $application->status) {
            $application->loadMissing('jobPosition');
            $this->notifier->notifyRoles(
                self::RECRUITMENT_ROLES,
                'recruitment_status',
                'Application Status Updated',
                "{$application->first_name} {$application->last_name} ({$application->jobPosition?->title}) moved from {$previousStatus} to {$application->status}",
                [
                    'application_id' => $application->id,
                    'position_id' => $application->job_position_id,
                    'status' => $application->status,
                ],
                '/recruitment',
                'normal',
                [$request->user()?->id]
            );
        }

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
        $interview->load(['jobApplication.jobPosition', 'interviewer']);

        $applicantName = trim("{$interview->jobApplication?->first_name} {$interview->jobApplication?->last_name}");
        $scheduledAt = $interview->scheduled_at ? date('M j, Y g:i A', strtotime((string) $interview->scheduled_at)) : '';
        $interviewData = [
            'interview_id' => $interview->id,
            'application_id' => $interview->job_application_id,
            'scheduled_at' => (string) $interview->scheduled_at,
        ];

        // Notify the assigned interviewer directly
        $this->notifier->notifyUser(
            $interview->interviewer_id,
            'recruitment_interview',
            'Interview Assigned to You',
            "You are scheduled to interview {$applicantName} for {$interview->jobApplication?->jobPosition?->title} on {$scheduledAt}",
            $interviewData,
            '/recruitment',
            'high'
        );

        $this->notifier->notifyRoles(
            self::RECRUITMENT_ROLES,
            'recruitment_interview',
            'Interview Scheduled',
            "Interview with {$applicantName} scheduled on {$scheduledAt}",
            $interviewData,
            '/recruitment',
            'normal',
            [$request->user()?->id, $interview->interviewer_id]
        );

        return response()->json($interview, 201);
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

        $previousStatus = $interview->status;
        $previousSchedule = (string) $interview->scheduled_at;

        $interview->update($validated);
        $interview->load(['jobApplication.jobPosition', 'interviewer']);

        $statusChanged = $previousStatus !== $interview->status;
        $rescheduled = $previousSchedule !== (string) $interview->scheduled_at;

        if ($statusChanged || $rescheduled) {
            $applicantName = trim("{$interview->jobApplication?->first_name} {$interview->jobApplication?->last_name}");
            $scheduledAt = $interview->scheduled_at ? date('M j, Y g:i A', strtotime((string) $interview->scheduled_at)) : '';
            $message = $rescheduled
                ? "Interview with {$applicantName} was rescheduled to {$scheduledAt}"
                : "Interview with {$applicantName} is now {$interview->status}";

            $this->notifier->notifyUser(
                $interview->interviewer_id !== $request->user()?->id ? $interview->interviewer_id : null,
                'recruitment_interview',
                'Interview Updated',
                $message,
                [
                    'interview_id' => $interview->id,
                    'application_id' => $interview->job_application_id,
                    'status' => $interview->status,
                    'scheduled_at' => (string) $interview->scheduled_at,
                ],
                '/recruitment'
            );
        }

        return response()->json($interview);
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
        $offer->load(['jobApplication.jobPosition']);

        $applicantName = trim("{$offer->jobApplication?->first_name} {$offer->jobApplication?->last_name}");

        $this->notifier->notifyRoles(
            self::RECRUITMENT_ROLES,
            'recruitment_offer',
            'Job Offer Created',
            "An offer of " . number_format((float) $offer->offered_salary) . " was extended to {$applicantName} for {$offer->jobApplication?->jobPosition?->title}",
            [
                'offer_id' => $offer->id,
                'application_id' => $offer->job_application_id,
                'status' => $offer->status,
            ],
            '/recruitment',
            'normal',
            [$request->user()?->id]
        );

        return response()->json($offer, 201);
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

        $previousStatus = $offer->status;
        $offer->update($validated);
        $offer->load(['jobApplication.jobPosition']);

        if ($previousStatus !== $offer->status) {
            $applicantName = trim("{$offer->jobApplication?->first_name} {$offer->jobApplication?->last_name}");

            $this->notifier->notifyRoles(
                self::RECRUITMENT_ROLES,
                'recruitment_offer',
                'Offer ' . ucfirst($offer->status),
                "The offer for {$applicantName} ({$offer->jobApplication?->jobPosition?->title}) is now {$offer->status}",
                [
                    'offer_id' => $offer->id,
                    'application_id' => $offer->job_application_id,
                    'status' => $offer->status,
                ],
                '/recruitment',
                $offer->status === 'accepted' ? 'high' : 'normal',
                [$request->user()?->id]
            );
        }

        return response()->json($offer);
    }
}
