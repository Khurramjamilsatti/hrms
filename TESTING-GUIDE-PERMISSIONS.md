# Testing Guide - Permission System

## Quick Testing Steps

### 1. Start Development Servers

```bash
# Terminal 1 - Laravel Backend
cd /Users/khurramjamil/Documents/my-products/hrms
php artisan serve --port=8001

# Terminal 2 - Vue Frontend
npm run dev
```

### 2. Test Different User Roles

#### Test Accounts (from TEST-ACCOUNTS.md)
- **Super Admin**: admin@hrms.com / password
- **Manager**: manager@hrms.com / password
- **Employee**: employee@hrms.com / password

### 3. Verification Checklist

#### Navigation Menu
- [ ] **Super Admin Login**
  - Should see ALL menu items (24+ modules)
  - Should see "Roles & Permissions" and "User Role Management" in Administration section
  
- [ ] **Manager Login**
  - Should see reduced menu items (no admin modules)
  - Should NOT see "Roles & Permissions" section
  
- [ ] **Employee Login**
  - Should see minimal menu items (self-service only)
  - Should see: Dashboard, Attendance, Leaves, Profile, etc.
  - Should NOT see: Payroll, Departments, HR admin features

#### Component Buttons

**EmployeeList.vue** (`/employees`)
- [ ] Super Admin/HR Admin: See "Add Employee" button
- [ ] Super Admin/HR Admin: See Edit & Delete buttons in table
- [ ] Employee: Should NOT see Add/Edit/Delete buttons

**DepartmentList.vue** (`/departments`)
- [ ] Super Admin/HR Admin: See "Add Department" button
- [ ] Super Admin/HR Admin: See Edit & Delete buttons
- [ ] Manager: May see view-only (depending on permissions)
- [ ] Employee: Should NOT access this page (menu item hidden)

**LeaveList.vue** (`/leaves`)
- [ ] All users: See "Apply Leave" button
- [ ] Manager/HR Admin: See Approve/Reject buttons for pending leaves
- [ ] Employee: Should NOT see Approve/Reject buttons

#### Permission Loading
- [ ] Open browser console (F12)
- [ ] Login with any account
- [ ] Check console for: "Fetching my permissions..." or similar
- [ ] Verify no 404 errors for `/api/my-permissions`
- [ ] Verify no 401/403 errors on page load

#### Page Refresh
- [ ] Login successfully
- [ ] Navigate to a module (e.g., Employees)
- [ ] Refresh the page (F5)
- [ ] Menu should still show correct items
- [ ] Buttons should still show/hide correctly
- [ ] Check console - permissions should be fetched again

### 4. API Permission Testing

Open browser console and run:

```javascript
// Check current user's permissions
fetch('/api/my-permissions', {
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
  }
})
.then(r => r.json())
.then(console.log)

// Check specific permission
fetch('/api/check-permission', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ permission: 'employees.create' })
})
.then(r => r.json())
.then(console.log)
```

### 5. Backend Test Script

```bash
cd /Users/khurramjamil/Documents/my-products/hrms
chmod +x test-permissions.sh
./test-permissions.sh
```

Expected output:
```
✓ Test 1: Login successful (200)
✓ Test 2: Fetch permissions successful (200)
✓ Test 3: Check permission endpoint works (200)
✓ Test 4: Access employees endpoint works
✓ Test 5: Access departments endpoint works
✓ Test 6: Super admin access works
```

### 6. Logout Test
- [ ] Click Logout button
- [ ] Check localStorage is cleared (F12 > Application > Local Storage)
- [ ] Verify `auth_token` is removed
- [ ] Verify redirected to login page
- [ ] Try accessing `/employees` directly - should redirect to login

## Common Issues & Solutions

### Issue 1: Menu Not Filtering
**Symptoms**: All menu items show regardless of user role
**Solution**: Check browser console for permission fetch errors
```javascript
// In browser console
localStorage.getItem('auth_token') // Check token exists
```

### Issue 2: Buttons Not Appearing
**Symptoms**: Create/Edit/Delete buttons not showing even for admin
**Solution**: Check permission store has loaded
```javascript
// In Vue component, add temporary console.log
console.log('Permissions:', permissionStore.permissions)
console.log('Can create?', can('employees.create'))
```

### Issue 3: 403 Forbidden Errors
**Symptoms**: API returns 403 when accessing endpoints
**Solution**: Check user actually has permissions assigned
```bash
# In Laravel tinker
php artisan tinker
>>> $user = User::find(1);
>>> $user->getAllPermissions()->pluck('slug');
```

### Issue 4: Double /api/ Prefix
**Symptoms**: 404 errors for `/api/api/my-permissions`
**Solution**: Already fixed in permission store (line 48, 69)
- Verify axios baseURL: `axios.defaults.baseURL` should be '/api'
- Permission store routes should NOT include '/api' prefix

### Issue 5: Page Refresh Loses Permissions
**Symptoms**: Menu shows wrong items after refresh
**Solution**: Already fixed - checkAuth now fetches permissions
- Check App.vue calls `await authStore.checkAuth()`
- Check auth.js `checkAuth()` is async and calls `fetchMyPermissions()`

## Performance Checks

### Load Time
- [ ] Initial page load < 2 seconds
- [ ] Permission fetch < 500ms
- [ ] Menu renders without flicker
- [ ] No visible loading states for permissions

### Error Handling
- [ ] Graceful handling of permission fetch failure
- [ ] Fallback to role-based filtering if permissions fail
- [ ] Console errors are descriptive

## Testing Matrix

| Role | Can Access Employees | Can Create Employee | Can Approve Leave | Can Access Payroll |
|------|---------------------|--------------------|--------------------|-------------------|
| super_admin | ✅ | ✅ | ✅ | ✅ |
| hr_admin | ✅ | ✅ | ✅ | ✅ |
| manager | ✅ | ❌ | ✅ | ⚠️ (view only) |
| employee | ⚠️ (own profile) | ❌ | ❌ | ⚠️ (own payroll) |

✅ = Full Access
⚠️ = Limited/Conditional Access
❌ = No Access

## Browser DevTools Inspection

### Vue DevTools
1. Install Vue DevTools extension
2. Open DevTools > Vue tab
3. Navigate to Stores > permission
4. Check:
   - `permissions` array has items
   - `allowedModules` array populated
   - `isSuperAdmin` boolean correct
   - `loading` is false after load

### Network Tab
1. Open DevTools > Network
2. Login
3. Check for:
   - `POST /api/login` → 200
   - `GET /api/my-permissions` → 200
4. Refresh page
5. Check for:
   - `GET /api/my-permissions` → 200 (should fetch again)

### Console Tab
1. Should NOT see:
   - ❌ 401 Unauthorized errors
   - ❌ 404 Not Found for permission routes
   - ❌ CORS errors
   - ❌ JavaScript errors

2. Should see (if you added debug logs):
   - ✅ "Permissions fetched: [...]"
   - ✅ "User loaded: [...]"

## Mobile/Responsive Testing

- [ ] Test on mobile viewport (Chrome DevTools)
- [ ] Menu should be collapsible/hamburger
- [ ] Buttons should be touch-friendly
- [ ] Permission checks work same as desktop

## Regression Testing

After implementing permission system, verify:
- [ ] Existing features still work
- [ ] Forms submit correctly
- [ ] Data loads properly
- [ ] No broken functionality

## Final Sign-Off Checklist

- [ ] All navigation menu items filter correctly
- [ ] All action buttons show/hide based on permissions
- [ ] API returns 403 when permission missing
- [ ] No console errors on any page
- [ ] Logout clears permissions properly
- [ ] Page refresh maintains correct state
- [ ] Super admin has full access
- [ ] Employee has minimal access
- [ ] Backend tests pass (6/6)

---

**Ready for Production**: When all items in Final Sign-Off Checklist are ✅
