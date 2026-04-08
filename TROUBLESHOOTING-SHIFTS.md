# 🔧 Shift Assignment Not Working - SOLUTION

## ❓ Problem
When clicking the purple "+" button to assign employees to shifts, nothing happens.

## ✅ Root Cause
**You're accessing the wrong URL!**

## 🎯 SOLUTION

### Step 1: Use the Correct URL
**❌ WRONG:** `http://localhost:8003/shifts`  
**✅ CORRECT:** `http://localhost:5173/shifts`

The Laravel server (port 8003) serves the backend API only. For development with hot-reload, you **MUST** use the Vite dev server (port 5173).

### Step 2: Clear Browser Cache
Press `Cmd+Shift+R` (Mac) or `Ctrl+Shift+R` (Windows/Linux) to hard refresh

### Step 3: Login as Admin/Manager
```
Email: admin@hrms.com
Password: password
```

Only **Admin** and **Manager** roles can assign employees to shifts.

### Step 4: Navigate to Shifts
Go to: `http://localhost:5173/shifts`

### Step 5: Test the Assignment Feature
1. You should see a list of shifts
2. Each shift has a purple **"+"** button in the Actions column
3. Click the purple button
4. A large modal should appear with 3 tabs
5. Select dates and employees
6. Click "Assign X Employee(s)"

## 🐛 Still Not Working? Check These:

### 1. Check Both Servers Are Running

**Laravel (Backend API):**
```bash
php artisan serve --port=8003
```

**Vite (Frontend Dev Server):**
```bash
npm run dev
```

Both should be running simultaneously in separate terminal windows.

### 2. Check Browser Console for Errors
1. Press `F12` to open Developer Tools
2. Go to **Console** tab
3. Look for red error messages
4. Common errors:
   - Module not found → Run `npm install`
   - API 401 → You're not logged in
   - API 403 → Wrong role (need admin/manager)

### 3. Check Network Tab
1. Open Developer Tools (`F12`)
2. Go to **Network** tab
3. Click the purple "+" button
4. Look for API calls to `/api/shifts/{id}/available-employees`
5. Check the response:
   - **200 OK** → Working correctly
   - **401 Unauthorized** → Not logged in
   - **403 Forbidden** → Wrong role
   - **404 Not Found** → API route missing

### 4. Verify You See the Modal
When you click the purple "+" button, you should see:
- Dark overlay covering the page
- Large white modal window
- "Assign Employees to Shift" header with purple gradient
- Three tabs: "Assign New Employees", "Current Assignments", "Assignment History"

If you **don't** see this modal:
1. Check browser console for Vue errors
2. Verify you're on `localhost:5173` (NOT 8003)
3. Clear cache and hard refresh

### 5. Check Your Role
Only Admin and Manager can access shift assignments.

To check your role:
```bash
# In terminal
php artisan tinker

# Then in tinker:
$user = User::where('email', 'your@email.com')->first();
echo $user->role;
```

Should return: `admin` or `manager`

## 📊 Quick Diagnostic Command

Run this to check everything:
```bash
./test-shift-assignment.sh
```

## 🔄 Complete Reset (If All Else Fails)

```bash
# Stop all servers
pkill -f "php artisan serve"
pkill -f "vite"

# Clear caches
rm -rf node_modules/.vite
rm -rf public/build

# Rebuild
npm run build

# Restart servers
php artisan serve --port=8003 &
npm run dev
```

Then:
1. Hard refresh browser (`Cmd+Shift+R`)
2. Go to `http://localhost:5173/login`
3. Login as admin
4. Go to `http://localhost:5173/shifts`
5. Click purple "+" button

## 📝 Common Mistakes

### Mistake 1: Wrong Port
**Issue:** Using `localhost:8003` instead of `localhost:5173`  
**Fix:** Always use `localhost:5173` for development

### Mistake 2: Not Logged In
**Issue:** Trying to access protected endpoints without authentication  
**Fix:** Login first at `http://localhost:5173/login`

### Mistake 3: Wrong Role
**Issue:** Logged in as "employee" instead of "admin" or "manager"  
**Fix:** Login with admin@hrms.com or manager@hrms.com

### Mistake 4: Old Cache
**Issue:** Browser showing old JavaScript without new components  
**Fix:** Hard refresh with `Cmd+Shift+R`

### Mistake 5: Dev Server Not Running
**Issue:** Vite dev server stopped or crashed  
**Fix:** Check terminal, restart with `npm run dev`

## ✨ Expected Behavior

### When Working Correctly:

1. **Click Purple "+" Button**
   - Modal appears immediately
   - Dark overlay covers page
   - Modal slides in smoothly

2. **Assign New Employees Tab**
   - Date pickers visible
   - Employee list loads (may take 1-2 seconds)
   - Can search employees
   - Can select multiple employees (purple background)

3. **Assign Button**
   - Shows "Assign X Employee(s)" where X is count
   - Disabled if no employees selected
   - Shows "Assigning..." during API call
   - Shows success message after completion

4. **Current Assignments Tab**
   - Lists all employees assigned to shift
   - Shows dates and employee details
   - Red trash icon to remove

5. **Assignment History Tab**
   - Shows all past and present assignments
   - Green badge for active, gray for completed

## 📞 Still Having Issues?

If you've tried everything above and it still doesn't work:

1. **Check Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check Browser Console:**
   - Look for Vue warnings
   - Look for network errors
   - Look for module loading errors

3. **Verify Database:**
   ```bash
   php artisan tinker
   DB::table('shifts')->count()
   DB::table('employees')->count()
   ```

4. **Test API Directly:**
   ```bash
   # Get auth token first by logging in
   TOKEN="your_token_here"
   
   # Test shifts endpoint
   curl -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        http://localhost:8003/api/shifts
   ```

## 🎯 TL;DR - Quick Fix

```bash
# 1. Go to correct URL
open http://localhost:5173/shifts

# 2. If page is blank, check servers
lsof -ti:5173  # Should return a number
lsof -ti:8003  # Should return a number

# 3. If no numbers, start servers
php artisan serve --port=8003 &
npm run dev

# 4. Hard refresh browser
# Press Cmd+Shift+R

# 5. Login
# admin@hrms.com / password

# 6. Try purple "+" button again
```

---

**System is fully operational.** If following these steps doesn't work, there may be a system-specific issue with your environment. Check Node.js version (should be 18+) and PHP version (should be 8.2+).
