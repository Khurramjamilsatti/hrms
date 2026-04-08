# Module Access Control - Visual Flow

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    USER AUTHENTICATION                       │
│                                                              │
│  User Login → JWT Token → Store in localStorage             │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              PERMISSION LOADING (Automatic)                  │
│                                                              │
│  API Call: GET /api/my-permissions                          │
│  Returns: {                                                  │
│    permissions: [...],          // All user permissions      │
│    allowed_modules: [...],      // Accessible modules        │
│    role: "...",                 // User role                │
│    is_super_admin: false        // Admin flag               │
│  }                                                           │
│                                                              │
│  Stored in: Pinia Permission Store                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                  DUAL ACCESS CONTROL                         │
│                                                              │
│  ┌─────────────────┐          ┌─────────────────┐          │
│  │   MENU FILTER   │          │  ROUTER GUARD   │          │
│  │   (UI Level)    │          │  (URL Level)    │          │
│  └────────┬────────┘          └────────┬────────┘          │
│           │                             │                    │
│           │ Filter menu items           │ Block navigation   │
│           │ by permissions              │ to unauthorized    │
│           │                             │ routes             │
│           ▼                             ▼                    │
│  Only show accessible        Redirect to dashboard          │
│  modules in sidebar          + show error message           │
└─────────────────────────────────────────────────────────────┘
```

## Navigation Flow Chart

```
                    USER CLICKS MENU ITEM OR TYPES URL
                                  │
                                  ▼
                    ┌─────────────────────────┐
                    │   Vue Router Navigation │
                    │   Triggered             │
                    └────────────┬────────────┘
                                 │
                                 ▼
                    ┌────────────────────────┐
                    │  beforeEach Guard      │
                    │  (router/index.js)     │
                    └────────────┬───────────┘
                                 │
                      ┌──────────┴──────────┐
                      │  Is Authenticated?  │
                      └──────────┬──────────┘
                                 │
                    NO ◄─────────┼─────────► YES
                    │            │            │
                    ▼            │            ▼
            ┌──────────────┐    │   ┌──────────────────┐
            │ Redirect to  │    │   │ Permissions      │
            │ /login       │    │   │ Loaded?          │
            └──────────────┘    │   └────────┬─────────┘
                                │            │
                                │   NO ◄─────┼─────► YES
                                │   │        │       │
                                │   ▼        │       ▼
                                │ ┌──────────────┐  │
                                │ │ Fetch        │  │
                                │ │ Permissions  │  │
                                │ │ from API     │  │
                                │ └──────┬───────┘  │
                                │        │          │
                                │        ▼          │
                                │   ┌──────────┐   │
                                │   │ Success? │   │
                                │   └────┬─────┘   │
                                │        │         │
                                │  YES ◄─┼─► NO   │
                                │   │    │    │   │
                                │   │    │    ▼   │
                                │   │    │  ┌──────────┐
                                │   │    │  │ Logout & │
                                │   │    │  │ Redirect │
                                │   │    │  └──────────┘
                                │   │    │
                                │   └────┼─────────────┘
                                │        │
                                ▼        ▼
                    ┌────────────────────────────┐
                    │ Route has meta.module?     │
                    └────────────┬───────────────┘
                                 │
                      NO ◄───────┼────────► YES
                      │          │          │
                      │          │          ▼
                      │          │  ┌──────────────────┐
                      │          │  │ Is Dashboard or  │
                      │          │  │ Profile?         │
                      │          │  └────────┬─────────┘
                      │          │           │
                      │          │  YES ◄────┼────► NO
                      │          │  │        │      │
                      │          │  │        │      ▼
                      │          │  │        │  ┌──────────────────┐
                      │          │  │        │  │ Check            │
                      │          │  │        │  │ canAccessModule()│
                      │          │  │        │  └────────┬─────────┘
                      │          │  │        │           │
                      │          │  │        │  YES ◄────┼────► NO
                      │          │  │        │  │        │      │
                      │          ▼  ▼        ▼  ▼        │      ▼
                      │      ┌──────────────────┐        │  ┌──────────────┐
                      └─────►│ ALLOW NAVIGATION │        │  │ BLOCK ACCESS │
                             └──────────────────┘        │  └──────┬───────┘
                                      │                  │         │
                                      ▼                  │         ▼
                             ┌──────────────────┐        │  ┌─────────────────┐
                             │ Load Component   │        │  │ Redirect to     │
                             └──────────────────┘        │  │ /dashboard      │
                                                         │  │ ?denied=module  │
                                                         │  └────────┬────────┘
                                                         │           │
                                                         │           ▼
                                                         │  ┌─────────────────┐
                                                         │  │ Dashboard       │
                                                         │  │ detects query   │
                                                         │  │ param           │
                                                         │  └────────┬────────┘
                                                         │           │
                                                         │           ▼
                                                         │  ┌─────────────────┐
                                                         │  │ Show Error      │
                                                         │  │ Notification    │
                                                         └──┤ "No Permission" │
                                                            └─────────────────┘
```

## Permission Check Logic

```
┌─────────────────────────────────────────────────────────┐
│          Permission Store State                          │
│                                                          │
│  isSuperAdmin: boolean                                   │
│  permissions: Permission[]                               │
│  allowedModules: string[]                                │
│  role: string                                            │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
         ┌─────────────────────────┐
         │ canAccessModule(module) │
         └───────────┬─────────────┘
                     │
                     ▼
         ┌───────────────────────┐
         │ Is Super Admin?       │
         └───────────┬───────────┘
                     │
          YES ◄──────┼──────► NO
          │          │         │
          │          │         ▼
          │          │  ┌──────────────────────┐
          │          │  │ module in             │
          │          │  │ allowedModules[]?    │
          │          │  └──────────┬───────────┘
          │          │             │
          │          │  YES ◄──────┼──────► NO
          ▼          ▼  │          │         │
    ┌────────────────────┐         │         │
    │  GRANT ACCESS      │◄────────┘         │
    │  return true       │                   │
    └────────────────────┘                   ▼
                                    ┌────────────────┐
                                    │  DENY ACCESS   │
                                    │  return false  │
                                    └────────────────┘
```

## Menu Filtering Example

```
User Role: Section Head
Permissions: employees.view, attendance.view, leaves.view, leaves.approve
Allowed Modules: ["employees", "attendance", "leaves"]

┌─────────────────────────────────────────────┐
│          ALL MENU ITEMS                      │
│                                             │
│  ✓ Dashboard        (always visible)        │
│  ✓ Employees        (in allowed_modules)    │
│  ✓ Attendance       (in allowed_modules)    │
│  ✓ Leaves           (in allowed_modules)    │
│  ✗ Payroll          (NOT in allowed_modules)│
│  ✗ Departments      (NOT in allowed_modules)│
│  ✗ Roles            (NOT in allowed_modules)│
│  ✓ Profile          (always visible)        │
└─────────────────────────────────────────────┘
                    │
                    │ Filter by canAccessModule()
                    ▼
┌─────────────────────────────────────────────┐
│        VISIBLE MENU ITEMS                    │
│                                             │
│  ✓ Dashboard                                │
│  ✓ Employees                                │
│  ✓ Attendance                               │
│  ✓ Leaves                                   │
│  ✓ Profile                                  │
└─────────────────────────────────────────────┘
```

## URL Access Blocking Example

```
Scenario: Employee tries to access /payroll

Step 1: User types URL
  Browser URL: http://localhost:5173/payroll
  
Step 2: Router Guard Triggered
  Route: { path: '/payroll', meta: { module: 'payroll' } }
  
Step 3: Check Authentication
  ✓ User is authenticated
  
Step 4: Check Permissions Loaded
  ✓ Permissions loaded
  
Step 5: Check Module Access
  Module: 'payroll'
  Allowed Modules: ['employees', 'attendance', 'leaves']
  Result: 'payroll' NOT in allowed_modules
  
Step 6: Block Navigation
  Action: Redirect to dashboard
  URL: http://localhost:5173/?denied=payroll&message=...
  
Step 7: Dashboard Loads
  Detects: route.query.denied = 'payroll'
  Action: Show error notification
  Message: "You do not have permission to access this module"
  
Step 8: Clean URL
  Remove query params
  Final URL: http://localhost:5173/
```

## Permission Hierarchy

```
Super Admin
    │
    ├─ ALL Modules
    │   ├─ Dashboard ✓
    │   ├─ Employees ✓
    │   ├─ Attendance ✓
    │   ├─ Leaves ✓
    │   ├─ Payroll ✓
    │   ├─ Departments ✓
    │   ├─ Roles ✓
    │   └─ All Other Modules ✓
    │
HR Admin
    │
    ├─ Most HR Modules
    │   ├─ Dashboard ✓
    │   ├─ Employees ✓
    │   ├─ Attendance ✓
    │   ├─ Leaves ✓
    │   ├─ Payroll ✓
    │   ├─ Departments ✓
    │   └─ Roles ✗
    │
Section Head
    │
    ├─ Team Management Modules
    │   ├─ Dashboard ✓
    │   ├─ Employees ✓ (view team)
    │   ├─ Attendance ✓ (team)
    │   ├─ Leaves ✓ (approve)
    │   ├─ Payroll ✗
    │   └─ Departments ✗
    │
Employee
    │
    ├─ Self-Service Modules
    │   ├─ Dashboard ✓
    │   ├─ Profile ✓
    │   ├─ Attendance ✓ (self)
    │   ├─ Leaves ✓ (apply)
    │   ├─ Employees ✗
    │   └─ Payroll ✗
```

## Testing Matrix

| Module | Super Admin | HR Admin | Section Head | Employee |
|--------|-------------|----------|--------------|----------|
| Dashboard | ✓ | ✓ | ✓ | ✓ |
| Employees | ✓ | ✓ | ✓ (team) | ✗ |
| Attendance | ✓ | ✓ | ✓ (team) | ✓ (self) |
| Leaves | ✓ | ✓ | ✓ (approve) | ✓ (apply) |
| Payroll | ✓ | ✓ | ✗ | ✗ |
| Departments | ✓ | ✓ | ✗ | ✗ |
| Loans | ✓ | ✓ | ✗ | ✓ (self) |
| Training | ✓ | ✓ | ✓ | ✓ |
| Assets | ✓ | ✓ | ✗ | ✗ |
| Roles | ✓ | ✗ | ✗ | ✗ |
| Profile | ✓ | ✓ | ✓ | ✓ |

✓ = Full Access
✓ (limited) = Limited Access (specified in parentheses)
✗ = No Access (blocked by router + hidden in menu)
