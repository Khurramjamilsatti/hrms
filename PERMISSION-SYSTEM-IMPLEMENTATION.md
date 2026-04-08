# Permission System Implementation

## Overview
This document outlines the complete action-level permission system applied across the entire HRMS application.

## Permission Structure
All permissions follow the pattern: `{module}.{action}`

### Actions
- `view` - View/List records
- `create` - Create new records
- `update` - Edit existing records
- `delete` - Delete records
- `approve` - Approve requests/applications
- `manage` - Full management capabilities (admin-level)
- `assign` - Assign resources (shifts, assets, etc.)

## Module Permissions

### 1. Employees
- `employees.view` - View employee list and details
- `employees.create` - Create new employees
- `employees.update` - Update employee information  
- `employees.delete` - Delete employees
- `employees.manage` - Full employee management

### 2. Departments
- `departments.view` - View departments
- `departments.create` - Create departments
- `departments.update` - Update departments
- `departments.delete` - Delete departments

### 3. Attend ance
- `attendance.view` - View attendance records
- `attendance.create` - Check-in/check-out
- `attendance.update` - Update attendance records
- `attendance.delete` - Delete attendance records
- `attendance.manage` - Manage all attendance

### 4. Leave Management
- `leave.view` - View leave applications
- `leave.create` - Apply for leave
- `leave.update` - Update leave applications
- `leave.delete` - Cancel leave applications
- `leave.approve` - Approve/reject leave

### 5. Payroll
- `payroll.view` - View payroll records
- `payroll.create` - Generate payroll
- `payroll.process` - Process payroll
- `payroll.manage` - Full payroll management

### 6. Assets
- `assets.view` - View assets
- `assets.create` - Add new assets
- `assets.update` - Update asset details
- `assets.delete` - Remove assets
- `assets.assign` - Assign/return assets

### 7. Loans
- `loans.view` - View loan requests
- `loans.create` - Request loans
- `loans.update` - Update loan details
- `loans.delete` - Cancel loan requests
- `loans.approve` - Approve/reject loans
- `loans.manage` - Manage loan payments

### 8. Salary Advances
- `salary-advance.view` - View advance requests
- `salary-advance.create` - Request salary advance
- `salary-advance.update` - Update advance requests
- `salary-advance.delete` - Cancel requests
- `salary-advance.approve` - Approve/reject advances

### 9. Salary Components
- `salary-components.view` - View salary structures
- `salary-components.create` - Create salary components
- `salary-components.update` - Update salary structures
- `salary-components.delete` - Remove components
- `salary-components.manage` - Full salary management

### 10. Shifts
- `shifts.view` - View shift schedules
- `shifts.create` - Create shifts
- `shifts.update` - Update shifts
- `shifts.delete` - Remove shifts
- `shifts.assign` - Assign employees to shifts
- `shifts.manage` - Full shift management

### 11. Recruitment
- `recruitment.view` - View positions and applications
- `recruitment.create` - Create positions/applications
- `recruitment.update` - Update recruitment records
- `recruitment.delete` - Remove records
- `recruitment.manage` - Manage hiring process

### 12. Performance
- `performance.view` - View reviews and goals
- `performance.create` - Create reviews/goals
- `performance.update` - Update performance records
- `performance.manage` - Full performance management

### 13. Training
- `training.view` - View courses and enrollments
- `training.create` - Create courses/sessions
- `training.update` - Update training records
- `training.delete` - Remove training records
- `training.manage` - Manage certifications

### 14. Travel & Expenses
- `travel.view` - View travel requests and expenses
- `travel.create` - Create travel/expense requests
- `travel.update` - Update requests
- `travel.approve` - Approve/reject requests
- `travel.manage` - Process payments

### 15. Timesheets
- `timesheets.view` - View timesheets
- `timesheets.create` - Create timesheets
- `timesheets.update` - Update timesheets
- `timesheets.approve` - Approve timesheets

### 16. Onboarding
- `onboarding.view` - View onboarding records
- `onboarding.create` - Create onboarding plans
- `onboarding.update` - Update onboarding
- `onboarding.manage` - Manage templates

### 17. Helpdesk
- `helpdesk.view` - View tickets
- `helpdesk.create` - Create tickets
- `helpdesk.update` - Update tickets
- `helpdesk.delete` - Delete tickets
- `helpdesk.manage` - Assign and resolve tickets

### 18. File Management
- `files.view` - View files
- `files.create` - Upload files
- `files.update` - Update file details
- `files.delete` - Delete files

### 19. Calendar
- `calendar.view` - View events
- `calendar.create` - Create events
- `calendar.update` - Update events
- `calendar.delete` - Remove events

### 20. Announcements
- `announcements.view` - View announcements
- `announcements.create` - Create announcements
- `announcements.update` - Update announcements
- `announcements.delete` - Delete announcements

### 21. Deployments
- `deployments.view` - View deployments
- `deployments.create` - Create deployments
- `deployments.update` - Update deployments
- `deployments.delete` - Remove deployments
- `deployments.approve` - Approve deployments

### 22. CV Bank
- `cv-bank.view` - View CVs
- `cv-bank.create` - Upload CVs
- `cv-bank.update` - Update CV records
- `cv-bank.delete` - Remove CVs

### 23. Organization
- `organization.view` - View org chart and directory

### 24. Notifications
- All authenticated users can manage their own notifications

## Frontend Permission Checks

### Using the Permission Store in Components

```javascript
import { usePermissionStore } from '@/stores/permission'

const permissionStore = usePermissionStore()

// Check single permission
if (permissionStore.hasPermission('employees.create')) {
  // Show create button
}

// Check multiple permissions (OR)
if (permissionStore.hasAnyPermission(['employees.create', 'employees.update'])) {
  // Show action buttons
}

// Check if user can access module
if (permissionStore.canAccessModule('employees')) {
  // Show module in navigation
}

// Check if super admin
if (permissionStore.isSuperAdmin) {
  // Full access to everything
}
```

### Navigation Menu Filtering

The navigation menu automatically filters based on user permissions using `permissionStore.canAccessModule(module)`.

### Button/Action Visibility

```vue
<template>
  <button 
    v-if="permissionStore.hasPermission('employees.create')"
    @click="openCreateForm"
    class="btn btn-primary">
    Create Employee
  </button>
</template>
```

## Backend Permission Checks

### Using the Permission Middleware

```php
// Single permission
Route::get('/employees', [EmployeeController::class, 'index'])
    ->middleware('permission:employees.view');

// Multiple permissions (OR logic)
Route::post('/employees', [EmployeeController::class, 'store'])
    ->middleware('permission:employees.create,employees.manage');
```

### Controller-Level Checks

```php
public function store(Request $request)
{
    // Manual check if needed
    if (!$request->user()->hasPermission('employees.create')) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }
    
    // ... rest of the method
}
```

## Super Admin Bypass

Users with `role === 'super_admin'` automatically have all permissions and bypass all checks.

## Testing Permissions

### 1. Login as different users
- Super Admin: admin@hrms.com
- Manager: manager@hrms.com
- Employee: employee@hrms.com

### 2. Verify UI elements are properly shown/hidden
- Navigation menu items
- Create/Edit/Delete buttons
- Action buttons

### 3. Test API endpoints
- Should return 403 for unauthorized actions
- Should succeed for authorized actions

### 4. Check permission loading
- Permissions should load on login
- Should be available in `permissionStore`
- Should refresh when roles change

## Troubleshooting

### Permissions not loading
1. Check `/my-permissions` endpoint
2. Verify user has a role assigned
3. Check Laravel logs for errors
4. Clear browser local storage

### Always getting 403
1. Verify permission slug matches exactly
2. Check if role has the permission
3. Verify middleware is applied correctly
4. Check if user role is 'super_admin'

### UI notshowing elements
1. Check if `permissionStore.fetchMyPermissions()` was called
2. Verify permission check logic in component
3. Check browser console for errors
4. Verify permission exists in database

## Implementation Complete

✅ Permission middleware created and registered
✅ User model has permission methods
✅ Permission store created for frontend
✅ All API routes protected with appropriate middleware
✅ Super admin bypass implemented
✅ Frontend permission checks ready to implement
✅ Login issue fixed (removed `/api` prefix from permission store)

## Next Steps

1. ✅ Test login functionality
2. ⏳ Implement frontend permission checks in all Vue components
3. ⏳ Test each module with different user roles
4. ⏳ Document role-permission mappings for deployment
5. ⏳ Create admin interface for managing role-permission assignments
