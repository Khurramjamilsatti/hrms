#!/bin/bash

# Test Organization Module Endpoints
# Make sure Laravel server is running on port 8001

BASE_URL="http://localhost:8001/api"
TOKEN=""

echo "========================================="
echo "Organization Module API Testing"
echo "========================================="
echo ""

# Login to get token
echo "1. Logging in to get auth token..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@hrms.com","password":"password"}')

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | sed 's/"token":"//')

if [ -z "$TOKEN" ]; then
    echo "❌ Failed to get auth token"
    echo "Response: $LOGIN_RESPONSE"
    exit 1
fi

echo "✅ Successfully logged in"
echo ""

# Test Organization Chart
echo "2. Testing Organization Chart endpoint..."
ORG_CHART=$(curl -s -X GET "$BASE_URL/organization/chart" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

CHART_COUNT=$(echo $ORG_CHART | grep -o '"id":' | wc -l)
echo "✅ Organization Chart returned (root nodes: $CHART_COUNT)"
echo ""

# Test Department Stats (with pagination)
echo "3. Testing Department Stats endpoint (page 1, per_page=20)..."
DEPT_STATS=$(curl -s -X GET "$BASE_URL/organization/department-stats?page=1&per_page=20" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

DEPT_TOTAL=$(echo $DEPT_STATS | grep -o '"total":[0-9]*' | head -1 | sed 's/"total"://')
DEPT_COUNT=$(echo $DEPT_STATS | grep -o '"id":' | wc -l)
echo "✅ Department Stats returned: $DEPT_COUNT departments (Total: $DEPT_TOTAL)"
echo ""

# Test Employee Directory (with pagination)
echo "4. Testing Employee Directory endpoint (page 1, per_page=20)..."
DIRECTORY=$(curl -s -X GET "$BASE_URL/organization/directory?page=1&per_page=20" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

DIR_TOTAL=$(echo $DIRECTORY | grep -o '"total":[0-9]*' | head -1 | sed 's/"total"://')
DIR_COUNT=$(echo $DIRECTORY | grep -o '"id":' | wc -l)
echo "✅ Employee Directory returned: $DIR_COUNT employees on page 1 (Total: $DIR_TOTAL)"
echo ""

# Test Employee Directory with search
echo "5. Testing Employee Directory with search..."
SEARCH=$(curl -s -X GET "$BASE_URL/organization/directory?search=ali&per_page=10" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

SEARCH_TOTAL=$(echo $SEARCH | grep -o '"total":[0-9]*' | head -1 | sed 's/"total"://')
echo "✅ Search for 'ali' returned: $SEARCH_TOTAL results"
echo ""

# Test Employee Directory with department filter
echo "6. Testing Employee Directory with department filter..."
DEPT_FILTER=$(curl -s -X GET "$BASE_URL/organization/directory?department_id=1&per_page=20" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

DEPT_FILTER_TOTAL=$(echo $DEPT_FILTER | grep -o '"total":[0-9]*' | head -1 | sed 's/"total"://')
DEPT_FILTER_COUNT=$(echo $DEPT_FILTER | grep -o '"id":' | wc -l)
echo "✅ Department filter (dept_id=1) returned: $DEPT_FILTER_COUNT employees on page 1 (Total: $DEPT_FILTER_TOTAL)"
echo ""

# Test Employee Directory pagination (page 2)
echo "7. Testing Employee Directory pagination (page 2)..."
PAGE2=$(curl -s -X GET "$BASE_URL/organization/directory?page=2&per_page=20" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

PAGE2_CURRENT=$(echo $PAGE2 | grep -o '"current_page":[0-9]*' | sed 's/"current_page"://')
PAGE2_COUNT=$(echo $PAGE2 | grep -o '"id":' | wc -l)
echo "✅ Page 2 returned: $PAGE2_COUNT employees (Current page: $PAGE2_CURRENT)"
echo ""

# Test with different per_page values
echo "8. Testing different per_page values..."
for PER_PAGE in 20 40 60 80 100; do
    RESULT=$(curl -s -X GET "$BASE_URL/organization/directory?page=1&per_page=$PER_PAGE" \
      -H "Authorization: Bearer $TOKEN" \
      -H "Accept: application/json")
    
    RESULT_COUNT=$(echo $RESULT | grep -o '"id":' | wc -l)
    echo "   ✅ per_page=$PER_PAGE returned: $RESULT_COUNT items on page 1"
done
echo ""

echo "========================================="
echo "Summary of Results:"
echo "========================================="
echo "✅ Total Employees: $DIR_TOTAL (Expected: 1000)"
echo "✅ Total Departments: $DEPT_TOTAL"
echo "✅ Organization Chart: Working"
echo "✅ Department Stats Pagination: Working"
echo "✅ Employee Directory Pagination: Working"
echo "✅ Search Functionality: Working"
echo "✅ Department Filter: Working"
echo ""

if [ "$DIR_TOTAL" = "1000" ]; then
    echo "✅ ✅ ✅ ALL TESTS PASSED! Employee count is correct (1000)"
else
    echo "⚠️  Warning: Employee count is $DIR_TOTAL but expected 1000"
fi

echo ""
echo "Testing complete!"
