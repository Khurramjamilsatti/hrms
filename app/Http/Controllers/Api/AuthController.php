<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeOnboardingTask;
use App\Models\LeaveApplication;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Your account has been deactivated.'], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('employee'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load([
            'employee.department',
            'employee.designation',
            'employee.manager',
        ]);

        if ($user->employee && $user->employee->profile_picture) {
            $picture = $user->employee->profile_picture;
            if (!str_starts_with($picture, 'http') && !str_starts_with($picture, '/storage/')) {
                $picture = '/storage/' . ltrim($picture, '/');
            }
            $user->setAttribute('profile_picture', $picture);
        }

        return response()->json($user);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'employee' => 'sometimes|array',
            'employee.phone' => 'nullable|string|max:50',
            'employee.date_of_birth' => 'nullable|date',
            'employee.gender' => 'nullable|in:male,female,other',
            'employee.cnic' => 'nullable|string|max:50',
            'employee.address' => 'nullable|string',
            'employee.emergency_contact_name' => 'nullable|string|max:255',
            'employee.emergency_contact_phone' => 'nullable|string|max:50',
            'employee.emergency_contact_relationship' => 'nullable|string|max:100',
            // Also accept flat fields for flexibility
            'phone' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'cnic' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);

        if (isset($validated['name'])) {
            $user->update(['name' => $validated['name']]);
        }

        $employeeData = $validated['employee'] ?? [];
        $flatFields = [
            'phone', 'date_of_birth', 'gender', 'cnic', 'address',
            'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
        ];
        foreach ($flatFields as $field) {
            if (array_key_exists($field, $validated)) {
                $employeeData[$field] = $validated[$field];
            }
        }

        if (!empty($employeeData) && $user->employee) {
            // Keep national_id in sync with cnic when provided
            if (array_key_exists('cnic', $employeeData) && $employeeData['cnic'] !== null) {
                $employeeData['national_id'] = $employeeData['cnic'];
            }

            // Keep legacy emergency_contact string in sync
            if (
                array_key_exists('emergency_contact_name', $employeeData)
                || array_key_exists('emergency_contact_phone', $employeeData)
            ) {
                $name = $employeeData['emergency_contact_name']
                    ?? $user->employee->emergency_contact_name;
                $phone = $employeeData['emergency_contact_phone']
                    ?? $user->employee->emergency_contact_phone;
                $employeeData['emergency_contact'] = trim(($name ?? '') . ($phone ? ' ' . $phone : ''));
            }

            $user->employee->update($employeeData);
        }

        $user->load('employee.department', 'employee.designation');
        if ($user->employee && $user->employee->profile_picture) {
            $user->setAttribute('profile_picture', $user->employee->profile_picture);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|max:5120',
        ]);

        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }

        if ($employee->profile_picture && Storage::disk('public')->exists($employee->profile_picture)) {
            Storage::disk('public')->delete($employee->profile_picture);
        }

        $file = $request->file('profile_picture');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('profile-pictures/' . $employee->id, $fileName, 'public');

        $employee->update(['profile_picture' => $path]);

        return response()->json([
            'message' => 'Profile picture updated successfully',
            'profile_picture' => '/storage/' . ltrim($path, '/'),
        ]);
    }

    public function profileStats(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'days_employed' => 0,
                'hours_employed' => 0,
                'minutes_employed' => 0,
                'leave_balance' => 0,
                'attendance_count' => 0,
                'pending_requests' => 0,
                'completed_tasks' => 0,
                'total_documents' => 0,
            ]);
        }

        $daysEmployed = 0;
        $hoursEmployed = 0;
        $minutesEmployed = 0;
        if ($employee->joining_date) {
            $totalMinutes = (int) abs($employee->joining_date->diffInMinutes(now()));
            $daysEmployed = intdiv($totalMinutes, 24 * 60);
            $hoursEmployed = intdiv($totalMinutes % (24 * 60), 60);
            $minutesEmployed = $totalMinutes % 60;
        }

        $leaveBalance = EmployeeLeaveBalance::where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->sum('remaining_days');

        $attendanceCount = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        $pendingRequests = LeaveApplication::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();

        $completedOnboardingTasks = EmployeeOnboardingTask::whereHas('onboarding', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        })->where('status', 'completed')->count();

        $timesheetCount = Timesheet::where('employee_id', $employee->id)->count();
        $completedTasks = $completedOnboardingTasks > 0 ? $completedOnboardingTasks : $timesheetCount;

        $totalDocuments = $employee->documents()->count();

        return response()->json([
            'days_employed' => $daysEmployed,
            'hours_employed' => $hoursEmployed,
            'minutes_employed' => $minutesEmployed,
            'leave_balance' => (float) $leaveBalance,
            'attendance_count' => $attendanceCount,
            'pending_requests' => $pendingRequests,
            'completed_tasks' => $completedTasks,
            'total_documents' => $totalDocuments,
        ]);
    }

    /**
     * Employees that can be linked to a user account (no user_id yet).
     */
    public function linkableEmployees(Request $request)
    {
        $user = $request->user()->loadMissing('employee');
        $canManageEmployees = $user->hasPermission('employees.update')
            || $user->hasPermission('employees.edit')
            || in_array($user->role, ['super_admin', 'hr_admin', 'admin'], true);

        if ($user->employee && ! $canManageEmployees) {
            return response()->json(['message' => 'Your account is already linked to an employee profile.'], 403);
        }

        $query = Employee::query()
            ->with(['department:id,name', 'designation:id,title'])
            ->whereNull('user_id')
            ->where(function ($q) {
                $q->whereNull('employment_status')
                    ->orWhere('employment_status', 'active');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                    ->orWhere('last_name', 'ilike', "%{$search}%")
                    ->orWhere('employee_code', 'ilike', "%{$search}%")
                    ->orWhereRaw("concat(first_name, ' ', last_name) ilike ?", ["%{$search}%"]);
            });
        }

        $employees = $query->orderBy('first_name')->orderBy('last_name')->limit(100)->get()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'employee_code' => $employee->employee_code,
                'department' => $employee->department?->name,
                'designation' => $employee->designation?->title,
            ];
        });

        return response()->json($employees);
    }

    /**
     * Link the authenticated user to an unlinked employee profile.
     */
    public function linkEmployee(Request $request)
    {
        $user = $request->user()->loadMissing('employee');

        if ($user->employee) {
            return response()->json(['message' => 'Your account is already linked to an employee profile.'], 422);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        if ($employee->user_id) {
            return response()->json(['message' => 'This employee is already linked to another user account.'], 422);
        }

        $employee->update(['user_id' => $user->id]);

        $freshUser = $user->fresh()->load([
            'employee.department',
            'employee.designation',
            'employee.manager',
        ]);

        return response()->json([
            'message' => 'Employee profile linked successfully',
            'user' => $freshUser,
            'employee' => $freshUser->employee,
        ]);
    }
}
