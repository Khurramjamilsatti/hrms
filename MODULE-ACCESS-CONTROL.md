# Module Access Control Implementation

## Overview
Complete implementation of permission-based module access control with both **menu filtering** and **URL access blocking**.

## Implementation Date
January 2025

## Features Implemented

### 1. Menu Filtering ✅
- Menu items are filtered based on user permissions
- Only modules the user has access to are displayed in the navigation
- Dashboard and Profile are always accessible
- Super Admin sees all menu items including admin-only sections

**Location**: `resources/js/layouts/DashboardLayout.vue` (lines 296-350)

### 2. URL Access Control ✅
- Router guards check permissions before allowing navigation
- Users are redirected to dashboard if they try to access unauthorized routes
- Error notification is displayed when access is denied
- Permissions are automatically loaded on authentication

**Location**: `resources/js/router/index.js` (lines 247-305)

## How It Works

### Backend Permission Structure
```
80 permissions with pattern: {module}.{action}
Examples:
- employees.view
- employees.create
- leaves.apply
- leaves.approve
- payroll.view
- payroll.process
```

### Frontend Implementation

#### 1. Permission Store (Pinia)
```javascript
// State
permissions: []           // Array of permission objects
allowedModules: []        // List of modules user can access
isSuperAdmin: false      // Super admin flag

// Getters
canAccessModule(module)   // Check if user can access a module
hasPermission(slug)       // Check specific permission
```

#### 2. Router Guard
```javascript
router.beforeEach(async (to, from, next) => {
  // Load permissions if not loaded
  if (isAuthenticated && permissions.length === 0) {
    await fetchMyPermissions();
  }
  
  // Check module access
  if (to.meta.module && !canAccessModule(to.meta.module)) {
    // Redirect to dashboard with error
    next({ name: 'Dashboard', query: { denied: module } });
  }
});
```

#### 3. Dashboard Notification
```javascript
// Watch for access denied query param
watch(() => route.query.denied, (deniedModule) => {
  if (deniedModule) {
    showError('You do not have permission to access this module');
  }
});
```

## Route Module Mapping

All routes now have `meta.module` field:

| Route | Module | Description |
|-------|--------|-------------|
| `/` | `dashboard` | Dashboard (Always Accessible) |
| `/profile` | `employees` | User Profile (Always Accessible) |
| `/employees` | `employees` | Employee Management |
| `/attendance` | `attendance` | Attendance Tracking |
| `/leaves` | `leaves` | Leave Management |
| `/payroll` | `payroll` | Payroll Processing |
| `/salary-components` | `salary_components` | Salary Components Master |
| `/loans` | `loans` | Loan Management |
| `/salary-advances` | `salary_advances` | Salary Advance Requests |
| `/cvs` | `cv_bank` | CV Bank |
| `/deployments` | `deployments` | Employee Deployments |
| `/departments` | `departments` | Department Management |
| `/recruitment` | `recruitment` | Recruitment |
| `/performance` | `performance` | Performance Management |
| `/assets` | `assets` | Asset Management |
| `/announcements` | `announcements` | Announcements |
| `/timesheets` | `timesheets` | Timesheet Management |
| `/onboarding` | `onboarding` | Onboarding |
| `/training` | `training` | Training & Development |
| `/travel-expenses` | `travel` | Travel & Expenses |
| `/shifts` | `shifts` | Shift Management |
| `/helpdesk` | `helpdesk` | Helpdesk |
| `/files` | `files` | Files |
| `/calendar` | `calendar` | Calendar |
| `/organization` | `organization` | Organization Chart |
| `/admin/roles` | `roles` | Roles & Permissions (Super Admin) |
| `/admin/user-roles` | `users` | User Role Management (Super Admin) |

## Access Control Flow

```
User tries to access /payroll
       ↓
Router Guard Intercepts
       ↓
Check if authenticated → NO → Redirect to /login
       ↓ YES
Check if permissions loaded → NO → Fetch permissions
       ↓ YES
Check route.meta.module → payroll
       ↓
Check permissionStore.canAccessModule('payroll')
       ↓
NO → Redirect to /dashboard?denied=payroll&message=...
       ↓
Dashboard displays error notification
       
YES → Allow navigation
       ↓
Component loads normally
```

## Testing Guide

### Test Scenario 1: Regular Employee
1. Login as employee (employee@hrms.com)
2. Try to access `/payroll` directly
3. **Expected**: Redirect to dashboard with error message
4. **Verify**: Payroll menu item not visible in sidebar

### Test Scenario 2: Section Head
1. Login as section head (manager@hrms.com)
2. Try to access `/admin/roles` directly
3. **Expected**: Redirect to dashboard with error message
4. **Verify**: Admin menu items not visible

### Test Scenario 3: Super Admin
1. Login as super admin (admin@hrms.com)
2. Access any route directly
3. **Expected**: All routes accessible
4. **Verify**: All menu items visible including admin section

### Test Scenario 4: Permission Changes
1. Super admin removes "payroll.view" from a user
2. User refreshes page
3. **Expected**: Payroll menu item disappears
4. Try accessing `/payroll` directly
5. **Expected**: Blocked with error message

## Code Locations

### Router Configuration
- **File**: `resources/js/router/index.js`
- **Lines**: 1-305
- **Key Changes**:
  - Added `usePermissionStore` import
  - Added `meta.module` to all routes
  - Replaced role-based guard with permission-based guard
  - Added async permission loading
  - Added access denied redirect with notification

### Dashboard Notification Handler
- **File**: `resources/js/views/Dashboard.vue`
- **Lines**: 804-920 (script section)
- **Key Changes**:
  - Added `useRoute` and `watch` imports
  - Added `useNotification` composable
  - Added watcher for `route.query.denied`
  - Shows error notification on access denial

### Menu Filtering (Already Implemented)
- **File**: `resources/js/layouts/DashboardLayout.vue`
- **Lines**: 296-350
- **Functionality**:
  - Filters menu items by `permissionStore.canAccessModule()`
  - Dashboard and profile always visible
  - Super admin sees all items

### Permission Store
- **File**: `resources/js/stores/permission.js`
- **Lines**: 1-100
- **Key Methods**:
  - `fetchMyPermissions()` - Load user permissions
  - `canAccessModule(module)` - Check module access
  - `hasPermission(slug)` - Check specific permission

## API Endpoints Used

### Get My Permissions
```
GET /api/my-permissions
Response:
{
  "data": {
    "permissions": [...],
    "grouped_permissions": {...},
    "allowed_modules": ["employees", "attendance", "leaves"],
    "role": "section_head",
    "is_super_admin": false
  }
}
```

### Check Permission
```
POST /api/check-permission
Body: { "permission": "payroll.view" }
Response: { "data": { "has_permission": false } }
```

## Benefits

### Security
- **Prevent URL Hacking**: Users cannot access unauthorized routes by typing URLs
- **Backend Protection**: Backend API already has permission middleware
- **Double Layer**: Frontend + Backend protection

### User Experience
- **Clean UI**: Users only see what they can access
- **Clear Feedback**: Error notifications explain why access was denied
- **No Confusion**: No broken links or unauthorized screens

### Maintainability
- **Centralized Logic**: All access control in router guard
- **Easy Updates**: Add module to route metadata only
- **Consistent**: Same permission system across backend and frontend

## Future Enhancements

1. **Custom Access Denied Page**
   - Create dedicated 403 page with helpful information
   - Show available modules and contact information

2. **Permission Caching**
   - Cache permissions in localStorage with TTL
   - Reduce API calls on page refresh

3. **Granular Action Permissions**
   - Check specific actions (view, create, edit, delete)
   - Hide buttons/links based on action permissions

4. **Permission Logging**
   - Log access denied attempts for security monitoring
   - Track which modules users try to access

5. **Dynamic Permission Refresh**
   - WebSocket/polling for real-time permission updates
   - Notify users when permissions change

## Troubleshooting

### Issue: User stuck at login, infinite redirect
**Cause**: Permission fetch failing
**Solution**: Check `/api/my-permissions` endpoint, verify user has valid role

### Issue: Menu items not updating after permission change
**Cause**: Permissions cached in store
**Solution**: Call `permissionStore.fetchMyPermissions()` or logout/login

### Issue: Dashboard query params visible in URL
**Cause**: Route replace not working
**Solution**: Ensure Vue Router instance accessible in watcher

### Issue: Super admin can't access admin routes
**Cause**: Module not in `allowed_modules` array
**Solution**: Backend should return all modules for super admin

## Related Documentation
- [RBAC Implementation](./RBAC-IMPLEMENTATION.md)
- [API Documentation](./API.md)
- [Frontend Complete Guide](./FRONTEND-COMPLETE.md)
- [Setup Complete](./SETUP-COMPLETE.md)

## Summary
✅ **Menu Filtering**: Implemented and working
✅ **URL Access Control**: Implemented with router guards
✅ **Error Notifications**: Displayed on access denial
✅ **Permission Loading**: Automatic on authentication
✅ **Module Mapping**: All 25+ routes configured
✅ **Testing**: Multiple scenarios covered

**Status**: COMPLETE AND OPERATIONAL
