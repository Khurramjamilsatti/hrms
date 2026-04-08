import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { usePermissionStore } from '@/stores/permission';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/',
    component: () => import('@/layouts/DashboardLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { module: 'dashboard' }
      },
      {
        path: 'employees',
        name: 'Employees',
        component: () => import('@/views/employees/EmployeeList.vue'),
        meta: { module: 'employees' }
      },
      {
        path: 'employees/create',
        name: 'EmployeeCreate',
        component: () => import('@/views/employees/EmployeeForm.vue'),
        meta: { module: 'employees' }
      },
      {
        path: 'employees/:id',
        name: 'EmployeeDetails',
        component: () => import('@/views/employees/EmployeeDetails.vue'),
        meta: { module: 'employees' }
      },
      {
        path: 'employees/:id/edit',
        name: 'EmployeeEdit',
        component: () => import('@/views/employees/EmployeeForm.vue'),
        meta: { module: 'employees' }
      },
      {
        path: 'attendance',
        name: 'Attendance',
        component: () => import('@/views/attendance/AttendanceList.vue'),
        meta: { module: 'attendance' }
      },
      {
        path: 'leaves',
        name: 'Leaves',
        component: () => import('@/views/leaves/LeaveList.vue'),
        meta: { module: 'leaves' }
      },
      {
        path: 'payroll',
        name: 'Payroll',
        component: () => import('@/views/payroll/PayrollList.vue'),
        meta: { module: 'payroll' }
      },
      // Salary Components Master
      {
        path: 'salary-components',
        name: 'SalaryComponents',
        component: () => import('@/views/salary-components/SalaryComponentsList.vue'),
        meta: { module: 'salary_components' }
      },
      // Loan Management
      {
        path: 'loans',
        name: 'Loans',
        component: () => import('@/views/loans/LoanList.vue'),
        meta: { module: 'loans' }
      },
      {
        path: 'loans/:id',
        name: 'LoanDetails',
        component: () => import('@/views/loans/LoanDetails.vue'),
        meta: { module: 'loans' }
      },
      // Salary Advance Management
      {
        path: 'salary-advances',
        name: 'SalaryAdvances',
        component: () => import('@/views/salary-advances/SalaryAdvanceList.vue'),
        meta: { module: 'salary_advances' }
      },
      // Salary Component Management
      {
        path: 'employees/:id/salary',
        name: 'SalaryComponentManagement',
        component: () => import('@/views/employees/SalaryComponentManagement.vue'),
        meta: { module: 'employees' }
      },
      // CV Bank
      {
        path: 'cvs',
        name: 'CvBank',
        component: () => import('@/views/cvs/CvList.vue'),
        meta: { module: 'cv_bank' }
      },
      {
        path: 'cvs/:id',
        name: 'CvDetails',
        component: () => import('@/views/cvs/CvDetails.vue'),
        meta: { module: 'cv_bank' }
      },
      // Deployments
      {
        path: 'deployments',
        name: 'Deployments',
        component: () => import('@/views/deployments/DeploymentList.vue'),
        meta: { module: 'deployments' }
      },
      {
        path: 'deployments/:id',
        name: 'DeploymentDetails',
        component: () => import('@/views/deployments/DeploymentDetails.vue'),
        meta: { module: 'deployments' }
      },
      {
        path: 'departments',
        name: 'Departments',
        component: () => import('@/views/departments/DepartmentList.vue'),
        meta: { module: 'departments' }
      },
      {
        path: 'profile',
        name: 'Profile',
        component: () => import('@/views/Profile.vue'),
        meta: { module: 'employees' }
      },
      {
        path: 'recruitment',
        name: 'Recruitment',
        component: () => import('@/views/recruitment/RecruitmentList.vue'),
        meta: { module: 'recruitment' }
      },
      {
        path: 'performance',
        name: 'Performance',
        component: () => import('@/views/performance/PerformanceList.vue'),
        meta: { module: 'performance' }
      },
      {
        path: 'assets',
        name: 'Assets',
        component: () => import('@/views/assets/AssetList.vue'),
        meta: { module: 'assets' }
      },
      {
        path: 'announcements',
        name: 'Announcements',
        component: () => import('@/views/announcements/AnnouncementList.vue'),
        meta: { module: 'announcements' }
      },
      // Timesheet Management
      {
        path: 'timesheets',
        name: 'Timesheets',
        component: () => import('@/views/timesheets/TimesheetList.vue'),
        meta: { module: 'timesheets' }
      },
      {
        path: 'timesheets/projects',
        name: 'Projects',
        component: () => import('@/views/timesheets/ProjectList.vue'),
        meta: { module: 'timesheets' }
      },
      // Onboarding
      {
        path: 'onboarding',
        name: 'Onboarding',
        component: () => import('@/views/onboarding/OnboardingList.vue'),
        meta: { module: 'onboarding' }
      },
      {
        path: 'onboarding/templates',
        name: 'OnboardingTemplates',
        component: () => import('@/views/onboarding/TemplateList.vue'),
        meta: { module: 'onboarding' }
      },
      // Training
      {
        path: 'training',
        name: 'Training',
        component: () => import('@/views/training/TrainingList.vue'),
        meta: { module: 'training' }
      },
      {
        path: 'training/courses',
        name: 'TrainingCourses',
        component: () => import('@/views/training/CourseList.vue'),
        meta: { module: 'training' }
      },
      // Travel & Expense
      {
        path: 'travel-expenses',
        name: 'TravelExpenses',
        component: () => import('@/views/travel-expenses/TravelExpenseList.vue'),
        meta: { module: 'travel' }
      },
      {
        path: 'travel-expenses/advances',
        name: 'AdvanceRequests',
        component: () => import('@/views/travel-expenses/AdvanceList.vue'),
        meta: { module: 'travel' }
      },
      // Shift Management
      {
        path: 'shifts',
        name: 'Shifts',
        component: () => import('@/views/shifts/ShiftManagement.vue'),
        meta: { module: 'shifts' }
      },
      {
        path: 'shifts/rosters',
        name: 'ShiftRosters',
        component: () => import('@/views/shifts/RosterList.vue'),
        meta: { module: 'shifts' }
      },
      // Helpdesk
      {
        path: 'helpdesk',
        name: 'Helpdesk',
        component: () => import('@/views/helpdesk/HelpdeskList.vue'),
        meta: { module: 'helpdesk' }
      },
      // Files
      {
        path: 'files',
        name: 'Files',
        component: () => import('@/views/files/FileList.vue'),
        meta: { module: 'files' }
      },
      // Calendar
      {
        path: 'calendar',
        name: 'Calendar',
        component: () => import('@/views/calendar/CalendarView.vue'),
        meta: { module: 'calendar' }
      },
      // Organization
      {
        path: 'organization',
        name: 'Organization',
        component: () => import('@/views/organization/OrgChart.vue'),
        meta: { module: 'organization' }
      },
      // Roles & Permissions Management (Super Admin only)
      {
        path: 'admin/roles',
        name: 'Roles',
        component: () => import('@/views/admin/roles/RoleList.vue'),
        meta: { module: 'roles' }
      },
      {
        path: 'admin/user-roles',
        name: 'UserRoles',
        component: () => import('@/views/admin/roles/UserRoleManagement.vue'),
        meta: { module: 'users' }
      },
    ]
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  const permissionStore = usePermissionStore();
  const isAuthenticated = authStore.isAuthenticated;

  // Handle guest routes (like login)
  if (to.meta.guest && isAuthenticated) {
    return next('/');
  }

  // Handle protected routes
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next('/login');
  }

  // If authenticated and permissions not loaded yet, fetch them
  if (isAuthenticated && !permissionStore.loaded) {
    try {
      await permissionStore.fetchMyPermissions();
    } catch (error) {
      console.error('Failed to fetch permissions:', error);
      // If permission fetch fails, logout and redirect to login
      authStore.logout();
      return next('/login');
    }
  }

  // Check module access permission
  if (to.meta.module && isAuthenticated) {
    const module = to.meta.module;
    
    // Dashboard and profile are always accessible
    if (module === 'dashboard' || (module === 'employees' && to.name === 'Profile')) {
      return next();
    }

    // Check if user has access to this module
    if (!permissionStore.canAccessModule(module)) {
      // User doesn't have permission, redirect to dashboard with notification
      console.warn(`Access denied to module: ${module}`);
      return next({ 
        name: 'Dashboard',
        replace: true,
        query: { 
          denied: module,
          message: 'You do not have permission to access this module'
        }
      });
    }
  }

  next();
});

export default router;
