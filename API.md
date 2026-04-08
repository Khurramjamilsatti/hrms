# HRMS API Documentation

Base URL: `http://localhost:8000/api`

All authenticated endpoints require the `Authorization: Bearer {token}` header.

## Authentication

### Login
```http
POST /login
Content-Type: application/json

{
  "email": "admin@hrms.com",
  "password": "password"
}

Response: 200 OK
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@hrms.com",
    "role": "admin"
  },
  "token": "1|abc123..."
}
```

### Logout
```http
POST /logout
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Logged out successfully"
}
```

### Get Current User
```http
GET /me
Authorization: Bearer {token}

Response: 200 OK
{
  "id": 1,
  "name": "Admin User",
  "email": "admin@hrms.com",
  "role": "admin",
  "employee": { ... }
}
```

## Dashboard

### Get Dashboard Data
```http
GET /dashboard
Authorization: Bearer {token}

Response: 200 OK
{
  "stats": {
    "total_employees": 50,
    "present_today": 45,
    "on_leave_today": 3,
    "total_payroll_this_month": "Rs. 2,500,000"
  },
  "recent_activities": [ ... ],
  "upcoming_leaves": [ ... ]
}
```

## Employees

### List Employees
```http
GET /employees?page=1&per_page=10&search=john&department_id=1
Authorization: Bearer {token}

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "employee_code": "EMP001",
      "full_name": "John Doe",
      "email": "john@hrms.com",
      "department": { "id": 1, "name": "IT" },
      "designation": { "id": 1, "title": "Developer" },
      "employment_status": "active"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 50
  }
}
```

### Get Employee Details
```http
GET /employees/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "id": 1,
  "employee_code": "EMP001",
  "full_name": "John Doe",
  "date_of_birth": "1990-01-15",
  "gender": "male",
  "phone": "+92300-1234567",
  "department": { ... },
  "designation": { ... },
  "salaries": [ ... ],
  "documents": [ ... ]
}
```

### Create Employee
```http
POST /employees
Authorization: Bearer {token}
Content-Type: application/json

{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@hrms.com",
  "phone": "+92300-1234567",
  "date_of_birth": "1990-01-15",
  "gender": "male",
  "address": "123 Street, Karachi",
  "department_id": 1,
  "designation_id": 1,
  "joining_date": "2024-01-01",
  "employment_type": "full_time",
  "employment_status": "active"
}

Response: 201 Created
```

### Update Employee
```http
PUT /employees/{id}
Authorization: Bearer {token}
Content-Type: application/json

Response: 200 OK
```

### Delete Employee
```http
DELETE /employees/{id}
Authorization: Bearer {token}

Response: 204 No Content
```

## Attendance

### List Attendance
```http
GET /attendance?month=12&year=2024&employee_id=1
Authorization: Bearer {token}

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "date": "2024-12-01",
      "check_in": "09:00:00",
      "check_out": "18:00:00",
      "working_hours": 8.0,
      "overtime_hours": 0.0,
      "status": "present"
    }
  ]
}
```

### Check In
```http
POST /attendance/check-in
Authorization: Bearer {token}
Content-Type: application/json

{
  "notes": "Optional check-in notes"
}

Response: 201 Created
{
  "id": 1,
  "check_in": "09:00:00",
  "message": "Checked in successfully"
}
```

### Check Out
```http
POST /attendance/check-out
Authorization: Bearer {token}
Content-Type: application/json

{
  "notes": "Optional check-out notes"
}

Response: 200 OK
{
  "id": 1,
  "check_out": "18:00:00",
  "working_hours": 8.0,
  "message": "Checked out successfully"
}
```

### Attendance Summary
```http
GET /attendance/summary?employee_id=1&month=12&year=2024
Authorization: Bearer {token}

Response: 200 OK
{
  "total_days": 30,
  "present_days": 25,
  "absent_days": 2,
  "leave_days": 3,
  "total_hours": 200,
  "overtime_hours": 10
}
```

## Leave Management

### List Leave Applications
```http
GET /leave-applications?status=pending&employee_id=1
Authorization: Bearer {token}

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "employee": { ... },
      "leave_type": { "id": 1, "name": "Annual Leave" },
      "start_date": "2024-12-15",
      "end_date": "2024-12-20",
      "total_days": 5,
      "reason": "Personal vacation",
      "status": "pending"
    }
  ]
}
```

### Apply for Leave
```http
POST /leave-applications
Authorization: Bearer {token}
Content-Type: application/json

{
  "leave_type_id": 1,
  "start_date": "2024-12-15",
  "end_date": "2024-12-20",
  "reason": "Personal vacation"
}

Response: 201 Created
```

### Approve Leave
```http
POST /leave-applications/{id}/approve
Authorization: Bearer {token}
Content-Type: application/json

{
  "approval_remarks": "Approved"
}

Response: 200 OK
```

### Reject Leave
```http
POST /leave-applications/{id}/reject
Authorization: Bearer {token}
Content-Type: application/json

{
  "approval_remarks": "Not enough staff"
}

Response: 200 OK
```

### Get Leave Balance
```http
GET /leave-applications/balance
Authorization: Bearer {token}

Response: 200 OK
{
  "balances": [
    {
      "leave_type": "Annual Leave",
      "total_days": 20,
      "used_days": 5,
      "remaining_days": 15
    }
  ]
}
```

## Payroll

### List Payroll
```http
GET /payroll?month=12&year=2024&employee_id=1
Authorization: Bearer {token}
Role: admin, manager

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "employee": { ... },
      "month": 12,
      "year": 2024,
      "basic_salary": 50000,
      "total_earnings": 55000,
      "total_deductions": 5000,
      "net_salary": 50000,
      "status": "paid"
    }
  ]
}
```

### Generate Payroll
```http
POST /payroll/generate
Authorization: Bearer {token}
Role: admin
Content-Type: application/json

{
  "month": 12,
  "year": 2024
}

Response: 201 Created
{
  "message": "Payroll generated successfully",
  "count": 50
}
```

### Process Payroll
```http
POST /payroll/{id}/process
Authorization: Bearer {token}
Role: admin

Response: 200 OK
{
  "message": "Payroll processed successfully"
}
```

### Get Payroll Details
```http
GET /payroll/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "id": 1,
  "employee": { ... },
  "basic_salary": 50000,
  "details": [
    {
      "component": "House Allowance",
      "type": "earning",
      "amount": 5000
    }
  ],
  "total_earnings": 55000,
  "total_deductions": 5000,
  "net_salary": 50000
}
```

## Departments

### List Departments
```http
GET /departments
Authorization: Bearer {token}

Response: 200 OK
[
  {
    "id": 1,
    "name": "Information Technology",
    "description": "IT Department",
    "manager_id": 2,
    "is_active": true,
    "employees_count": 15
  }
]
```

### Create Department
```http
POST /departments
Authorization: Bearer {token}
Role: admin
Content-Type: application/json

{
  "name": "Human Resources",
  "description": "HR Department",
  "manager_id": 3,
  "is_active": true
}

Response: 201 Created
```

## Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
  "message": "This action is unauthorized."
}
```

### Not Found (404)
```json
{
  "message": "Resource not found."
}
```

### Server Error (500)
```json
{
  "message": "Server Error",
  "error": "Error details..."
}
```

## Rate Limiting

API requests are limited to:
- 60 requests per minute for authenticated users
- 10 requests per minute for guest users

Rate limit headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
```

## Currency Format

All monetary values are in PKR (Pakistani Rupees).
- Format: `Rs. 50,000`
- API returns raw numbers: `50000`
- Frontend formats for display

## Date Format

- Input: `YYYY-MM-DD` (e.g., `2024-12-01`)
- Output: ISO 8601 format

## Pagination

List endpoints support pagination:
```
?page=1&per_page=10
```

Response includes meta:
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 100,
    "last_page": 10
  }
}
```

## Shift Management

### List Shifts
```http
GET /shifts?page=1&per_page=15&search=morning&status=active
Authorization: Bearer {token}
Role: admin, manager

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "name": "Morning Shift",
      "start_time": "09:00:00",
      "end_time": "17:00:00",
      "grace_period_minutes": 15,
      "is_active": true,
      "employee_shifts_count": 25
    }
  ],
  "meta": { ... }
}
```

### Get Shift Statistics
```http
GET /shifts/statistics
Authorization: Bearer {token}
Role: admin, manager

Response: 200 OK
{
  "total_shifts": 4,
  "active_shifts": 3,
  "inactive_shifts": 1,
  "total_assigned_employees": 45
}
```

### Employee Shift Assignments

#### List Assigned Employees
```http
GET /shifts/{shift_id}/assignments
Authorization: Bearer {token}
Role: admin, manager

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "shift_id": 1,
      "employee_id": 123,
      "effective_from": "2024-01-01",
      "effective_to": null,
      "employee": {
        "id": 123,
        "employee_code": "EMP001",
        "first_name": "John",
        "last_name": "Doe",
        "department": { ... }
      }
    }
  ]
}
```

#### Assign Employee to Shift
```http
POST /shifts/{shift_id}/assignments
Authorization: Bearer {token}
Role: admin, manager
Content-Type: application/json

{
  "employee_id": 123,
  "effective_from": "2024-01-01",
  "effective_to": "2024-12-31"
}

Response: 201 Created (success)
or 409 Conflict (date overlap detected)
```

#### Bulk Assign Employees
```http
POST /shifts/{shift_id}/assignments/bulk
Authorization: Bearer {token}
Role: admin, manager
Content-Type: application/json

{
  "employee_ids": [123, 456, 789],
  "effective_from": "2024-01-01",
  "effective_to": null
}

Response: 200 OK
{
  "message": "Processed 3 assignments: 2 successful, 1 conflict, 0 errors",
  "assignments": [ ... ],
  "conflicts": [ ... ],
  "success_count": 2,
  "conflict_count": 1
}
```

#### Get Available Employees
```http
GET /shifts/{shift_id}/available-employees?effective_from=2024-01-01&effective_to=2024-12-31&search=john
Authorization: Bearer {token}
Role: admin, manager

Response: 200 OK
{
  "data": [
    // List of employees not assigned to any shift during specified period
  ]
}
```

#### Remove Assignment
```http
DELETE /shifts/{shift_id}/assignments/{assignment_id}
Authorization: Bearer {token}
Role: admin, manager

Response: 200 OK
```

#### Get Assignment History
```http
GET /shifts/{shift_id}/assignment-history
Authorization: Bearer {token}
Role: admin, manager

Response: 200 OK
{
  "data": [
    // All assignments (past, present, future)
  ]
}
```

## Filtering and Sorting

Most list endpoints support:
- `search`: Text search
- `sort_by`: Field to sort by
- `sort_order`: `asc` or `desc`
- Specific filters per resource

Example:
```
GET /employees?search=john&sort_by=name&sort_order=asc&department_id=1
```
