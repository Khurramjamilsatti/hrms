# 🚀 HRMS - Quick API Reference

## Authentication
```bash
# Login
POST /api/login
Body: { "email": "admin@hrms.com", "password": "password" }

# Get current user
GET /api/me
Headers: Authorization: Bearer {token}

# Logout
POST /api/logout
Headers: Authorization: Bearer {token}
```

## 📊 Core Modules (Existing)

### Employees
- `GET /api/employees` - List all employees
- `POST /api/employees` - Create employee
- `GET /api/employees/{id}` - View employee
- `PUT /api/employees/{id}` - Update employee
- `DELETE /api/employees/{id}` - Delete employee

### Attendance
- `GET /api/attendance` - List attendance records
- `POST /api/attendance/check-in` - Check in
- `POST /api/attendance/check-out` - Check out
- `GET /api/attendance/summary` - Monthly summary

### Leave Management
- `GET /api/leave-applications` - List leave applications
- `POST /api/leave-applications` - Apply for leave
- `POST /api/leave-applications/{id}/approve` - Approve (Admin/Manager)
- `POST /api/leave-applications/{id}/reject` - Reject (Admin/Manager)

### Payroll
- `GET /api/payroll` - List payroll
- `POST /api/payroll/generate` - Generate monthly payroll (Admin/Manager)
- `POST /api/payroll/{id}/process` - Process payroll (Admin/Manager)
- `POST /api/payroll/{id}/mark-paid` - Mark as paid (Admin/Manager)

## 🆕 New Zoho HR Features

### ⏱️ Timesheets
```bash
# Projects
GET /api/timesheets/projects
POST /api/timesheets/projects
{
  "name": "Project Alpha",
  "manager_id": 1,
  "start_date": "2024-01-01",
  "budget": 500000
}

# Timesheets
GET /api/timesheets
POST /api/timesheets
{
  "employee_id": 1,
  "project_id": 1,
  "date": "2024-01-15",
  "start_time": "09:00",
  "end_time": "17:00",
  "description": "Worked on feature X"
}

POST /api/timesheets/{id}/submit
POST /api/timesheets/{id}/approve  # Manager only
```

### 🚀 Onboarding
```bash
# Templates
GET /api/onboarding/templates
POST /api/onboarding/templates
{
  "name": "Software Engineer Onboarding",
  "department_id": 1,
  "duration_days": 30
}

# Start onboarding for new hire
POST /api/onboarding/start
{
  "employee_id": 10,
  "template_id": 1,
  "start_date": "2024-02-01",
  "buddy_id": 5
}

# Complete task
POST /api/onboarding/tasks/{id}/complete
{
  "notes": "Task completed successfully"
}
```

### 📚 Training
```bash
# Courses
GET /api/training/courses
POST /api/training/courses
{
  "name": "Laravel Advanced",
  "type": "technical",
  "duration_hours": 16,
  "delivery_mode": "online",
  "cost": 50000
}

# Sessions
POST /api/training/sessions
{
  "course_id": 1,
  "session_name": "Laravel Q1 2024",
  "start_date": "2024-03-01",
  "end_date": "2024-03-15",
  "available_seats": 20
}

# Enroll employee
POST /api/training/enrollments
{
  "session_id": 1,
  "employee_id": 5
}

# Issue certificate
POST /api/training/enrollments/{id}/certificate
```

### ✈️ Travel & Expenses
```bash
# Travel Request
POST /api/travel-expenses/travel-requests
{
  "employee_id": 1,
  "purpose": "Client Meeting",
  "from_location": "Karachi",
  "to_location": "Lahore",
  "departure_date": "2024-02-15",
  "return_date": "2024-02-17",
  "travel_mode": "flight",
  "estimated_cost": 35000
}

POST /api/travel-expenses/travel-requests/{id}/submit
POST /api/travel-expenses/travel-requests/{id}/approve  # Manager

# Expense Claim
POST /api/travel-expenses/expense-claims
{
  "employee_id": 1,
  "travel_request_id": 1,
  "category_id": 1,
  "expense_date": "2024-02-15",
  "merchant_name": "Hotel XYZ",
  "description": "Hotel accommodation",
  "amount": 15000,
  "currency": "PKR"
}

POST /api/travel-expenses/expense-claims/{id}/approve  # Manager
POST /api/travel-expenses/expense-claims/{id}/mark-paid  # Manager

# Advance Request
POST /api/travel-expenses/advance-requests
{
  "employee_id": 1,
  "travel_request_id": 1,
  "purpose": "Travel advance",
  "amount": 20000,
  "required_date": "2024-02-10"
}
```

### 📅 Shift Scheduling
```bash
# Create Roster
POST /api/shift-scheduling/rosters
{
  "name": "February 2024 Roster",
  "department_id": 1,
  "start_date": "2024-02-01",
  "end_date": "2024-02-29"
}

# Assign Shifts
POST /api/shift-scheduling/assignments
{
  "roster_id": 1,
  "employee_id": 5,
  "shift_id": 1,
  "date": "2024-02-15",
  "start_time": "09:00",
  "end_time": "17:00"
}

# Bulk Assign
POST /api/shift-scheduling/assignments/bulk
{
  "roster_id": 1,
  "assignments": [
    { "employee_id": 5, "shift_id": 1, "date": "2024-02-15", ... },
    { "employee_id": 6, "shift_id": 2, "date": "2024-02-15", ... }
  ]
}

# Request Shift Swap
POST /api/shift-scheduling/swap-requests
{
  "requester_id": 5,
  "requester_assignment_id": 10,
  "swapper_id": 6,
  "reason": "Personal appointment"
}

POST /api/shift-scheduling/swap-requests/{id}/respond
{
  "response": "accept"
}

POST /api/shift-scheduling/swap-requests/{id}/approve  # Manager
```

### 🎫 Helpdesk
```bash
# Create Ticket
POST /api/helpdesk/tickets
{
  "employee_id": 1,
  "category_id": 1,
  "subject": "Salary slip issue",
  "description": "Unable to download salary slip",
  "priority": "high"
}

# Add Reply
POST /api/helpdesk/tickets/{id}/replies
{
  "message": "I have the same issue",
  "is_internal": false
}

# Assign (Manager)
POST /api/helpdesk/tickets/{id}/assign
{
  "assigned_to": 3
}

# Resolve (Manager)
POST /api/helpdesk/tickets/{id}/resolve
{
  "resolution_notes": "Issue resolved by updating database"
}

# Rate
POST /api/helpdesk/tickets/{id}/rate
{
  "rating": 5,
  "feedback": "Quick resolution!"
}
```

### 📁 Files
```bash
# Upload File
POST /api/files
Content-Type: multipart/form-data
{
  "employee_id": 1,
  "category_id": 1,
  "title": "Employment Contract",
  "description": "Signed contract",
  "file": <file>,
  "is_confidential": true,
  "expiry_date": "2025-12-31"
}

# Get Files
GET /api/files?employee_id=1&category_id=1

# Download
GET /api/files/{id}/download

# Upload New Version
POST /api/files/{id}/new-version
{
  "file": <file>,
  "description": "Updated contract"
}

# Access Logs
GET /api/files/{id}/access-logs
```

### 📆 Calendar
```bash
# Create Event
POST /api/calendar/events
{
  "title": "All Hands Meeting",
  "event_type": "company_event",
  "start_datetime": "2024-02-20 10:00:00",
  "end_datetime": "2024-02-20 11:30:00",
  "location": "Conference Room A",
  "meeting_link": "https://meet.google.com/abc",
  "attendees": [1, 2, 3, 4, 5]
}

# Respond to Event
POST /api/calendar/events/{id}/respond
{
  "status": "accepted",
  "response_note": "Will attend"
}

# Add Reminder
POST /api/calendar/events/{id}/reminders
{
  "remind_before_minutes": 30
}

# My Events
GET /api/calendar/my-events?start_date=2024-02-01&end_date=2024-02-29
```

### 🔔 Notifications
```bash
# Get Notifications
GET /api/notifications
GET /api/notifications?is_read=false

# Unread Count
GET /api/notifications/unread-count

# Mark as Read
POST /api/notifications/{id}/mark-read
POST /api/notifications/mark-all-read

# Preferences
GET /api/notifications/preferences
POST /api/notifications/preferences
{
  "notification_type": "email",
  "enabled_events": ["leave_approved", "payroll_generated", "training_enrolled"]
}
```

### 🏢 Organization
```bash
# Organization Chart
GET /api/organization/chart

# Hierarchy
GET /api/organization/hierarchy

# Department Stats
GET /api/organization/department-stats

# Employee Directory
GET /api/organization/directory?search=john&department_id=1

# Team Members
GET /api/organization/team/{managerId}
```

---

## 🔐 Role-Based Access

### Admin
- Full access to all endpoints
- Can create/update/delete all resources
- Approvals for all requests

### Manager
- Department/team management
- Approve/reject workflows
- View team reports
- Cannot access other departments (in some modules)

### Employee
- Self-service features
- View own records only
- Submit requests (leave, travel, expenses, tickets)
- Cannot approve/reject

---

## 📊 Common Query Parameters

### Pagination
```bash
GET /api/employees?page=1&per_page=20
```

### Filtering
```bash
GET /api/attendance?date=2024-02-15
GET /api/leave-applications?status=pending
GET /api/timesheets?project_id=1&status=approved
```

### Search
```bash
GET /api/organization/directory?search=john
GET /api/employees?search=developer
```

### Date Ranges
```bash
GET /api/calendar/my-events?start_date=2024-02-01&end_date=2024-02-29
GET /api/timesheets/summary?month=2&year=2024
```

---

## 🎯 HTTP Status Codes

- `200 OK` - Successful GET, PUT
- `201 Created` - Successful POST
- `400 Bad Request` - Validation error
- `401 Unauthorized` - Not authenticated
- `403 Forbidden` - Not authorized (wrong role)
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed
- `500 Internal Server Error` - Server error

---

## 💡 Response Format

### Success
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  ...
}
```

### Success with Pagination
```json
{
  "current_page": 1,
  "data": [...],
  "first_page_url": "http://localhost:8001/api/employees?page=1",
  "from": 1,
  "last_page": 5,
  "per_page": 15,
  "to": 15,
  "total": 75
}
```

### Error
```json
{
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

## 🔧 Development Commands

```bash
# Run migrations
php artisan migrate

# Check migration status
php artisan migrate:status

# List all routes
php artisan route:list

# Start Laravel server
php artisan serve --port=8001

# Start Vue dev server
npm run dev
```

---

## 📝 Testing with Postman/cURL

### Get Token
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@hrms.com","password":"password"}'
```

### Use Token
```bash
curl -X GET http://localhost:8001/api/employees \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create Resource
```bash
curl -X POST http://localhost:8001/api/timesheets \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "employee_id": 1,
    "project_id": 1,
    "date": "2024-02-15",
    "start_time": "09:00",
    "end_time": "17:00"
  }'
```

---

## 🎉 Ready to Use!

**All 210+ API endpoints are ready and functional!**

**Backend:** ✅ 100% Complete
**Frontend:** ⏳ Needs Vue components

See [ZOHO-HR-FEATURES.md](ZOHO-HR-FEATURES.md) for complete feature documentation.
