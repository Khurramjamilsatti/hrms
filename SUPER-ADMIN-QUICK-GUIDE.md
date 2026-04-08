# Super Admin: How to Manage Roles & Permissions

## ✅ System Status: FULLY OPERATIONAL

All tests passed! Super admin can manage roles and assign them to users.

## Quick Access Guide

### Step 1: Login as Super Admin
```
URL: http://localhost:5173/login
Email: admin@hrms.com
Password: password
```

### Step 2: Find Admin Menu Items

After login, look at the **sidebar menu**. Scroll to the bottom to find:

```
┌─────────────────────────────────┐
│  📊 Dashboard                   │
│  👥 Employees                   │
│  📅 Attendance                  │
│  🏖️ Leave Requests             │
│  💰 Payroll                     │
│  ...                            │
│  📆 Calendar                    │
│  🏛️ Organization               │
│  ──────────────────────────────│
│  🔐 Roles & Permissions    ← HERE
│  👥 User Role Management   ← HERE
│  ──────────────────────────────│
│  👤 My Profile                  │
└─────────────────────────────────┘
```

**These two menu items appear ONLY for Super Admin!**

### Step 3: Manage Roles

Click **🔐 Roles & Permissions** to:

- ✅ View all roles
- ✅ Create new roles
- ✅ Edit role names and descriptions
- ✅ Assign permissions to roles
- ✅ Activate/deactivate roles
- ✅ Delete unused roles

**Direct URL:** http://localhost:5173/admin/roles

### Step 4: Assign Roles to Users

Click **👥 User Role Management** to:

- ✅ View all users and their current roles
- ✅ Assign roles to users (one at a time)
- ✅ Remove roles from users
- ✅ Grant direct permissions to individual users
- ✅ View all permissions for each user

**Direct URL:** http://localhost:5173/admin/user-roles

## What You Can Do

### 1. Create a New Role

**Example: Create "Accountant" Role**

1. Go to **Roles & Permissions**
2. Click **"Create New Role"** button
3. Fill in form:
   ```
   Name: Accountant
   Description: Manages payroll and financial records
   Select Permissions:
     ☑ payroll.view
     ☑ payroll.process
     ☑ employees.view (to see employee data)
     ☑ salary-components.view
     ☑ loans.view
     ☑ salary-advance.view
   ```
4. Click **"Save"**
5. Role created! Now you can assign it to users.

### 2. Assign Role to User

**Example: Make "John Doe" an HR Manager**

1. Go to **User Role Management**
2. Find "John Doe" in the user list (use search if many users)
3. Click **"Assign Role"** button on his row
4. Select "HR Admin" from dropdown
5. Click **"Assign"**
6. Done! ✅

**Result:**
- John immediately sees all HR modules in menu
- John can now manage employees, attendance, leaves
- John's permissions updated in real-time

### 3. Grant Extra Permission to User

**Example: Let one employee view payroll (exception)**

1. Go to **User Role Management**
2. Find the employee
3. Click **"View Permissions"**
4. Click **"Grant Permission"**
5. Select "payroll.view"
6. Click **"Add"**
7. Done! ✅

**Result:**
- Employee keeps their regular role
- Gets additional "payroll.view" permission
- Can now see payroll menu item

### 4. Remove Role from User

**Example: Demote a manager back to employee**

1. Go to **User Role Management**
2. Find the manager
3. Click **"Remove Role"**
4. Confirm action
5. Done! ✅

**Result:**
- User loses all role permissions
- Reverts to basic employee access
- Menu items automatically update

## Current System Stats

Based on test results:

```
✅ 5 Roles Created:
   1. Super Admin (0 permissions - bypasses all checks)
   2. HR Admin (67 permissions)
   3. Manager (38 permissions)
   4. Employee (28 permissions)
   5. Section Head (40 permissions)

✅ 80 Permissions Available
   Across 28 modules

✅ 100 Users in System
   Ready for role assignment
```

## API Endpoints You Can Use

All these work with Super Admin token:

### Role Management
```bash
GET    /api/roles              # List all roles
POST   /api/roles              # Create new role
GET    /api/roles/{id}         # View role details
PUT    /api/roles/{id}         # Update role
DELETE /api/roles/{id}         # Delete role
```

### Permission Management
```bash
GET    /api/permissions        # List all permissions
POST   /api/permissions        # Create permission
GET    /api/permissions/{id}   # View permission
PUT    /api/permissions/{id}   # Update permission
DELETE /api/permissions/{id}   # Delete permission
```

### User Role Assignment
```bash
POST   /api/users/{id}/assign-role       # Assign role to user
DELETE /api/users/{id}/remove-role       # Remove user's role
POST   /api/users/{id}/grant-permission  # Grant permission to user
POST   /api/users/{id}/revoke-permission # Revoke permission from user
GET    /api/users/{id}/permissions       # View user's permissions
```

### Role Permission Sync
```bash
POST   /api/roles/{id}/permissions/sync   # Replace all role permissions
POST   /api/roles/{id}/permissions/grant  # Add permission to role
POST   /api/roles/{id}/permissions/revoke # Remove permission from role
```

## Permission Naming Convention

All permissions follow this pattern: `module.action`

**Examples:**
```
employees.view      - Can view employees
employees.create    - Can create employees
employees.update    - Can edit employees
employees.delete    - Can delete employees

payroll.view        - Can view payroll
payroll.process     - Can process payroll

leaves.apply        - Can apply for leaves
leaves.approve      - Can approve leaves

departments.manage  - Full department access
```

## Common Use Cases

### Scenario 1: New HR Staff Member Joins
```
1. Create user account
2. Go to User Role Management
3. Assign "HR Admin" role
4. They immediately get 67 HR permissions
5. Can start working right away
```

### Scenario 2: Temporary Payroll Access
```
1. Go to User Role Management
2. Find the user
3. Grant "payroll.view" permission directly
4. User can view payroll (temporary)
5. Later revoke when no longer needed
```

### Scenario 3: Create Department Head Role
```
1. Go to Roles & Permissions
2. Create "Department Head" role
3. Assign permissions:
   - employees.view, employees.update
   - attendance.view, attendance.approve
   - leaves.view, leaves.approve
   - performance.view, performance.create
4. Save role
5. Assign to all department heads
```

## Security Notes

### Who Can Access Admin Panel?
✅ **Only Super Admin** (role = 'super_admin')

❌ Not visible to:
- HR Admin
- Managers
- Section Heads
- Employees

### Protection Layers
1. **Backend Middleware**: `super_admin` middleware on all routes
2. **Frontend Menu**: Admin items only show for super_admin
3. **Router Guards**: URL access blocked for non-super-admins
4. **API Validation**: All endpoints check super_admin status

### Safety Features
- ✅ Cannot delete role if users assigned to it
- ✅ Cannot deactivate last super admin role
- ✅ Direct permissions supplement (don't override) role permissions
- ✅ All changes logged in database
- ✅ Role changes require logout/login to apply

## Troubleshooting

### Problem: Admin Menu Not Showing

**Check:**
1. Logged in as 'admin@hrms.com'?
2. User role is 'super_admin' not 'admin'?
3. Try logout and login again
4. Clear browser cache

### Problem: "403 Forbidden" Error

**Solution:**
- Your account is not super_admin
- Check `users` table: `role_id` should point to Super Admin role
- Contact database admin

### Problem: Changes Not Applying

**Solution:**
- User must logout and login again
- Permissions loaded at login time
- Cached in frontend store

## Documentation Files

- **SUPER-ADMIN-ROLE-MANAGEMENT.md** - Complete guide (this file)
- **MODULE-ACCESS-CONTROL.md** - Permission system docs
- **RBAC-IMPLEMENTATION.md** - Role implementation details
- **test-super-admin-access.sh** - Automated test script

## Summary

✅ **System is fully operational!**

Super Admin can:
- ✓ Manage all roles
- ✓ Manage all permissions
- ✓ Assign roles to any user
- ✓ Grant/revoke direct permissions
- ✓ Create custom roles for specific needs

**Access now:**
1. Login: http://localhost:5173/login (admin@hrms.com / password)
2. Roles: http://localhost:5173/admin/roles
3. Users: http://localhost:5173/admin/user-roles

Everything works perfectly! 🎉
