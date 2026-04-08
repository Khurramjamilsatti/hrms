<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveApplicationController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\RecruitmentController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\TimesheetController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\TrainingController;
use App\Http\Controllers\Api\TravelExpenseController;
use App\Http\Controllers\Api\ShiftSchedulingController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\HelpdeskController;
use App\Http\Controllers\Api\FileManagementController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\SalaryAdvanceController;
use App\Http\Controllers\Api\SalaryComponentController;
use App\Http\Controllers\Api\CvBankController;
use App\Http\Controllers\Api\DeploymentController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserRoleController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Departments - Permission-based access
    Route::get('/departments', [DepartmentController::class, 'index'])->middleware('permission:departments.view');
    Route::post('/departments', [DepartmentController::class, 'store'])->middleware('permission:departments.create');
    Route::get('/departments/{department}', [DepartmentController::class, 'show'])->middleware('permission:departments.view');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->middleware('permission:departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->middleware('permission:departments.delete');

    // Dropdown data for forms
    Route::get('/designations', function () {
        return response()->json(\App\Models\Designation::orderBy('level')->get());
    });

    // Employees - Permission-based access
    Route::get('/employees/all', [EmployeeController::class, 'getAllEmployees'])->middleware('permission:employees.view');
    Route::get('/employees/dropdown', [EmployeeController::class, 'getAllEmployeesForDropdown'])->middleware('permission:employees.view');
    Route::get('/employees/section-heads', [EmployeeController::class, 'getSectionHeads'])->middleware('permission:employees.view');
    Route::get('/employees', [EmployeeController::class, 'index'])->middleware('permission:employees.view');
    Route::post('/employees', [EmployeeController::class, 'store'])->middleware('permission:employees.create');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->middleware('permission:employees.view');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->middleware('permission:employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->middleware('permission:employees.delete');

    // Attendance - Permission-based access
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->middleware('permission:attendance.view');
        Route::post('/', [AttendanceController::class, 'store'])->middleware('permission:attendance.checkin');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->middleware('permission:attendance.checkin');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->middleware('permission:attendance.checkin');
        Route::get('/summary', [AttendanceController::class, 'summary'])->middleware('permission:attendance.view');
        Route::get('/{attendance}', [AttendanceController::class, 'show'])->middleware('permission:attendance.view');
        Route::put('/{attendance}', [AttendanceController::class, 'update'])->middleware('permission:attendance.manage');
        Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->middleware('permission:attendance.manage');
    });

    // Leave Types
    Route::get('/leave-types', function () {
        return response()->json(\App\Models\LeaveType::where('is_active', true)->get());
    });

    // Leave Applications - Permission-based access
    Route::prefix('leave-applications')->group(function () {
        Route::get('/', [LeaveApplicationController::class, 'index'])->middleware('permission:leaves.view');
        Route::post('/', [LeaveApplicationController::class, 'store'])->middleware('permission:leaves.apply');
        Route::get('/{leaveApplication}', [LeaveApplicationController::class, 'show'])->middleware('permission:leaves.view');
        Route::post('/{leaveApplication}/approve', [LeaveApplicationController::class, 'approve'])
            ->middleware('permission:leaves.approve');
        Route::post('/{leaveApplication}/reject', [LeaveApplicationController::class, 'reject'])
            ->middleware('permission:leaves.approve');
        Route::post('/{leaveApplication}/cancel', [LeaveApplicationController::class, 'cancel'])
            ->middleware('permission:leaves.cancel');
    });

    // Payroll - Permission-based access
    Route::prefix('payroll')->middleware('permission:payroll.view,payroll.manage')->group(function () {
        Route::get('/', [PayrollController::class, 'index']);
        Route::post('/generate', [PayrollController::class, 'generateMonthlyPayroll'])->middleware('permission:payroll.create');
        Route::get('/{payroll}', [PayrollController::class, 'show']);
        Route::post('/{payroll}/process', [PayrollController::class, 'processPayroll'])->middleware('permission:payroll.process');
        Route::post('/{payroll}/mark-paid', [PayrollController::class, 'markAsPaid'])->middleware('permission:payroll.process');
    });

    // Employee can view their own payroll
    Route::get('/my-payroll', [PayrollController::class, 'index']);
    Route::get('/my-payroll/{payroll}', [PayrollController::class, 'show']);

    // Recruitment - Permission-based access
    Route::prefix('recruitment')->group(function () {
        Route::get('/positions', [RecruitmentController::class, 'getPositions'])->middleware('permission:recruitment.view');
        Route::post('/positions', [RecruitmentController::class, 'storePosition'])->middleware('permission:recruitment.create');
        Route::put('/positions/{position}', [RecruitmentController::class, 'updatePosition'])->middleware('permission:recruitment.update');
        Route::delete('/positions/{position}', [RecruitmentController::class, 'deletePosition'])->middleware('permission:recruitment.delete');
        
        Route::get('/applications', [RecruitmentController::class, 'getApplications'])->middleware('permission:recruitment.view');
        Route::post('/applications', [RecruitmentController::class, 'storeApplication'])->middleware('permission:recruitment.create');
        Route::post('/applications/{application}/status', [RecruitmentController::class, 'updateApplicationStatus'])
            ->middleware('permission:recruitment.manage');
    });

    // Performance Reviews - Permission-based access
    Route::prefix('performance')->group(function () {
        Route::get('/reviews', [PerformanceController::class, 'getReviews'])->middleware('permission:performance.view');
        Route::get('/goals', [PerformanceController::class, 'getGoals'])->middleware('permission:performance.view');
        
        Route::post('/reviews', [PerformanceController::class, 'storeReview'])->middleware('permission:performance.create');
        Route::put('/reviews/{review}', [PerformanceController::class, 'updateReview'])->middleware('permission:performance.update');
        Route::post('/goals', [PerformanceController::class, 'storeGoal'])->middleware('permission:performance.create');
        Route::put('/goals/{goal}', [PerformanceController::class, 'updateGoal'])->middleware('permission:performance.update');
        Route::get('/cycles', [PerformanceController::class, 'getCycles'])->middleware('permission:performance.view');
        Route::post('/cycles', [PerformanceController::class, 'storeCycle'])->middleware('permission:performance.manage');
    });

    // Assets - Permission-based access
    Route::prefix('assets')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->middleware('permission:assets.view');
        Route::post('/', [AssetController::class, 'store'])->middleware('permission:assets.create');
        Route::get('/{asset}', [AssetController::class, 'show'])->middleware('permission:assets.view');
        Route::put('/{asset}', [AssetController::class, 'update'])->middleware('permission:assets.update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->middleware('permission:assets.delete');
        Route::post('/assign', [AssetController::class, 'assignAsset'])->middleware('permission:assets.assign');
        Route::post('/assignments/{assignment}/return', [AssetController::class, 'returnAsset'])->middleware('permission:assets.assign');
        Route::get('/assignments/list', [AssetController::class, 'getAssignments'])->middleware('permission:assets.view');
    });

    // Announcements - Permission-based access
    Route::prefix('announcements')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->middleware('permission:announcements.view');
        Route::post('/', [AnnouncementController::class, 'store'])->middleware('permission:announcements.create');
        Route::put('/{announcement}', [AnnouncementController::class, 'update'])->middleware('permission:announcements.update');
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->middleware('permission:announcements.delete');
    });

    // Timesheets & Projects - Permission-based access
    Route::prefix('timesheets')->group(function () {
        Route::get('/projects', [TimesheetController::class, 'getProjects'])->middleware('permission:timesheets.view');
        Route::post('/projects', [TimesheetController::class, 'storeProject'])->middleware('permission:timesheets.manage');
        Route::put('/projects/{project}', [TimesheetController::class, 'updateProject'])->middleware('permission:timesheets.manage');
        
        Route::get('/projects/{project}/tasks', [TimesheetController::class, 'getProjectTasks'])->middleware('permission:timesheets.view');
        Route::post('/tasks', [TimesheetController::class, 'storeTask'])->middleware('permission:timesheets.manage');
        Route::put('/tasks/{task}', [TimesheetController::class, 'updateTask'])->middleware('permission:timesheets.update');
        
        Route::get('/', [TimesheetController::class, 'getTimesheets'])->middleware('permission:timesheets.view');
        Route::post('/', [TimesheetController::class, 'storeTimesheet'])->middleware('permission:timesheets.create');
        Route::put('/{timesheet}', [TimesheetController::class, 'updateTimesheet'])->middleware('permission:timesheets.update');
        Route::post('/{timesheet}/submit', [TimesheetController::class, 'submitTimesheet'])->middleware('permission:timesheets.create');
        Route::post('/{timesheet}/approve', [TimesheetController::class, 'approveTimesheet'])->middleware('permission:timesheets.approve');
        Route::post('/{timesheet}/reject', [TimesheetController::class, 'rejectTimesheet'])->middleware('permission:timesheets.approve');
        Route::get('/summary', [TimesheetController::class, 'getTimesheetSummary'])->middleware('permission:timesheets.view');
    });

    // Onboarding - Permission-based access
    Route::prefix('onboarding')->group(function () {
        Route::get('/templates', [OnboardingController::class, 'getTemplates'])->middleware('permission:onboarding.view');
        Route::post('/templates', [OnboardingController::class, 'storeTemplate'])->middleware('permission:onboarding.create');
        Route::get('/templates/{template}', [OnboardingController::class, 'getTemplate'])->middleware('permission:onboarding.view');
        Route::put('/templates/{template}', [OnboardingController::class, 'updateTemplate'])->middleware('permission:onboarding.update');
        
        Route::post('/template-tasks', [OnboardingController::class, 'storeTemplateTask'])->middleware('permission:onboarding.create');
        Route::put('/template-tasks/{task}', [OnboardingController::class, 'updateTemplateTask'])->middleware('permission:onboarding.update');
        Route::delete('/template-tasks/{task}', [OnboardingController::class, 'deleteTemplateTask'])->middleware('permission:onboarding.delete');
        
        Route::get('/', [OnboardingController::class, 'getOnboardings'])->middleware('permission:onboarding.view');
        Route::post('/start', [OnboardingController::class, 'startOnboarding'])->middleware('permission:onboarding.manage');
        Route::get('/{onboarding}', [OnboardingController::class, 'show'])->middleware('permission:onboarding.view');
        Route::put('/{onboarding}', [OnboardingController::class, 'updateOnboarding'])->middleware('permission:onboarding.update');
        Route::delete('/{onboarding}', [OnboardingController::class, 'deleteOnboarding'])->middleware('permission:onboarding.delete');
        Route::post('/tasks/{task}/complete', [OnboardingController::class, 'completeTask'])->middleware('permission:onboarding.update');
        Route::post('/tasks/{task}/skip', [OnboardingController::class, 'skipTask'])->middleware('permission:onboarding.manage');
    });

    // Training - Permission-based access
    Route::prefix('training')->group(function () {
        Route::get('/courses', [TrainingController::class, 'getCourses'])->middleware('permission:training.view');
        Route::get('/sessions', [TrainingController::class, 'getSessions'])->middleware('permission:training.view');
        Route::get('/enrollments', [TrainingController::class, 'getEnrollments'])->middleware('permission:training.view');
        Route::get('/certificates', [TrainingController::class, 'getCertificates'])->middleware('permission:training.view');
        
        Route::post('/courses', [TrainingController::class, 'storeCourse'])->middleware('permission:training.create');
        Route::put('/courses/{course}', [TrainingController::class, 'updateCourse'])->middleware('permission:training.update');
        
        Route::post('/sessions', [TrainingController::class, 'storeSession'])->middleware('permission:training.create');
        Route::put('/sessions/{session}', [TrainingController::class, 'updateSession'])->middleware('permission:training.update');
        
        Route::post('/enrollments', [TrainingController::class, 'enrollEmployee'])->middleware('permission:training.manage');
        Route::put('/enrollments/{enrollment}', [TrainingController::class, 'updateEnrollment'])->middleware('permission:training.manage');
        Route::delete('/enrollments/{enrollment}', [TrainingController::class, 'deleteEnrollment'])->middleware('permission:training.delete');
        Route::post('/enrollments/{enrollment}/certificate', [TrainingController::class, 'issueCertificate'])
            ->middleware('permission:training.manage');
    });

    // Travel & Expenses - Permission-based access
    Route::prefix('travel-expenses')->group(function () {
        Route::get('/travel-requests', [TravelExpenseController::class, 'getTravelRequests'])->middleware('permission:travel.view');
        Route::post('/travel-requests', [TravelExpenseController::class, 'storeTravelRequest'])->middleware('permission:travel.create');
        Route::put('/travel-requests/{travelRequest}', [TravelExpenseController::class, 'updateTravelRequest'])->middleware('permission:travel.update');
        Route::post('/travel-requests/{travelRequest}/submit', [TravelExpenseController::class, 'submitTravelRequest'])->middleware('permission:travel.create');
        
        Route::get('/expense-categories', [TravelExpenseController::class, 'getExpenseCategories'])->middleware('permission:travel.view');
        Route::get('/expense-claims', [TravelExpenseController::class, 'getExpenseClaims'])->middleware('permission:travel.view');
        Route::post('/expense-claims', [TravelExpenseController::class, 'storeExpenseClaim'])->middleware('permission:travel.create');
        Route::put('/expense-claims/{expenseClaim}', [TravelExpenseController::class, 'updateExpenseClaim'])->middleware('permission:travel.update');
        Route::post('/expense-claims/{expenseClaim}/submit', [TravelExpenseController::class, 'submitExpenseClaim'])->middleware('permission:travel.create');
        
        Route::get('/advance-requests', [TravelExpenseController::class, 'getAdvanceRequests'])->middleware('permission:travel.view');
        Route::post('/advance-requests', [TravelExpenseController::class, 'storeAdvanceRequest'])->middleware('permission:travel.create');
        
        Route::get('/mileage-claims', [TravelExpenseController::class, 'getMileageClaims'])->middleware('permission:travel.view');
        Route::post('/mileage-claims', [TravelExpenseController::class, 'storeMileageClaim'])->middleware('permission:travel.create');
        Route::put('/mileage-claims/{mileageClaim}', [TravelExpenseController::class, 'updateMileageClaim'])->middleware('permission:travel.update');
        Route::post('/mileage-claims/{mileageClaim}/submit', [TravelExpenseController::class, 'submitMileageClaim'])->middleware('permission:travel.create');
        
        Route::get('/travel-policies', [TravelExpenseController::class, 'getTravelPolicies'])->middleware('permission:travel.view');
        Route::post('/travel-policies', [TravelExpenseController::class, 'storeTravelPolicy'])->middleware('permission:travel.manage');
        Route::put('/travel-policies/{travelPolicy}', [TravelExpenseController::class, 'updateTravelPolicy'])->middleware('permission:travel.manage');
        
        Route::post('/expense-categories', [TravelExpenseController::class, 'storeExpenseCategory'])->middleware('permission:travel.manage');
        
        Route::post('/travel-requests/{travelRequest}/approve', [TravelExpenseController::class, 'approveTravelRequest'])
            ->middleware('permission:travel.approve');
        Route::post('/travel-requests/{travelRequest}/reject', [TravelExpenseController::class, 'rejectTravelRequest'])
            ->middleware('permission:travel.approve');
        
        Route::post('/expense-claims/{expenseClaim}/approve', [TravelExpenseController::class, 'approveExpenseClaim'])
            ->middleware('permission:travel.approve');
        Route::post('/expense-claims/{expenseClaim}/reject', [TravelExpenseController::class, 'rejectExpenseClaim'])
            ->middleware('permission:travel.approve');
        Route::post('/expense-claims/{expenseClaim}/mark-paid', [TravelExpenseController::class, 'markExpensePaid'])
            ->middleware('permission:travel.manage');
        
        Route::post('/advance-requests/{advanceRequest}/approve', [TravelExpenseController::class, 'approveAdvanceRequest'])
            ->middleware('permission:travel.approve');
        Route::post('/advance-requests/{advanceRequest}/reject', [TravelExpenseController::class, 'rejectAdvanceRequest'])
            ->middleware('permission:travel.approve');
        Route::post('/advance-requests/{advanceRequest}/mark-paid', [TravelExpenseController::class, 'markAdvancePaid'])
            ->middleware('permission:travel.manage');
        Route::post('/advance-requests/{advanceRequest}/settle', [TravelExpenseController::class, 'settleAdvance'])
            ->middleware('permission:travel.manage');
        
        Route::post('/mileage-claims/{mileageClaim}/approve', [TravelExpenseController::class, 'approveMileageClaim'])
            ->middleware('permission:travel.approve');
        Route::post('/mileage-claims/{mileageClaim}/reject', [TravelExpenseController::class, 'rejectMileageClaim'])
            ->middleware('permission:travel.approve');
        Route::post('/mileage-claims/{mileageClaim}/mark-paid', [TravelExpenseController::class, 'markMileagePaid'])
            ->middleware('permission:travel.manage');
    });

    // Loans - Permission-based access
    Route::prefix('loans')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->middleware('permission:loans.view');
        Route::post('/', [LoanController::class, 'store'])->middleware('permission:loans.create');
        Route::get('/{loan}', [LoanController::class, 'show'])->middleware('permission:loans.view');
        Route::put('/{loan}', [LoanController::class, 'update'])->middleware('permission:loans.update');
        Route::delete('/{loan}', [LoanController::class, 'destroy'])->middleware('permission:loans.delete');
        
        Route::post('/{loan}/approve', [LoanController::class, 'approve'])->middleware('permission:loans.approve');
        Route::post('/{loan}/reject', [LoanController::class, 'reject'])->middleware('permission:loans.approve');
        Route::post('/{loan}/disburse', [LoanController::class, 'disburse'])->middleware('permission:loans.manage');
        Route::post('/{loan}/payments', [LoanController::class, 'addPayment'])->middleware('permission:loans.manage');
    });

    // Salary Advances - Permission-based access
    Route::prefix('salary-advances')->group(function () {
        Route::get('/', [SalaryAdvanceController::class, 'index'])->middleware('permission:salary-advance.view');
        Route::post('/', [SalaryAdvanceController::class, 'store'])->middleware('permission:salary-advance.create');
        Route::get('/{salaryAdvance}', [SalaryAdvanceController::class, 'show'])->middleware('permission:salary-advance.view');
        Route::put('/{salaryAdvance}', [SalaryAdvanceController::class, 'update'])->middleware('permission:salary-advance.update');
        Route::delete('/{salaryAdvance}', [SalaryAdvanceController::class, 'destroy'])->middleware('permission:salary-advance.delete');
        
        Route::post('/{salaryAdvance}/approve', [SalaryAdvanceController::class, 'approve'])->middleware('permission:salary-advance.approve');
        Route::post('/{salaryAdvance}/reject', [SalaryAdvanceController::class, 'reject'])->middleware('permission:salary-advance.approve');
        Route::post('/{salaryAdvance}/disburse', [SalaryAdvanceController::class, 'disburse'])->middleware('permission:salary-advance.approve');
    });

    // Salary Components & Employee Salaries - Permission-based access
    Route::prefix('salary-components')->group(function () {
        Route::get('/', [SalaryComponentController::class, 'index'])->middleware('permission:salary-components.view');
        
        // Master Salary Components CRUD
        Route::post('/', [SalaryComponentController::class, 'store'])->middleware('permission:salary-components.create');
        Route::put('/{id}', [SalaryComponentController::class, 'updateMaster'])->middleware('permission:salary-components.update');
        Route::delete('/{id}', [SalaryComponentController::class, 'destroyMaster'])->middleware('permission:salary-components.delete');
        
        // Employee Salary Management
        Route::get('/employees/{employeeId}', [SalaryComponentController::class, 'getEmployeeSalary'])
            ->middleware('permission:salary-components.view');
        Route::post('/employees/{employeeId}', [SalaryComponentController::class, 'storeEmployeeSalary'])
            ->middleware('permission:salary-components.manage');
        Route::put('/components/{componentId}', [SalaryComponentController::class, 'updateComponent'])
            ->middleware('permission:salary-components.update');
        Route::delete('/components/{componentId}', [SalaryComponentController::class, 'deleteComponent'])
            ->middleware('permission:salary-components.delete');
        Route::get('/employees/{employeeId}/increment-history', [SalaryComponentController::class, 'getIncrementHistory'])
            ->middleware('permission:salary-components.view');
        Route::post('/employees/{employeeId}/apply-increment', [SalaryComponentController::class, 'applyIncrement'])
            ->middleware('permission:salary-components.manage');
    });

    // CV Bank - Permission-based access
    Route::prefix('cvs')->group(function () {
        Route::get('/', [CvBankController::class, 'index'])->middleware('permission:cv-bank.view');
        Route::post('/', [CvBankController::class, 'store'])->middleware('permission:cv-bank.create');
        Route::get('/{cv}', [CvBankController::class, 'show'])->middleware('permission:cv-bank.view');
        Route::post('/{cv}', [CvBankController::class, 'update'])->middleware('permission:cv-bank.update'); // POST for file upload support
        Route::delete('/{cv}', [CvBankController::class, 'destroy'])->middleware('permission:cv-bank.delete');
        Route::get('/{cv}/download', [CvBankController::class, 'download'])->middleware('permission:cv-bank.view');
        
        Route::get('/employees/{employeeId}/history', [CvBankController::class, 'getEmployeeCvHistory'])
            ->middleware('permission:cv-bank.view');
    });

    // Deployments - Permission-based access
    Route::prefix('deployments')->group(function () {
        Route::get('/', [DeploymentController::class, 'index'])->middleware('permission:deployments.view');
        Route::get('/{deployment}', [DeploymentController::class, 'show'])->middleware('permission:deployments.view');
        Route::get('/employees/{employeeId}/history', [DeploymentController::class, 'getEmployeeDeploymentHistory'])
            ->middleware('permission:deployments.view');
        
        Route::post('/', [DeploymentController::class, 'store'])->middleware('permission:deployments.create');
        Route::put('/{deployment}', [DeploymentController::class, 'update'])->middleware('permission:deployments.update');
        Route::delete('/{deployment}', [DeploymentController::class, 'destroy'])->middleware('permission:deployments.delete');
        Route::post('/{deployment}/approve', [DeploymentController::class, 'approve'])->middleware('permission:deployments.approve');
        Route::post('/{deployment}/activate', [DeploymentController::class, 'activate'])->middleware('permission:deployments.approve');
        Route::post('/{deployment}/complete', [DeploymentController::class, 'complete'])->middleware('permission:deployments.manage');
        Route::post('/{deployment}/extend', [DeploymentController::class, 'extend'])->middleware('permission:deployments.update');
        Route::post('/extensions/{extension}/approve', [DeploymentController::class, 'approveExtension'])
            ->middleware('permission:deployments.approve');
    });

    // Shifts Management - Permission-based access
    Route::get('/shifts/statistics', [ShiftController::class, 'statistics'])->middleware('permission:shifts.view');
    Route::get('/shifts/active', [ShiftController::class, 'active'])->middleware('permission:shifts.view');
    Route::post('/shifts/{shift}/toggle-status', [ShiftController::class, 'toggleStatus'])->middleware('permission:shifts.manage');
    
    // Employee Shift Assignment (Universal SOPs)
    Route::get('/shifts/{shift}/assignments', [ShiftController::class, 'getAssignedEmployees'])->middleware('permission:shifts.view');
    Route::post('/shifts/{shift}/assignments', [ShiftController::class, 'assignEmployee'])->middleware('permission:shifts.assign');
    Route::post('/shifts/{shift}/assignments/bulk', [ShiftController::class, 'bulkAssignEmployees'])->middleware('permission:shifts.assign');
    Route::delete('/shifts/{shift}/assignments/{assignment}', [ShiftController::class, 'removeEmployeeAssignment'])
        ->middleware('permission:shifts.assign');
    Route::put('/shifts/{shift}/assignments/{assignment}', [ShiftController::class, 'updateAssignment'])
        ->middleware('permission:shifts.assign');
    Route::get('/shifts/{shift}/available-employees', [ShiftController::class, 'getAvailableEmployees'])->middleware('permission:shifts.view');
    Route::get('/shifts/{shift}/assignment-history', [ShiftController::class, 'getAssignmentHistory'])->middleware('permission:shifts.view');
    
    // Shift CRUD
    Route::get('/shifts', [ShiftController::class, 'index'])->middleware('permission:shifts.view');
    Route::post('/shifts', [ShiftController::class, 'store'])->middleware('permission:shifts.create');
    Route::get('/shifts/{shift}', [ShiftController::class, 'show'])->middleware('permission:shifts.view');
    Route::put('/shifts/{shift}', [ShiftController::class, 'update'])->middleware('permission:shifts.update');
    Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->middleware('permission:shifts.delete');

    // Shift Scheduling - Permission-based access
    Route::prefix('shift-scheduling')->group(function () {
        Route::get('/rosters', [ShiftSchedulingController::class, 'getRosters'])->middleware('permission:shifts.view');
        Route::post('/rosters', [ShiftSchedulingController::class, 'storeRoster'])->middleware('permission:shifts.create');
        Route::get('/rosters/{roster}', [ShiftSchedulingController::class, 'getRoster'])->middleware('permission:shifts.view');
        Route::put('/rosters/{roster}', [ShiftSchedulingController::class, 'updateRoster'])->middleware('permission:shifts.update');
        Route::post('/rosters/{roster}/publish', [ShiftSchedulingController::class, 'publishRoster'])->middleware('permission:shifts.manage');
        
        Route::get('/assignments', [ShiftSchedulingController::class, 'getAssignments'])->middleware('permission:shifts.view');
        Route::post('/assignments', [ShiftSchedulingController::class, 'storeAssignment'])->middleware('permission:shifts.assign');
        Route::post('/assignments/bulk', [ShiftSchedulingController::class, 'bulkAssignShifts'])->middleware('permission:shifts.assign');
        Route::put('/assignments/{assignment}', [ShiftSchedulingController::class, 'updateAssignment'])->middleware('permission:shifts.assign');
        Route::delete('/assignments/{assignment}', [ShiftSchedulingController::class, 'deleteAssignment'])->middleware('permission:shifts.assign');
        
        Route::get('/swap-requests', [ShiftSchedulingController::class, 'getSwapRequests'])->middleware('permission:shifts.view');
        Route::post('/swap-requests/{swapRequest}/approve', [ShiftSchedulingController::class, 'approveShiftSwap'])
            ->middleware('permission:shifts.manage');
        Route::post('/swap-requests/{swapRequest}/decline', [ShiftSchedulingController::class, 'declineShiftSwap'])
            ->middleware('permission:shifts.manage');
    });

    Route::post('/shift-scheduling/swap-requests', [ShiftSchedulingController::class, 'requestShiftSwap']);
    Route::post('/shift-scheduling/swap-requests/{swapRequest}/respond', [ShiftSchedulingController::class, 'respondToSwapRequest']);

    // Helpdesk - Permission-based access
    Route::prefix('helpdesk')->group(function () {
        Route::get('/statistics', [HelpdeskController::class, 'getStatistics'])->middleware('permission:helpdesk.view');
        Route::get('/categories', [HelpdeskController::class, 'getCategories'])->middleware('permission:helpdesk.view');
        Route::get('/tickets', [HelpdeskController::class, 'getTickets'])->middleware('permission:helpdesk.view');
        Route::post('/tickets', [HelpdeskController::class, 'storeTicket'])->middleware('permission:helpdesk.create');
        Route::get('/tickets/{ticket}', [HelpdeskController::class, 'getTicket'])->middleware('permission:helpdesk.view');
        Route::put('/tickets/{ticket}', [HelpdeskController::class, 'updateTicket'])->middleware('permission:helpdesk.update');
        Route::delete('/tickets/{ticket}', [HelpdeskController::class, 'deleteTicket'])->middleware('permission:helpdesk.delete');
        Route::post('/tickets/{ticket}/reopen', [HelpdeskController::class, 'reopenTicket'])->middleware('permission:helpdesk.update');
        Route::post('/tickets/{ticket}/rate', [HelpdeskController::class, 'rateTicket'])->middleware('permission:helpdesk.create');
        Route::post('/tickets/{ticket}/replies', [HelpdeskController::class, 'addReply'])->middleware('permission:helpdesk.create');
        Route::get('/tickets/{ticket}/replies', [HelpdeskController::class, 'getReplies'])->middleware('permission:helpdesk.view');
        
        Route::post('/categories', [HelpdeskController::class, 'storeCategory'])->middleware('permission:helpdesk.manage');
        Route::post('/tickets/{ticket}/assign', [HelpdeskController::class, 'assignTicket'])->middleware('permission:helpdesk.manage');
        Route::post('/tickets/{ticket}/resolve', [HelpdeskController::class, 'resolveTicket'])->middleware('permission:helpdesk.manage');
        Route::post('/tickets/{ticket}/close', [HelpdeskController::class, 'closeTicket'])->middleware('permission:helpdesk.manage');
    });

    // File Management - Permission-based access
    Route::prefix('files')->group(function () {
        Route::get('/categories', [FileManagementController::class, 'getCategories'])->middleware('permission:files.view');
        Route::get('/', [FileManagementController::class, 'getFiles'])->middleware('permission:files.view');
        Route::post('/', [FileManagementController::class, 'storeFile'])->middleware('permission:files.create');
        Route::get('/{file}', [FileManagementController::class, 'getFile'])->middleware('permission:files.view');
        Route::get('/{file}/download', [FileManagementController::class, 'downloadFile'])->middleware('permission:files.view');
        Route::put('/{file}', [FileManagementController::class, 'updateFile'])->middleware('permission:files.update');
        Route::delete('/{file}', [FileManagementController::class, 'deleteFile'])->middleware('permission:files.delete');
        Route::post('/{file}/new-version', [FileManagementController::class, 'uploadNewVersion'])->middleware('permission:files.update');
        Route::get('/{file}/access-logs', [FileManagementController::class, 'getAccessLogs'])->middleware('permission:files.view');
        
        Route::post('/categories', [FileManagementController::class, 'storeCategory'])->middleware('permission:files.manage');
    });

    // Calendar - Permission-based access
    Route::prefix('calendar')->group(function () {
        Route::get('/events', [CalendarController::class, 'getEvents'])->middleware('permission:calendar.view');
        Route::post('/events', [CalendarController::class, 'storeEvent'])->middleware('permission:calendar.create');
        Route::get('/events/{event}', [CalendarController::class, 'getEvent'])->middleware('permission:calendar.view');
        Route::put('/events/{event}', [CalendarController::class, 'updateEvent'])->middleware('permission:calendar.update');
        Route::delete('/events/{event}', [CalendarController::class, 'deleteEvent'])->middleware('permission:calendar.delete');
        Route::post('/events/{event}/respond', [CalendarController::class, 'respondToEvent']);
        Route::post('/events/{event}/attendees', [CalendarController::class, 'addAttendees'])->middleware('permission:calendar.update');
        Route::delete('/events/{event}/attendees/{attendee}', [CalendarController::class, 'removeAttendee'])
            ->middleware('permission:calendar.update');
        Route::post('/events/{event}/reminders', [CalendarController::class, 'addReminder'])->middleware('permission:calendar.create');
        Route::get('/my-events', [CalendarController::class, 'getMyEvents']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'getNotifications']);
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'deleteNotification']);
        Route::delete('/clear-all', [NotificationController::class, 'clearAll']);
        Route::get('/preferences', [NotificationController::class, 'getPreferences']);
        Route::post('/preferences', [NotificationController::class, 'updatePreferences']);
    });

    // Organization Chart & Directory - Permission-based access
    Route::prefix('organization')->group(function () {
        Route::get('/chart', [OrganizationController::class, 'getOrganizationChart'])->middleware('permission:organization.view');
        Route::get('/hierarchy', [OrganizationController::class, 'getHierarchy'])->middleware('permission:organization.view');
        Route::get('/department-stats', [OrganizationController::class, 'getDepartmentStats'])->middleware('permission:organization.view');
        Route::get('/directory', [OrganizationController::class, 'getEmployeeDirectory'])->middleware('permission:organization.view');
        Route::get('/team/{manager}', [OrganizationController::class, 'getTeamMembers'])->middleware('permission:organization.view');
    });

    // Roles & Permissions Management (Super Admin only)
    Route::middleware('super_admin')->prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{role}', [RoleController::class, 'show']);
        Route::put('/{role}', [RoleController::class, 'update']);
        Route::delete('/{role}', [RoleController::class, 'destroy']);
        
        // Role Permission Management
        Route::post('/{role}/permissions/sync', [RoleController::class, 'syncPermissions']);
        Route::post('/{role}/permissions/grant', [RoleController::class, 'grantPermission']);
        Route::post('/{role}/permissions/revoke', [RoleController::class, 'revokePermission']);
    });

    Route::middleware('super_admin')->prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::get('/modules', [PermissionController::class, 'modules']);
        Route::get('/actions', [PermissionController::class, 'actions']);
        Route::get('/{permission}', [PermissionController::class, 'show']);
        Route::put('/{permission}', [PermissionController::class, 'update']);
        Route::delete('/{permission}', [PermissionController::class, 'destroy']);
    });

    // User Role Assignment (Super Admin only)
    Route::middleware('super_admin')->prefix('users')->group(function () {
        Route::post('/{user}/assign-role', [UserRoleController::class, 'assignRole']);
        Route::delete('/{user}/remove-role', [UserRoleController::class, 'removeRole']);
        Route::post('/{user}/grant-permission', [UserRoleController::class, 'grantPermission']);
        Route::post('/{user}/revoke-permission', [UserRoleController::class, 'revokePermission']);
        Route::get('/{user}/permissions', [UserRoleController::class, 'permissions']);
    });

    // Current User's Permissions (Any authenticated user)
    Route::get('/my-permissions', [UserRoleController::class, 'myPermissions']);
    Route::post('/check-permission', [UserRoleController::class, 'checkPermission']);
});
