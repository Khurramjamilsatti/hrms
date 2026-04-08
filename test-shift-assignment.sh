#!/bin/bash

# HRMS Shift Assignment - Troubleshooting Script

echo "🔍 HRMS Shift Assignment Troubleshooting"
echo "========================================"
echo ""

# Check if Laravel server is running
echo "1. Checking Laravel server..."
if lsof -ti:8003 > /dev/null 2>&1; then
    echo "   ✅ Laravel server is running on port 8003"
else
    echo "   ❌ Laravel server is NOT running on port 8003"
    echo "   👉 Start it with: php artisan serve --port=8003"
fi

# Check if Vite dev server is running
echo ""
echo "2. Checking Vite dev server..."
if lsof -ti:5173 > /dev/null 2>&1; then
    echo "   ✅ Vite dev server is running on port 5173"
elif lsof -ti:5174 > /dev/null 2>&1; then
    echo "   ✅ Vite dev server is running on port 5174"
else
    echo "   ❌ Vite dev server is NOT running"
    echo "   👉 Start it with: npm run dev"
fi

# Test API connectivity
echo ""
echo "3. Testing API connectivity..."
response=$(curl -s -o /dev/null -w "%{http_code}" -H "Accept: application/json" http://localhost:8003/api/shifts)
if [ "$response" == "401" ]; then
    echo "   ✅ API is responding (requires authentication)"
elif [ "$response" == "200" ]; then
    echo "   ✅ API is responding successfully"
else
    echo "   ⚠️  API returned status code: $response"
fi

# Check if component files exist
echo ""
echo "4. Checking component files..."
if [ -f "resources/js/views/shifts/ShiftManagement.vue" ]; then
    echo "   ✅ ShiftManagement.vue exists"
else
    echo "   ❌ ShiftManagement.vue NOT found"
fi

if [ -f "resources/js/views/shifts/ShiftAssignments.vue" ]; then
    echo "   ✅ ShiftAssignments.vue exists"
else
    echo "   ❌ ShiftAssignments.vue NOT found"
fi

# Check ShiftController
echo ""
echo "5. Checking ShiftController..."
if [ -f "app/Http/Controllers/Api/ShiftController.php" ]; then
    methods=$(grep -c "public function" app/Http/Controllers/Api/ShiftController.php)
    echo "   ✅ ShiftController.php exists ($methods methods)"
else
    echo "   ❌ ShiftController.php NOT found"
fi

# Build assets
echo ""
echo "6. Building fresh assets..."
npm run build > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ Assets built successfully"
else
    echo "   ❌ Build failed - check npm run build for errors"
fi

echo ""
echo "========================================"
echo "📋 NEXT STEPS:"
echo ""
echo "1. Clear browser cache (Cmd+Shift+R or Ctrl+Shift+R)"
echo "2. Login as admin or manager:"
echo "   - Email: admin@hrms.com"
echo "   - Password: password"
echo "3. Navigate to: http://localhost:5173/shifts"
echo "   (NOT http://localhost:8003/shifts)"
echo "4. Click the purple '+' button next to any shift"
echo ""
echo "If still not working:"
echo "- Open browser console (F12) and check for errors"
echo "- Check Network tab for failed API requests"
echo "- Verify you're logged in as admin/manager role"
echo ""
