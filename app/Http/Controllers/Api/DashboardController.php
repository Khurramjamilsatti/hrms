<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\Payroll;
use App\Models\Announcement;
use App\Models\OvertimeRequest;
use App\Models\EmployeeContract;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Super Admin & HR Admin - Full dashboard
        if ($user->isSuperAdmin() || $user->isHRAdmin() || $user->isAdmin()) {
            return $this->getAdminDashboard($today, $currentMonth, $currentYear);
        }

        // Section Head - Department-specific dashboard
        if ($user->isSectionHead()) {
            return $this->getSectionHeadDashboard($user, $today, $currentMonth, $currentYear);
        }

        // Manager - Team-specific dashboard
        if ($user->isManager()) {
            return $this->getManagerDashboard($user, $today, $currentMonth, $currentYear);
        }

        // Employee - Personal dashboard
        return $this->getEmployeeDashboard($user, $today, $currentMonth, $currentYear);
    }

    private function getAdminDashboard($today, $currentMonth, $currentYear)
    {
            // Department distribution
            $departmentStats = Employee::select(
                'departments.name',
                'departments.id',
                DB::raw('COUNT(*) as employee_count')
            )
            ->join('departments', 'employees.department_id', '=', 'departments.id')
            ->where('employment_status', 'active')
            ->groupBy('departments.id', 'departments.name')
            ->get();

            // Recent employees
            $recentEmployees = Employee::with(['department', 'designation'])
                ->where('employment_status', 'active')
                ->orderBy('joining_date', 'desc')
                ->limit(5)
                ->get();

            // Recent leave applications
            $recentLeaves = LeaveApplication::with(['employee', 'leaveType'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Attendance trend - last 7 days
            $attendanceTrend = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $attendanceCount = Attendance::where('date', $date->format('Y-m-d'))->count();
                $presentCount = Attendance::where('date', $date->format('Y-m-d'))
                    ->whereIn('status', ['present', 'late'])->count();
                $absentCount = Attendance::where('date', $date->format('Y-m-d'))
                    ->where('status', 'absent')->count();
                $leaveCount = Attendance::where('date', $date->format('Y-m-d'))
                    ->where('status', 'on_leave')->count();
                
                $attendanceTrend[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('D'),
                    'total' => $attendanceCount,
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'on_leave' => $leaveCount,
                ];
            }

            // Monthly hiring trend
            $hiringTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $monthDate = Carbon::now()->subMonths($i);
                $count = Employee::whereMonth('joining_date', $monthDate->month)
                    ->whereYear('joining_date', $monthDate->year)
                    ->count();
                
                $hiringTrend[] = [
                    'month' => $monthDate->format('M'),
                    'year' => $monthDate->format('Y'),
                    'count' => $count,
                ];
            }

            // Gender distribution
            $genderStats = Employee::select('gender', DB::raw('COUNT(*) as count'))
                ->where('employment_status', 'active')
                ->groupBy('gender')
                ->get();

            // Employment type distribution
            $employmentTypeStats = Employee::select('employment_type', DB::raw('COUNT(*) as count'))
                ->where('employment_status', 'active')
                ->groupBy('employment_type')
                ->get();

            // Upcoming birthdays (next 30 days)
            $upcomingBirthdays = Employee::select('id', 'first_name', 'last_name', 'date_of_birth', 'department_id')
                ->with('department')
                ->where('employment_status', 'active')
                ->whereNotNull('date_of_birth')
                ->get()
                ->filter(function($employee) {
                    if (!$employee->date_of_birth) return false;
                    $birthday = Carbon::parse($employee->date_of_birth);
                    $today = Carbon::today();
                    $nextBirthday = Carbon::create($today->year, $birthday->month, $birthday->day);
                    
                    if ($nextBirthday->isPast()) {
                        $nextBirthday->addYear();
                    }
                    
                    $daysUntil = $today->diffInDays($nextBirthday, false);
                    return $daysUntil >= 0 && $daysUntil <= 30;
                })
                ->sortBy(function($employee) {
                    $birthday = Carbon::parse($employee->date_of_birth);
                    $today = Carbon::today();
                    $nextBirthday = Carbon::create($today->year, $birthday->month, $birthday->day);
                    if ($nextBirthday->isPast()) {
                        $nextBirthday->addYear();
                    }
                    return $nextBirthday;
                })
                ->take(5)
                ->values();

            // Announcements (recent 3)
            $announcements = Announcement::where('is_published', true)
                ->where(function($query) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', Carbon::now());
                })
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            // Pending overtime requests
            $pendingOvertimeRequests = OvertimeRequest::where('status', 'pending')->count();

            // Leave type breakdown (current month)
            $leaveTypeStats = LeaveApplication::select('leave_type_id', DB::raw('COUNT(*) as count'))
                ->with('leaveType')
                ->whereMonth('start_date', $currentMonth)
                ->whereYear('start_date', $currentYear)
                ->where('status', 'approved')
                ->groupBy('leave_type_id')
                ->get();

            // Expiring contracts (next 30 days)
            $expiringContracts = EmployeeContract::with('employee.department')
                ->whereBetween('end_date', [
                    Carbon::today(),
                    Carbon::today()->addDays(30)
                ])
                ->where('status', 'active')
                ->orderBy('end_date')
                ->limit(5)
                ->get();

            // Attendance rate for current month
            $totalWorkingDays = Carbon::now()->diffInDaysFiltered(function(Carbon $date) {
                return !$date->isWeekend();
            }, Carbon::now()->startOfMonth());
            
            $activeEmployees = Employee::where('employment_status', 'active')->count();
            $totalExpectedAttendance = $totalWorkingDays * $activeEmployees;
            $actualAttendance = Attendance::whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->whereIn('status', ['present', 'late'])
                ->count();
            
            $attendanceRate = $totalExpectedAttendance > 0 
                ? round(($actualAttendance / $totalExpectedAttendance) * 100, 2) 
                : 0;

            return response()->json([
                'total_employees' => Employee::where('employment_status', 'active')->count(),
                'present_today' => Attendance::where('date', $today->format('Y-m-d'))
                    ->whereIn('status', ['present', 'late'])->count(),
                'absent_today' => Attendance::where('date', $today->format('Y-m-d'))
                    ->where('status', 'absent')->count(),
                'on_leave_today' => Attendance::where('date', $today->format('Y-m-d'))
                    ->where('status', 'on_leave')->count(),
                'pending_leave_requests' => LeaveApplication::where('status', 'pending')->count(),
                'departments' => DB::table('departments')
                    ->where('is_active', true)
                    ->count(),
                'recent_hires' => Employee::where('joining_date', '>=', Carbon::now()->subDays(30))
                    ->count(),
                'payroll_stats' => [
                    'total_payroll_current_month' => Payroll::where('month', $currentMonth)
                        ->where('year', $currentYear)
                        ->sum('net_salary'),
                    'processed_payrolls' => Payroll::where('month', $currentMonth)
                        ->where('year', $currentYear)
                        ->where('status', 'paid')
                        ->count(),
                    'pending_payrolls' => Payroll::where('month', $currentMonth)
                        ->where('year', $currentYear)
                        ->where('status', 'draft')
                        ->count(),
                ],
                'department_stats' => $departmentStats,
                'recent_employees' => $recentEmployees,
                'recent_leaves' => $recentLeaves,
                'attendance_trend' => $attendanceTrend,
                'hiring_trend' => $hiringTrend,
                'gender_stats' => $genderStats,
                'employment_type_stats' => $employmentTypeStats,
                'upcoming_birthdays' => $upcomingBirthdays,
                'announcements' => $announcements,
                'pending_overtime_requests' => $pendingOvertimeRequests,
                'leave_type_stats' => $leaveTypeStats,
                'expiring_contracts' => $expiringContracts,
                'attendance_rate' => $attendanceRate,
                'total_working_days' => $totalWorkingDays,
            ]);
    }

    private function getSectionHeadDashboard($user, $today, $currentMonth, $currentYear)
    {
        $sectionHeadEmployee = $user->employee;
        
        if (!$sectionHeadEmployee || !$sectionHeadEmployee->department_id) {
            return response()->json(['message' => 'Section head not assigned to department'], 400);
        }

        $departmentId = $sectionHeadEmployee->department_id;
        
        // Department employees
        $deptEmployees = Employee::where('department_id', $departmentId)
            ->where('employment_status', 'active')
            ->get();
        $deptEmployeeIds = $deptEmployees->pluck('id');

        // Announcements
        $announcements = Announcement::where('is_published', true)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', Carbon::now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'role' => 'section_head',
            'department_name' => $sectionHeadEmployee->department->name ?? 'Unknown',
            'total_employees' => $deptEmployees->count(),
            'present_today' => Attendance::whereIn('employee_id', $deptEmployeeIds)
                ->where('date', $today->format('Y-m-d'))
                ->whereIn('status', ['present', 'late'])->count(),
            'absent_today' => Attendance::whereIn('employee_id', $deptEmployeeIds)
                ->where('date', $today->format('Y-m-d'))
                ->where('status', 'absent')->count(),
            'on_leave_today' => Attendance::whereIn('employee_id', $deptEmployeeIds)
                ->where('date', $today->format('Y-m-d'))
                ->where('status', 'on_leave')->count(),
            'pending_leave_requests' => LeaveApplication::whereIn('employee_id', $deptEmployeeIds)
                ->where('status', 'pending')->count(),
            'department_employees' => $deptEmployees->take(10),
            'recent_leaves' => LeaveApplication::with(['employee.user', 'leaveType'])
                ->whereIn('employee_id', $deptEmployeeIds)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'announcements' => $announcements,
        ]);
    }

    private function getManagerDashboard($user, $today, $currentMonth, $currentYear)
    {
        // Team members
        $teamEmployees = Employee::where('manager_id', $user->id)
            ->where('employment_status', 'active')
            ->get();
        $teamEmployeeIds = $teamEmployees->pluck('id');

        // Announcements
        $announcements = Announcement::where('is_published', true)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', Carbon::now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'role' => 'manager',
            'total_team_members' => $teamEmployees->count(),
            'present_today' => Attendance::whereIn('employee_id', $teamEmployeeIds)
                ->where('date', $today->format('Y-m-d'))
                ->whereIn('status', ['present', 'late'])->count(),
            'absent_today' => Attendance::whereIn('employee_id', $teamEmployeeIds)
                ->where('date', $today->format('Y-m-d'))
                ->where('status', 'absent')->count(),
            'on_leave_today' => Attendance::whereIn('employee_id', $teamEmployeeIds)
                ->where('date', $today->format('Y-m-d'))
                ->where('status', 'on_leave')->count(),
            'pending_leave_requests' => LeaveApplication::whereIn('employee_id', $teamEmployeeIds)
                ->where('status', 'pending')->count(),
            'team_members' => $teamEmployees,
            'recent_leaves' => LeaveApplication::with(['employee.user', 'leaveType'])
                ->whereIn('employee_id', $teamEmployeeIds)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'announcements' => $announcements,
        ]);
    }

    private function getEmployeeDashboard($user, $today, $currentMonth, $currentYear)
    {
        $employee = $user->employee;
        
        if (!$employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }
        
        // Recent attendance (last 7 days)
        $myRecentAttendance = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [Carbon::now()->subDays(6)->format('Y-m-d'), $today->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->get();

        // Upcoming approved leaves
        $myUpcomingLeaves = LeaveApplication::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('start_date', '>=', $today)
            ->with('leaveType')
            ->orderBy('start_date')
            ->limit(3)
            ->get();

        // My overtime requests
        $myOvertimeRequests = OvertimeRequest::where('employee_id', $employee->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Announcements for employee
        $myAnnouncements = Announcement::where('is_published', true)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', Carbon::now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Total working hours this month
        $totalHoursThisMonth = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('working_hours');

        // Total overtime hours this month
        $totalOvertimeThisMonth = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('overtime_hours');

        return response()->json([
            'role' => 'employee',
            'my_attendance_today' => Attendance::where('employee_id', $employee->id)
                ->where('date', $today->format('Y-m-d'))
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->orderBy('id', 'desc')
                ->first(),
            'my_attendance_summary' => [
                'present_days' => Attendance::where('employee_id', $employee->id)
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->whereIn('status', ['present', 'late'])
                    ->count(),
                'absent_days' => Attendance::where('employee_id', $employee->id)
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->where('status', 'absent')
                    ->count(),
                'leave_days' => Attendance::where('employee_id', $employee->id)
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->where('status', 'on_leave')
                    ->count(),
                'total_hours' => $totalHoursThisMonth,
                'overtime_hours' => $totalOvertimeThisMonth,
            ],
            'my_leave_balance' => $employee->leaveBalances()
                ->with('leaveType')
                ->where('year', $currentYear)
                ->get(),
            'my_pending_leaves' => LeaveApplication::where('employee_id', $employee->id)
                ->where('status', 'pending')
                ->with('leaveType')
                ->get(),
            'my_recent_payslips' => Payroll::where('employee_id', $employee->id)
                ->where('status', 'paid')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(3)
                ->get(),
            'my_recent_attendance' => $myRecentAttendance,
            'my_upcoming_leaves' => $myUpcomingLeaves,
            'my_overtime_requests' => $myOvertimeRequests,
            'announcements' => $myAnnouncements,
        ]);
    }

    public function stats(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);

        $monthlyPayroll = Payroll::select(
            'month',
            DB::raw('SUM(net_salary) as total_payroll'),
            DB::raw('COUNT(*) as employee_count')
        )
        ->where('year', $year)
        ->where('status', 'paid')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $departmentStats = Employee::select(
            'departments.name',
            DB::raw('COUNT(*) as employee_count')
        )
        ->join('departments', 'employees.department_id', '=', 'departments.id')
        ->where('employment_status', 'active')
        ->groupBy('departments.id', 'departments.name')
        ->get();

        return response()->json([
            'monthly_payroll' => $monthlyPayroll,
            'department_distribution' => $departmentStats,
            'currency' => config('app.currency'),
            'currency_symbol' => config('app.currency_symbol'),
        ]);
    }
}
