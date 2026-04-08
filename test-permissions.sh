#!/bin/bash

echo "================================"
echo "Permission System Test Script"
echo "================================"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Base URL
BASE_URL="http://localhost:8001/api"

echo "Testing Permission System..."
echo ""

# Test 1: Login and get token
echo "${YELLOW}Test 1: Login as admin${NC}"
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@hrms.com",
    "password": "password"
  }')

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
  echo "${RED}✗ Login failed${NC}"
  echo "Response: $LOGIN_RESPONSE"
  exit 1
else
  echo "${GREEN}✓ Login successful${NC}"
  echo "Token: ${TOKEN:0:20}..."
fi

echo ""

# Test 2: Fetch user permissions
echo "${YELLOW}Test 2: Fetch my permissions${NC}"
PERMISSIONS_RESPONSE=$(curl -s -X GET "$BASE_URL/my-permissions" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN")

PERMISSION_COUNT=$(echo $PERMISSIONS_RESPONSE | grep -o '"permissions":\[' | wc -l)

if [ $PERMISSION_COUNT -gt 0 ]; then
  echo "${GREEN}✓ Permissions fetched successfully${NC}"
  echo "Response preview:"
  echo $PERMISSIONS_RESPONSE | head -c 500
  echo "..."
else
  echo "${RED}✗ Failed to fetch permissions${NC}"
  echo "Response: $PERMISSIONS_RESPONSE"
fi

echo ""

# Test 3: Check permission endpoint
echo "${YELLOW}Test 3: Check specific permission${NC}"
CHECK_RESPONSE=$(curl -s -X POST "$BASE_URL/check-permission" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"permission": "employees.view"}')

HAS_PERMISSION=$(echo $CHECK_RESPONSE | grep -o '"has_permission":[^,}]*' | cut -d':' -f2)

if [ "$HAS_PERMISSION" = "true" ]; then
  echo "${GREEN}✓ Permission check working${NC}"
else
  echo "${RED}✗ Permission check failed${NC}"
  echo "Response: $CHECK_RESPONSE"
fi

echo ""

# Test 4: Access protected endpoint with permission
echo "${YELLOW}Test 4: Access employees endpoint (with permission)${NC}"
EMPLOYEES_RESPONSE=$(curl -s -X GET "$BASE_URL/employees?page=1&perPage=5" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN")

if echo "$EMPLOYEES_RESPONSE" | grep -q '"data"'; then
  echo "${GREEN}✓ Employees endpoint accessible${NC}"
else
  echo "${RED}✗ Failed to access employees endpoint${NC}"
  echo "Response: $EMPLOYEES_RESPONSE"
fi

echo ""

# Test 5: Get departments
echo "${YELLOW}Test 5: Access departments endpoint${NC}"
DEPARTMENTS_RESPONSE=$(curl -s -X GET "$BASE_URL/departments" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN")

if echo "$DEPARTMENTS_RESPONSE" | grep -q '"data"\|"id"' ]; then
  echo "${GREEN}✓ Departments endpoint accessible${NC}"
else
  echo "${RED}✗ Failed to access departments endpoint${NC}"
  echo "Response: $DEPARTMENTS_RESPONSE"
fi

echo ""

# Test 6: Check role management (super admin only)
echo "${YELLOW}Test 6: Access roles endpoint (super admin)${NC}"
ROLES_RESPONSE=$(curl -s -X GET "$BASE_URL/roles" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN")

if echo "$ROLES_RESPONSE" | grep -q '"data"\|"roles"' ; then
  echo "${GREEN}✓ Roles endpoint accessible (super admin)${NC}"
else
  echo "${RED}✗ Failed to access roles endpoint${NC}"
  echo "Response: $ROLES_RESPONSE"
fi

echo ""
echo "================================"
echo "Test Summary"
echo "================================"

echo ""
echo "Testing complete!"
echo ""
echo "Next steps:"
echo "1. Test login in browser at http://localhost:5173/login"
echo "2. Verify permissions load after login"
echo "3. Check navigation menu filters correctly"
echo "4. Test create/edit/delete buttons visibility"
echo ""
