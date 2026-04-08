<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingCourse;
use App\Models\TrainingSession;
use App\Models\TrainingEnrollment;
use App\Models\TrainingCertificate;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    // Courses
    public function getCourses(Request $request)
    {
        $query = TrainingCourse::with(['instructor', 'sessions', 'department']);
        
        // Filter by department if provided
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        $courses = $query->latest()->paginate(50);
        
        return response()->json($courses);
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'type' => 'required|in:technical,soft_skills,compliance,leadership,other',
            'duration_hours' => 'required|integer|min:1',
            'instructor_id' => 'nullable|exists:employees,id',
            'external_provider' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'delivery_mode' => 'required|in:online,classroom,hybrid,self_paced',
            'max_participants' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $course = TrainingCourse::create($validated);
        return response()->json($course->load('instructor'), 201);
    }

    public function updateCourse(Request $request, $id)
    {
        $course = TrainingCourse::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:technical,soft_skills,compliance,leadership,other',
            'duration_hours' => 'sometimes|integer|min:1',
            'instructor_id' => 'nullable|exists:employees,id',
            'external_provider' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'delivery_mode' => 'sometimes|in:online,classroom,hybrid,self_paced',
            'max_participants' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $course->update($validated);
        return response()->json($course->load('instructor'));
    }

    // Sessions
    public function getSessions(Request $request)
    {
        $query = TrainingSession::with(['course', 'enrollments']);

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->latest('start_date')->paginate(50);
        return response()->json($sessions);
    }

    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:training_courses,id',
            'session_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'available_seats' => 'nullable|integer|min:1',
            'status' => 'nullable|in:scheduled,ongoing,completed,cancelled',
        ]);

        $session = TrainingSession::create($validated);
        return response()->json($session->load('course'), 201);
    }

    public function updateSession(Request $request, $id)
    {
        $session = TrainingSession::findOrFail($id);
        
        $validated = $request->validate([
            'session_name' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'available_seats' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:scheduled,ongoing,completed,cancelled',
        ]);

        $session->update($validated);
        return response()->json($session->load('course'));
    }

    // Enrollments
    public function getEnrollments(Request $request)
    {
        $query = TrainingEnrollment::with(['session.course.instructor', 'employee.user', 'certificate']);

        // Handle employee_id parameter for specific employee
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        } elseif ($request->user()->role === 'employee' && $request->user()->employee) {
            $query->where('employee_id', $request->user()->employee->id);
        }
        // Admin/Manager without employee_id parameter sees all enrollments

        if ($request->has('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by course name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('session.course', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by course type
        if ($request->filled('type')) {
            $query->whereHas('session.course', function($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        $enrollments = $query->latest()->paginate(50);
        return response()->json($enrollments);
    }

    public function enrollEmployee(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:training_sessions,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        // Check if already enrolled
        $existing = TrainingEnrollment::where('session_id', $validated['session_id'])
            ->where('employee_id', $validated['employee_id'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Employee already enrolled'], 400);
        }

        $enrollment = TrainingEnrollment::create([
            'session_id' => $validated['session_id'],
            'employee_id' => $validated['employee_id'],
            'enrolled_by' => $request->user()->id,
            'enrolled_date' => now(),
            'status' => 'enrolled',
        ]);

        return response()->json($enrollment->load(['session.course', 'employee']), 201);
    }

    public function updateEnrollment(Request $request, $id)
    {
        $enrollment = TrainingEnrollment::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'sometimes|in:enrolled,in_progress,completed,failed,cancelled',
            'completion_date' => 'nullable|date',
            'score' => 'nullable|numeric|min:0|max:100',
            'attendance_percentage' => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $enrollment->update($validated);
        return response()->json($enrollment->load(['session.course', 'employee']));
    }

    public function deleteEnrollment($id)
    {
        $enrollment = TrainingEnrollment::findOrFail($id);
        
        // Check if certificate was issued
        if ($enrollment->certificate_issued) {
            return response()->json([
                'message' => 'Cannot delete enrollment with issued certificate'
            ], 400);
        }

        $enrollment->delete();
        return response()->json(['message' => 'Enrollment deleted successfully']);
    }

    public function issueCertificate(Request $request, $enrollmentId)
    {
        $enrollment = TrainingEnrollment::findOrFail($enrollmentId);

        if ($enrollment->status !== 'completed') {
            return response()->json(['message' => 'Enrollment must be completed first'], 400);
        }

        if ($enrollment->certificate_issued) {
            return response()->json(['message' => 'Certificate already issued'], 400);
        }

        $validated = $request->validate([
            'expiry_date' => 'nullable|date',
        ]);

        $certificateNumber = 'CERT-' . strtoupper(uniqid());

        $certificate = TrainingCertificate::create([
            'enrollment_id' => $enrollment->id,
            'certificate_number' => $certificateNumber,
            'issue_date' => now(),
            'expiry_date' => $validated['expiry_date'] ?? null,
        ]);

        $enrollment->update(['certificate_issued' => true]);

        return response()->json($certificate, 201);
    }

    public function getCertificates(Request $request)
    {
        $query = TrainingCertificate::with(['enrollment.session.course', 'enrollment.employee']);

        if ($request->user()->role === 'employee') {
            $query->whereHas('enrollment', function($q) use ($request) {
                $q->where('employee_id', $request->user()->employee->id);
            });
        }

        $certificates = $query->latest()->paginate(15);
        return response()->json($certificates);
    }
}
