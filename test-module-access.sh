#!/bin/bash

# Module Access Control Test Script
# Tests permission-based access control for all routes

echo "🔐 Testing Module Access Control"
echo "================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test accounts
SUPER_ADMIN_EMAIL="admin@hrms.com"
SECTION_HEAD_EMAIL="manager@hrms.com"
EMPLOYEE_EMAIL="employee@hrms.com"
PASSWORD="password"

BASE_URL="http://localhost:8001/api"

echo "📋 Test Accounts:"
echo "  1. Super Admin: $SUPER_ADMIN_EMAIL"
echo "  2. Section Head: $SECTION_HEAD_EMAIL"
echo "  3. Employee: $EMPLOYEE_EMAIL"
echo ""

# Function to login and get token
login() {
    local email=$1
    local password=$2
    
    response=$(curl -s -X POST "$BASE_URL/login" \
        -H "Content-Type: application/json" \
        -d "{\"email\":\"$email\",\"password\":\"$password\"}")
    
    token=$(echo $response | jq -r '.token')
    echo $token
}

# Function to get permissions
get_permissions() {
    local token=$1
    
    response=$(curl -s -X GET "$BASE_URL/my-permissions" \
        -H "Authorization: Bearer $token")
    
    echo $response | jq -r '.data.allowed_modules[]'
}

# Function to test access
test_module_access() {
    local role=$1
    local token=$2
    local module=$3
    local endpoint=$4
    
    response=$(curl -s -w "\n%{http_code}" -X GET "$BASE_URL$endpoint" \
        -H "Authorization: Bearer $token")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" -eq 200 ]; then
        echo -e "${GREEN}✓${NC} $role can access $module (HTTP $http_code)"
        return 0
    elif [ "$http_code" -eq 403 ]; then
        echo -e "${RED}✗${NC} $role cannot access $module (HTTP $http_code)"
        return 1
    else
        echo -e "${YELLOW}?${NC} $role - $module returned HTTP $http_code"
        return 2
    fi
}

# Test Super Admin
echo "🔹 Testing Super Admin Access"
echo "-----------------------------"
admin_token=$(login "$SUPER_ADMIN_EMAIL" "$PASSWORD")

if [ "$admin_token" != "null" ] && [ -n "$admin_token" ]; then
    echo "Logged in as Super Admin"
    
    echo ""
    echo "Allowed Modules:"
    get_permissions "$admin_token"
    
    echo ""
    echo "Testing Module Access:"
    test_module_access "Super Admin" "$admin_token" "Employees" "/employees"
    test_module_access "Super Admin" "$admin_token" "Payroll" "/payroll"
    test_module_access "Super Admin" "$admin_token" "Departments" "/departments"
    test_module_access "Super Admin" "$admin_token" "Roles" "/roles"
    test_module_access "Super Admin" "$admin_token" "Loans" "/loans"
else
    echo -e "${RED}Failed to login as Super Admin${NC}"
fi

echo ""
echo ""

# Test Section Head
echo "🔹 Testing Section Head Access"
echo "------------------------------"
section_head_token=$(login "$SECTION_HEAD_EMAIL" "$PASSWORD")

if [ "$section_head_token" != "null" ] && [ -n "$section_head_token" ]; then
    echo "Logged in as Section Head"
    
    echo ""
    echo "Allowed Modules:"
    get_permissions "$section_head_token"
    
    echo ""
    echo "Testing Module Access:"
    test_module_access "Section Head" "$section_head_token" "Employees" "/employees"
    test_module_access "Section Head" "$section_head_token" "Leaves" "/leave-applications"
    test_module_access "Section Head" "$section_head_token" "Attendance" "/attendance"
    test_module_access "Section Head" "$section_head_token" "Payroll" "/payroll"
    test_module_access "Section Head" "$section_head_token" "Roles" "/roles"
else
    echo -e "${RED}Failed to login as Section Head${NC}"
fi

echo ""
echo ""

# Test Employee
echo "🔹 Testing Employee Access"
echo "-------------------------"
employee_token=$(login "$EMPLOYEE_EMAIL" "$PASSWORD")

if [ "$employee_token" != "null" ] && [ -n "$employee_token" ]; then
    echo "Logged in as Employee"
    
    echo ""
    echo "Allowed Modules:"
    get_permissions "$employee_token"
    
    echo ""
    echo "Testing Module Access:"
    test_module_access "Employee" "$employee_token" "Employees (Self)" "/employees"
    test_module_access "Employee" "$employee_token" "Leaves" "/leave-applications"
    test_module_access "Employee" "$employee_token" "Attendance" "/attendance"
    test_module_access "Employee" "$employee_token" "Payroll" "/payroll"
    test_module_access "Employee" "$employee_token" "Departments" "/departments"
    test_module_access "Employee" "$employee_token" "Roles" "/roles"
else
    echo -e "${RED}Failed to login as Employee${NC}"
fi

echo ""
echo ""
echo "================================"
echo "✅ Test Complete!"
echo ""
echo "📝 Summary:"
echo "  - Super Admin should have access to ALL modules"
echo "  - Section Head should have limited access (no admin modules)"
echo "  - Employee should have minimal access (self-service only)"
echo ""
echo "🌐 Frontend Testing:"
echo "  1. Open http://localhost:5173"
echo "  2. Login with different accounts"
echo "  3. Verify menu items match allowed modules"
echo "  4. Try accessing unauthorized URLs directly"
echo "  5. Confirm redirect to dashboard with error message"
echo ""
