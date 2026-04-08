# Test Accounts - Multi-Level Approval System

## Hierarchy Overview

```
LEVEL 5: Super Admin (God Mode)
    ↓
LEVEL 4: HR Admin (Final Approval Authority)
    ↓
LEVEL 3: Section Heads (First Level Approval - Department Based)
    ↓
LEVEL 2: Managers (Team Leads)
    ↓
LEVEL 1: Employees (Regular Users)
```

## Test Accounts

### Level 5: Super Admin (Can Do Anything)
- **Email**: `super@hrms.com`
- **Password**: `password`
- **Role**: `super_admin`
- **Permissions**:
  - Full system access
  - Can override any approval workflow
  - Can approve at any stage
  - All admin functions

---

### Level 4: HR Admin (Final Approval Authority)
- **Email**: `hradmin@hrms.com`
- **Password**: `password`
- **Role**: `hr_admin`
- **Permissions**:
  - Final approval for leave requests
  - Final approval for attendance corrections
  - Full HR management access
  - Can approve requests that section heads have approved
  - Payroll management
  - Employee records management

---

### Level 3: Section Heads (First Level Approvers)

#### Finance Section Head
- **Email**: `finance.head@hrms.com`
- **Password**: `password`
- **Role**: `section_head`
- **Department**: Finance
- **Permissions**:
  - First-level approval for Finance department employees
  - Can approve/reject leave requests from their department
  - Can approve/reject attendance corrections from their department
  - Manage team members
  - View department reports

#### IT Section Head
- **Email**: `it.head@hrms.com`
- **Password**: `password`
- **Role**: `section_head`
- **Department**: IT
- **Permissions**: Same as Finance Head but for IT department

#### HR Section Head
- **Email**: `hr.head@hrms.com`
- **Password**: `password`
- **Role**: `section_head`
- **Department**: Human Resources
- **Permissions**: Same as above but for HR department

#### Operations Section Head
- **Email**: `operations.head@hrms.com`
- **Password**: `password`
- **Role**: `section_head`
- **Department**: Operations
- **Permissions**: Same as above but for Operations department

#### Sales Section Head
- **Email**: `sales.head@hrms.com`
- **Password**: `password`
- **Role**: `section_head`
- **Department**: Sales
- **Permissions**: Same as above but for Sales department

---

### Level 2: Managers (Team Leads)

#### Manager User (Legacy)
- **Email**: `manager@hrms.com`
- **Password**: `password`
- **Role**: `manager`
- **Permissions**:
  - Team management
  - View team reports
  - Cannot approve leave/attendance (escalates to section head)

#### Team Lead
- **Email**: `team.lead@hrms.com`
- **Password**: `password`
- **Role**: `manager`
- **Permissions**: Same as Manager User

---

### Level 1: Employees (Regular Users)

#### Admin User (Legacy Admin)
- **Email**: `admin@hrms.com`
- **Password**: `password`
- **Role**: `admin`
- **Permissions**: Standard admin functions (legacy account)

#### Employee User (Basic)
- **Email**: `employee@hrms.com`
- **Password**: `password`
- **Role**: `employee`
- **Permissions**:
  - View own profile
  - Apply for leave
  - Check in/out attendance
  - View payslips
  - Submit attendance corrections

#### John Doe
- **Email**: `john.doe@hrms.com`
- **Password**: `password`
- **Role**: `employee`

#### Jane Smith
- **Email**: `jane.smith@hrms.com`
- **Password**: `password`
- **Role**: `employee`

#### Mike Johnson
- **Email**: `mike.johnson@hrms.com`
- **Password**: `password`
- **Role**: `employee`

#### Sarah Williams
- **Email**: `sarah.williams@hrms.com`
- **Password**: `password`
- **Role**: `employee`

#### David Brown
- **Email**: `david.brown@hrms.com`
- **Password**: `password`
- **Role**: `employee`

---

## Approval Workflow Examples

### Leave Request Approval Flow

1. **Employee Submits Leave Request**
   - Login as: `john.doe@hrms.com`
   - Submit leave application
   - Status: `pending`, Approval Level: `pending`

2. **Section Head Reviews (First Approval)**
   - Login as: `finance.head@hrms.com` (assuming John is in Finance)
   - View pending leave requests for Finance department
   - Approve or Reject
   - If Approved: Status: `pending`, Approval Level: `first_approved`
   - If Rejected: Status: `rejected`, Approval Level: `rejected`

3. **HR Admin Reviews (Final Approval)**
   - Login as: `hradmin@hrms.com`
   - View all first-approved leave requests
   - Final Approve or Reject
   - If Approved: Status: `approved`, Approval Level: `final_approved`
   - If Rejected: Status: `rejected`, Approval Level: `rejected`

### Super Admin Override
- Login as: `super@hrms.com`
- Can approve at any stage
- Can bypass workflow entirely

---

## Testing Scenarios

### Scenario 1: Complete Approval Flow
1. Login as employee → Submit leave
2. Login as section head → Approve first level
3. Login as HR admin → Final approve
4. Login as employee → Verify approved leave

### Scenario 2: Rejection at First Level
1. Login as employee → Submit leave
2. Login as section head → Reject with remarks
3. Login as employee → See rejection and remarks

### Scenario 3: Rejection at Final Level
1. Login as employee → Submit leave
2. Login as section head → Approve
3. Login as HR admin → Reject with remarks
4. Login as employee → See final rejection

### Scenario 4: Super Admin Override
1. Login as employee → Submit leave
2. Login as super admin → Directly approve (bypass workflow)
3. Login as employee → See approved leave

---

## Setup Instructions

1. **Run Fresh Migration**:
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Access Application**:
   - Backend: http://localhost:8001/api
   - Frontend: http://localhost:5173

3. **Login with Test Accounts**:
   - Try logging in with different roles
   - Test the approval workflows

4. **Verify Role Hierarchy**:
   - Check that section heads only see their department requests
   - Check that HR admin sees all first-approved requests
   - Check that super admin can access everything

---

## Role Methods in User Model

```php
// Check role type
$user->isSuperAdmin()        // true if super_admin
$user->isHRAdmin()            // true if hr_admin
$user->isSectionHead()        // true if section_head
$user->isManager()            // true if manager
$user->isEmployee()           // true if employee

// Check approval permissions
$user->canApproveFirst()      // section_head, hr_admin, super_admin
$user->canApproveFinal()      // hr_admin, super_admin

// Get role level (1-5)
$user->getRoleLevel()         // 5 for super_admin, 1 for employee

// Compare authority
$user1->hasHigherAuthorityThan($user2)
```

---

## Database Schema Changes

### Leave Applications Table
```
first_approved_by          → User who performed first approval
first_approval_remarks     → Remarks from first approver
first_approved_at          → Timestamp of first approval

final_approved_by          → User who performed final approval (renamed from approved_by)
final_approval_remarks     → Remarks from final approver
final_approved_at          → Timestamp of final approval

approval_level             → pending | first_approved | final_approved | rejected
```

### Attendances Table
```
first_approved_by          → User who performed first approval
first_approval_remarks     → Remarks from first approver
first_approved_at          → Timestamp of first approval

final_approved_by          → User who performed final approval
final_approval_remarks     → Remarks from final approver
final_approved_at          → Timestamp of final approval

approval_level             → pending | first_approved | final_approved | rejected
```

---

## Notes

- All test accounts use password: `password`
- Section heads are department-based approvers
- HR Admin sees all requests after first approval
- Super Admin can override any workflow
- Employees can only see their own requests
- Managers can view team data but cannot approve
