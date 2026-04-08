# Super Admin Role & Permission Management - User Guide

## Status: ✅ FULLY IMPLEMENTED

Super Admin can already change user roles and grant/revoke permissions through the system.

## What's Already In Place

### 1. Backend API (✅ Complete)
All necessary API endpoints exist in `routes/api.php`:

**User Role Management:**
- `POST /api/users/{user}/assign-role` - Assign role to user
- `DELETE /api/users/{user}/remove-role` - Remove role from user
- `POST /api/users/{user}/grant-permission` - Grant direct permission to user
- `POST /api/users/{user}/revoke-permission` - Revoke permission from user
- `GET /api/users/{user}/permissions` - View user's all permissions

**Role Management:**
- `GET /api/roles` - List all roles
- `POST /api/roles` - Create new role
- `GET /api/roles/{role}` - View role details
- `PUT /api/roles/{role}` - Update role
- `DELETE /api/roles/{role}` - Delete role
- `POST /api/roles/{role}/permissions/sync` - Sync role permissions
- `POST /api/roles/{role}/permissions/grant` - Grant permission to role
- `POST /api/roles/{role}/permissions/revoke` - Revoke permission from role

**Permission Management:**
- `GET /api/permissions` - List all permissions
- `POST /api/permissions` - Create new permission
- `GET /api/permissions/{permission}` - View permission
- `PUT /api/permissions/{permission}` - Update permission
- `DELETE /api/permissions/{permission}` - Delete permission

**All routes protected with `super_admin` middleware.**

### 2. Controllers (✅ Complete)
Three fully-implemented controllers:
- `UserRoleController.php` - User role and permission assignment
- `RoleController.php` - Role CRUD and management
- `PermissionController.php` - Permission CRUD

### 3. Frontend Interface (✅ Complete)
Two admin pages:
- **Roles & Permissions** (`/admin/roles`) - Manage roles and their permissions
- **User Role Management** (`/admin/user-roles`) - Assign roles to users

### 4. Pinia Store (✅ Complete)
`stores/role.js` with all necessary methods:
- `assignRoleToUser()`
- `removeRoleFromUser()`
- `grantPermissionToUser()`
- `revokePermissionFromUser()`
- `syncRolePermissions()`

### 5. Menu Access (✅ Complete)
Admin menu items show automatically for super admins in the sidebar.

## How to Use

### Access the Admin Panel

1. **Login as Super Admin**
   ```
   Email: admin@hrms.com
   Password: password
   ```

2. **Navigate to Admin Section**
   Look for these menu items at the bottom of the sidebar:
   - 🔐 **Roles & Permissions**
   - 👥 **User Role Management**

### Manage Roles & Permissions

#### View All Roles
1. Click **Roles & Permissions** in sidebar
2. See list of all roles with their permissions
3. Filter by active status or search

#### Create New Role
1. Go to **Roles & Permissions**
2. Click **Create New Role** button
3. Fill in:
   - Role Name (e.g., "Department Manager")
   - Slug (auto-generated if empty)
   - Description
   - Select permissions to assign
4. Click **Save**

#### Edit Existing Role
1. Go to **Roles & Permissions**
2. Click **Edit** on any role
3. Update name, description, or permissions
4. Click **Update**

#### View Role Permissions
1. Go to **Roles & Permissions**
2. Click on any role name
3. See all permissions grouped by module
4. View users assigned to this role

### Assign Roles to Users

#### View All Users
1. Click **User Role Management** in sidebar
2. See list of all users with their current roles
3. Search users by name or email

#### Assign Role to User
1. Go to **User Role Management**
2. Find the user in the list
3. Click **Assign Role** button
4. Select role from dropdown
5. Click **Assign**
6. User immediately gets all permissions from that role

#### Remove Role from User
1. Go to **User Role Management**
2. Find the user with assigned role
3. Click **Remove Role**
4. Confirm action
5. User reverts to default employee role

#### View User Permissions
1. Go to **User Role Management**
2. Click **View Permissions** on any user
3. See all permissions (from role + direct grants)
4. Grant additional direct permissions if needed
5. Revoke specific permissions

### Grant Direct Permissions

Sometimes you need to give specific permissions without changing roles:

1. Go to **User Role Management**
2. Click **View Permissions** on user
3. Click **Grant Permission**
4. Select permission from list
5. Click **Add**
6. User now has this permission in addition to role permissions

### Permission System Overview

**Permission Format:** `module.action`

**Examples:**
- `employees.view` - View employees
- `employees.create` - Create employees
- `payroll.process` - Process payroll
- `leaves.approve` - Approve leave requests

**Permission Hierarchy:**
```
Super Admin (role = 'super_admin')
  ↓ Bypasses all permission checks
  ↓ Has access to everything

Custom Role (e.g., "HR Manager")
  ↓ Has specific permissions assigned
  ↓ employees.*, attendance.*, leaves.*

User with Direct Permissions
  ↓ gets role permissions + direct permissions
  ↓ useful for exceptions
```

## API Usage Examples

### Assign Role to User
```bash
curl -X POST http://localhost:8001/api/users/5/assign-role \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "role_id": 3
  }'
```

### Create New Role
```bash
curl -X POST http://localhost:8001/api/roles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Department Manager",
    "description": "Manages department resources",
    "is_active": true,
    "permission_ids": [1, 2, 3, 15, 16, 17]
  }'
```

### Sync Role Permissions
```bash
curl -X POST http://localhost:8001/api/roles/3/permissions/sync \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "permission_ids": [1, 2, 3, 4, 5]
  }'
```

### Grant Direct Permission to User
```bash
curl -X POST http://localhost:8001/api/users/5/grant-permission \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "permission_id": 25
  }'
```

## Common Workflows

### Scenario 1: Promote Employee to Manager
1. Go to **User Role Management**
2. Find employee
3. Click **Assign Role**
4. Select "Manager" or "Section Head"
5. Click **Assign**
6. Employee immediately sees new menu items
7. Employee can now manage team

### Scenario 2: Create Custom Role for HR Staff
1. Go to **Roles & Permissions**
2. Click **Create New Role**
3. Name: "HR Staff"
4. Select permissions:
   - employees.* (all employee permissions)
   - attendance.view, attendance.update
   - leaves.view, leaves.approve
   - documents.view, documents.manage
5. Click **Save**
6. Go to **User Role Management**
7. Assign this role to HR staff members

### Scenario 3: Temporarily Grant Extra Permission
1. Go to **User Role Management**
2. Find user
3. Click **View Permissions**
4. Click **Grant Permission**
5. Select "payroll.view" (for temporary access)
6. User can now view payroll
7. Later, click **Revoke** to remove

## Security Features

### Role-Based Access Control (RBAC)
- Users inherit all permissions from their assigned role
- Super Admin bypasses all checks
- Direct permissions can supplement role permissions

### Middleware Protection
- All admin routes protected by `super_admin` middleware
- Regular users cannot access role management
- API returns 403 if unauthorized

### Frontend Guards
- Menu items hidden from non-super-admins
- Router guards block URL access
- Components check permissions before showing actions

## Troubleshooting

### Admin Menu Not Showing
**Problem**: Super admin doesn't see admin menu items

**Solutions**:
1. Check user.role === 'super_admin' (not 'admin')
2. Logout and login again
3. Clear browser cache
4. Check console for errors

### Cannot Assign Role
**Problem**: "You don't have permission" error

**Solutions**:
1. Verify logged in as super_admin
2. Check role is active (is_active = true)
3. Verify super_admin middleware working

### Permission Changes Not Applying
**Problem**: User doesn't see new menu items after role change

**Solutions**:
1. User must logout and login again
2. Permissions cached in frontend store
3. Call `permissionStore.fetchMyPermissions()` to refresh

## Database Tables

The system uses these tables:

### roles
- id, name, slug, description, is_active
- Contains all role definitions

### permissions
- id, name, slug, module, action, description
- Contains all permission definitions

### role_permissions
- role_id, permission_id
- Links roles to permissions (many-to-many)

### users
- role_id (foreign key to roles)
- role (fallback string field)
- Links user to assigned role

### user_permissions
- user_id, permission_id
- Direct permissions granted to users

## Best Practices

### When to Use Roles
- ✅ Standard job positions (Manager, HR Admin, etc.)
- ✅ Multiple users need same permissions
- ✅ Permission sets change together
- ✅ Easier to manage at scale

### When to Use Direct Permissions
- ✅ Temporary access needed
- ✅ Exception to role permissions
- ✅ Testing new permissions
- ✅ One-off requirements

### Permission Naming Convention
Follow the `module.action` pattern:
- **module**: employees, payroll, leaves, etc.
- **action**: view, create, update, delete, approve, manage

Keep permissions granular:
- ❌ `hr.all` (too broad)
- ✅ `employees.view`, `employees.create`, `employees.update`

## Summary

The system is **fully operational** and ready to use:

✅ Super Admin can manage all roles
✅ Super Admin can manage all permissions  
✅ Super Admin can assign roles to users
✅ Super Admin can grant/revoke direct permissions
✅ Changes apply immediately
✅ Full UI and API available
✅ All routes protected
✅ Menu automatically updated

**Access the features at:**
- http://localhost:5173/admin/roles - Manage roles
- http://localhost:5173/admin/user-roles - Assign roles to users

**Login as Super Admin:**
- Email: admin@hrms.com
- Password: password
