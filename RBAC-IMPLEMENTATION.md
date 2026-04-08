# Role-Based Access Control (RBAC) Implementation

## Overview
Complete role-based access control has been implemented to restrict data and module access based on user roles. Each role has specific permissions and data visibility.

## Role Hierarchy

```
Level 5: Super Admin (God Mode)
    |
Level 4: HR Admin (Final Approval Authority)
    |
Level 3: Section Head (Department Manager, First-Level Approver)
    |
Level 2: Manager (Team Lead)
    |
Level 1: Employee (Self-Service) / Admin (Legacy)
```

---

## Role Permissions

### 1. Super Admin (`super_admin`)
**Access Level**: Full system access

**Frontend Modules**:
- ✅ ALL modules available
- Full navigation sidebar

**Data Access**:
- Can view/edit ALL employee records
- Can view/manage ALL departments
- Can view ALL attendance records
- Can view ALL leave applications
- Can view/generate ALL payroll
- Can override any approval workflow

**Special Permissions**:
- Organization structure management
- System-wide configuration
- All admin functions

---

### 2. HR Admin (`hr_admin`)
**Access Level**: Full HR management, final approval authority

**Frontend Modules** (All except):
- ❌ Organization (structure editing restricted)
- ✅ All other modules

**Data Access**:
- View/manage ALL employees
- View ALL attendance
- **Final approval** for leave requests
- **Final approval** for attendance corrections
- View/generate payroll
- Access all HR reports

**Approval Workflow**:
- Can give final approval after section head's first approval
- Can reject at final stage
- Cannot override super admin

---

### 3. Section Head (`section_head`)
**Access Level**: Department-specific management, first-level approver

**Frontend Modules**:
- ✅ Dashboard (department stats)
- ✅ Employees (department only)
- ✅ Attendance (department only)
- ✅ Leaves (department only)
- ✅ Timesheets (department)
- ✅ Departments (view info)
- ✅ Training (department)
- ✅ Shifts (department)
- ✅ Helpdesk (department tickets)
- ✅ Files, Calendar, Profile

**Restricted Modules**:
- ❌ Payroll (no access)
- ❌ Salary Components (no access)
- ❌ Loans (no access)
- ❌ Salary Advances (no access)
- ❌ CVs (no access)
- ❌ Deployments (no access)
- ❌ Onboarding (no access)
- ❌ Organization (no access)
- ❌ Travel Expenses (viewing only)

**Data Access**:
- View employees **in their department only**
- View attendance **for department employees**
- View leave requests **from department employees**
- **First-level approval** for leave requests
- **First-level approval** for attendance corrections

**Dashboard Stats**:
- Department name
- Total department employees (count)
- Present today (department)
- Absent today (department)
- On leave today (department)
- Pending leave requests (department)
- Department employee list
- Recent leave applications (department)

**Approval Workflow**:
- Receives leave requests from department employees
- Can approve (moves to HR Admin) or reject
- Cannot give final approval

---

### 4. Manager (`manager`)
**Access Level**: Team-specific viewing, limited management

**Frontend Modules**:
- ✅ Dashboard (team stats)
- ✅ Employees (team only)
- ✅ Attendance (team view)
- ✅ Leaves (team view)
- ✅ Timesheets (team)
- ✅ Training (team)
- ✅ Helpdesk (team tickets)
- ✅ Files, Calendar, Profile

**Restricted Modules**:
- ❌ Payroll (no access)
- ❌ Salary Components (no access)
- ❌ Loans (no access)
- ❌ Salary Advances (no access)
- ❌ Departments (no access)
- ❌ CVs, Deployments, Onboarding (no access)
- ❌ Organization (no access)
- ❌ Shifts (view only)
- ❌ Travel Expenses (view only)

**Data Access**:
- View employees **who report to them only** (manager_id = user.id)
- View attendance **for team members only**
- View leave requests **from team members**
- **Cannot approve** leaves (view only)
- **Cannot approve** attendance (view only)

**Dashboard Stats**:
- Total team members (count)
- Present today (team)
- Absent today (team)
- On leave today (team)
- Pending leave requests (team)
- Team member list
- Recent leave applications (team)

---

### 5. Employee (`employee`)
**Access Level**: Self-service only

**Frontend Modules**:
- ✅ Dashboard (personal stats)
- ✅ My Profile
- ✅ Attendance (own only)
- ✅ Leaves (own only)
- ✅ Loans (own only)
- ✅ Salary Advances (own only)
- ✅ Timesheets (own only)
- ✅ Training (own only)
- ✅ Travel Expenses (own only)
- ✅ Shifts (view own assignments)
- ✅ Helpdesk (own tickets)
- ✅ Files (accessible files)
- ✅ Calendar (company calendar)

**Restricted Modules** (All blocked):
- ❌ Employees (cannot view employee list)
- ❌ Payroll (can only see own payslips)
- ❌ Salary Components (no access)
- ❌ CVs, Deployments (no access)
- ❌ Onboarding (no access)
- ❌ Departments (no access)
- ❌ Organization (no access)

**Data Access**:
- **Own data ONLY** - Cannot view other employees
- Own attendance records
- Own leave applications
- Own payslips
- Own loans and advances
- Own timesheets
- Own training records
- Own travel expenses

**Dashboard Stats**:
- My attendance today (check-in/out, hours)
- Attendance summary (present/absent/leave days this month)
- Total hours worked this month
- Overtime hours this month
- Leave balance (by type)
- Pending leave applications
- Recent payslips (last 3)
- Recent attendance (last 7 days)
- Upcoming approved leaves
- My overtime requests
- Announcements

**Permissions**:
- Apply for leave
- Check in/out attendance
- Request loans/advances
- Submit timesheets
- View own payslips
- Create helpdesk tickets

---

### 6. Admin (`admin`) - Legacy Role
**Access Level**: Full system access (backward compatibility)

**Frontend Modules**: All modules

**Data Access**: Same as super_admin (for backward compatibility)

---

## Controller-Level Access Control

### EmployeeController
```php
- index(): 
  - Employee: 403 Forbidden
  - Manager: Team only (manager_id = user.id)
  - Section Head: Department only
  - HR Admin/Super Admin/Admin: All

- show($employee):
  - Employee: Own profile only
  - Manager: Team member only
  - Section Head: Department employee only
  - HR Admin/Super Admin/Admin: Any employee

- store(), update(), destroy():
  - HR Admin/Super Admin/Admin only
```

### AttendanceController
```php
- index():
  - Employee: Own attendance only
  - Manager: Team attendance
  - Section Head: Department attendance
  - HR Admin/Super Admin/Admin: All attendance

- checkIn(), checkOut():
  - All roles (but only for own employee_id)
```

### LeaveApplicationController
```php
- index():
  - Employee: Own leaves only
  - Manager: Team leaves
  - Section Head: Department leaves
  - HR Admin/Super Admin/Admin: All leaves

- store():
  - All roles (but employees typically for self)

- approve():
  - Section Head: First-level approval (department leaves)
  - HR Admin/Super Admin: Final approval
```

### PayrollController
```php
- index():
  - Employee: Own payroll only
  - Manager: 403 Forbidden
  - Section Head: 403 Forbidden
  - HR Admin/Super Admin/Admin: All payroll

- generateMonthlyPayroll():
  - HR Admin/Super Admin/Admin only

- processPayroll():
  - HR Admin/Super Admin/Admin only
```

### DashboardController
```php
- index():
  - Super Admin/HR Admin/Admin: getAdminDashboard()
  - Section Head: getSectionHeadDashboard()
  - Manager: getManagerDashboard()
  - Employee: getEmployeeDashboard()
```

---

## Frontend Menu Filtering

**Implementation**: `resources/js/layouts/DashboardLayout.vue`

The menuItems computed property filters menu items based on `user.role`:

```javascript
if (userRole === 'super_admin') {
  return items; // All items
}

if (userRole === 'hr_admin') {
  return items.filter(item => !['organization'].includes(item.name));
}

if (userRole === 'section_head') {
  return items.filter(item => [
    'dashboard', 'employees', 'attendance', 'leaves',
    'timesheets', 'departments', 'training', 'shifts',
    'helpdesk', 'files', 'calendar', 'profile'
  ].includes(item.name));
}

if (userRole === 'manager') {
  return items.filter(item => [
    'dashboard', 'employees', 'attendance', 'leaves',
    'timesheets', 'training', 'helpdesk', 'files',
    'calendar', 'profile'
  ].includes(item.name));
}

if (userRole === 'employee') {
  return items.filter(item => [
    'dashboard', 'attendance', 'leaves', 'loans',
    'salary-advances', 'timesheets', 'training', 'travel',
    'shifts', 'helpdesk', 'files', 'calendar', 'profile'
  ].includes(item.name));
}
```

---

## Database-Level Filtering

All data queries in controllers use role-based WHERE clauses:

```php
// Example: Section Head sees department employees only
if ($user->isSectionHead()) {
    $sectionHeadEmployee = $user->employee;
    if ($sectionHeadEmployee && $sectionHeadEmployee->department_id) {
        $query->where('department_id', $sectionHeadEmployee->department_id);
    }
}

// Example: Manager sees team members only
if ($user->isManager()) {
    $query->where('manager_id', $user->id);
}

// Example: Employee sees own data only
if ($user->isEmployee()) {
    $query->where('employee_id', $user->employee->id);
}
```

---

## Approval Workflow

### Leave Request Flow

1. **Employee submits leave request**
   - Status: `pending`
   - Approval Level: `pending`

2. **Section Head reviews (First Approval)**
   - Status remains: `pending`
   - Approval Level: `first_approved` (if approved)
   - Fields updated: `first_approved_by`, `first_approval_remarks`, `first_approved_at`
   
3. **HR Admin reviews (Final Approval)**
   - Status changes to: `approved`
   - Approval Level: `final_approved`
   - Fields updated: `final_approved_by`, `final_approval_remarks`, `final_approved_at`
   - Leave balance deducted

4. **Super Admin can override at any stage**
   - Can directly approve without workflow
   - Can approve/reject at any level

### Rejection Flow

- **Section Head rejects**: Status = `rejected`, Approval Level = `rejected`, workflow ends
- **HR Admin rejects**: Status = `rejected`, Approval Level = `rejected`, workflow ends

---

## Testing Access Control

### Test Accounts (All passwords: `password`)

| Email | Role | Department | Purpose |
|-------|------|------------|---------|
| super@hrms.com | super_admin | - | Test full access |
| hradmin@hrms.com | hr_admin | - | Test final approval |
| finance.head@hrms.com | section_head | Finance | Test dept management |
| it.head@hrms.com | section_head | IT | Test dept management |
| manager@hrms.com | manager | - | Test team view |
| employee@hrms.com | employee | - | Test self-service |

### Testing Checklist

**Employee Role:**
- [ ] Cannot access employee list
- [ ] Can only see own attendance
- [ ] Can only see own leaves
- [ ] Can only see own payslips
- [ ] Cannot access payroll module
- [ ] Cannot access departments

**Section Head Role:**
- [ ] Can see department employees only
- [ ] Can see department attendance
- [ ] Can approve department leave requests (first level)
- [ ] Cannot see payroll
- [ ] Cannot see other departments

**HR Admin Role:**
- [ ] Can see all employees
- [ ] Can see all attendance
- [ ] Can give final approval to leaves
- [ ] Can generate payroll
- [ ] Can access all HR modules

**Super Admin Role:**
- [ ] Can access everything
- [ ] Can override workflows
- [ ] Can manage organization structure

---

## Security Considerations

### Backend Validation
✅ All controllers validate user role before data access
✅ Database queries filtered by role-specific WHERE clauses
✅ 403 Forbidden responses for unauthorized access
✅ Cannot access other users' data via API manipulation

### Frontend Protection
✅ Menu items hidden based on role
✅ Routes protected with navigation guards
✅ UI elements conditional on user role
⚠️ **Note**: Frontend is for UX only, security enforced at API level

### API Security
✅ All routes protected with `auth:sanctum` middleware
✅ User role checked in every controller action
✅ Cannot bypass by changing URL or API endpoint
✅ Cannot view other employees' data by guessing IDs

---

## Future Enhancements

- [ ] Add `can_approve_first` permission flag to section_head users
- [ ] Add `can_approve_final` permission flag to hr_admin users
- [ ] Implement permission groups for fine-grained access control
- [ ] Add audit log for permission changes
- [ ] Add role-based API rate limiting
- [ ] Implement data encryption for sensitive fields

---

## Troubleshooting

**Issue**: Employee can still see employee list
- **Cause**: Frontend not updated or cached
- **Fix**: Clear browser cache, logout/login

**Issue**: Section head sees all employees
- **Cause**: Section head not assigned to department
- **Fix**: Update employee record with department_id

**Issue**: 403 Forbidden on legitimate access
- **Cause**: User role not properly set
- **Fix**: Check user.role field in database

**Issue**: Dashboard shows wrong stats
- **Cause**: Role not recognized in dashboard controller
- **Fix**: Verify role checking methods in User model

---

## Documentation Updated

- ✅ Frontend menu filtering
- ✅ Backend controller access control
- ✅ Dashboard role-based stats
- ✅ Payroll access restrictions
- ✅ Employee data filtering
- ✅ Attendance filtering
- ✅ Leave application filtering
- ✅ Role hierarchy explained
- ✅ Test accounts provided

**Last Updated**: February 22, 2026
