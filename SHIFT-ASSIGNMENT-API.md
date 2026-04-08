# Shift Assignment API - Quick Reference

## Base URL
```
http://localhost:8003/api
```

## Authentication
All endpoints require Laravel Sanctum token authentication:
```
Authorization: Bearer {token}
```

Access restricted to: **Admin, Manager** roles

---

## Endpoints

### 1. List Assigned Employees
Get current employees assigned to a shift

**Endpoint**: `GET /shifts/{shift_id}/assignments`

**Query Parameters**:
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)

**Response**:
```json
{
  "data": [
    {
      "id": 1,
      "shift_id": 5,
      "employee_id": 123,
      "effective_from": "2024-01-01",
      "effective_to": null,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "employee": {
        "id": 123,
        "employee_code": "EMP001",
        "first_name": "John",
        "last_name": "Doe",
        "department": {
          "id": 1,
          "name": "IT Department"
        },
        "designation": {
          "id": 1,
          "title": "Software Engineer"
        }
      }
    }
  ],
  "current_page": 1,
  "per_page": 15,
  "total": 25
}
```

---

### 2. Assign Single Employee
Assign one employee to a shift with conflict detection

**Endpoint**: `POST /shifts/{shift_id}/assignments`

**Request Body**:
```json
{
  "employee_id": 123,
  "effective_from": "2024-01-01",
  "effective_to": "2024-12-31"
}
```

**Fields**:
- `employee_id` (required): Employee ID to assign
- `effective_from` (required): Start date (YYYY-MM-DD)
- `effective_to` (optional): End date (YYYY-MM-DD). Null for permanent assignment

**Success Response** (201):
```json
{
  "message": "Employee successfully assigned to shift",
  "assignment": {
    "id": 1,
    "shift_id": 5,
    "employee_id": 123,
    "effective_from": "2024-01-01",
    "effective_to": "2024-12-31",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

**Conflict Response** (409):
```json
{
  "message": "Employee is already assigned to another shift during this period",
  "conflict": {
    "shift_id": 3,
    "shift_name": "Evening Shift",
    "effective_from": "2023-12-01",
    "effective_to": null
  }
}
```

**Validation Errors** (422):
```json
{
  "message": "The given data was invalid",
  "errors": {
    "employee_id": ["The employee id field is required."],
    "effective_from": ["The effective from field is required."]
  }
}
```

---

### 3. Bulk Assign Employees
Assign multiple employees with individual conflict tracking

**Endpoint**: `POST /shifts/{shift_id}/assignments/bulk`

**Request Body**:
```json
{
  "employee_ids": [123, 456, 789],
  "effective_from": "2024-01-01",
  "effective_to": "2024-12-31"
}
```

**Fields**:
- `employee_ids` (required): Array of employee IDs
- `effective_from` (required): Start date (YYYY-MM-DD)
- `effective_to` (optional): End date (YYYY-MM-DD)

**Success Response** (200):
```json
{
  "message": "Processed 3 assignments: 2 successful, 1 conflict, 0 errors",
  "assignments": [
    {
      "id": 1,
      "shift_id": 5,
      "employee_id": 123,
      "effective_from": "2024-01-01",
      "effective_to": "2024-12-31"
    },
    {
      "id": 2,
      "shift_id": 5,
      "employee_id": 456,
      "effective_from": "2024-01-01",
      "effective_to": "2024-12-31"
    }
  ],
  "conflicts": [
    {
      "employee_id": 789,
      "employee_name": "Jane Smith",
      "conflict": {
        "shift_id": 3,
        "shift_name": "Evening Shift",
        "effective_from": "2023-12-01",
        "effective_to": null
      }
    }
  ],
  "errors": [],
  "success_count": 2,
  "conflict_count": 1,
  "error_count": 0
}
```

---

### 4. Remove Assignment
Remove an employee from a shift

**Endpoint**: `DELETE /shifts/{shift_id}/assignments/{assignment_id}`

**Success Response** (200):
```json
{
  "message": "Employee assignment removed successfully"
}
```

**Not Found Response** (404):
```json
{
  "message": "Assignment not found or does not belong to this shift"
}
```

---

### 5. Update Assignment
Modify assignment dates with conflict re-validation

**Endpoint**: `PUT /shifts/{shift_id}/assignments/{assignment_id}`

**Request Body**:
```json
{
  "effective_from": "2024-02-01",
  "effective_to": "2024-11-30"
}
```

**Success Response** (200):
```json
{
  "message": "Assignment updated successfully",
  "assignment": {
    "id": 1,
    "shift_id": 5,
    "employee_id": 123,
    "effective_from": "2024-02-01",
    "effective_to": "2024-11-30",
    "updated_at": "2024-01-15T10:30:00.000000Z"
  }
}
```

**Conflict Response** (409):
```json
{
  "message": "The updated dates conflict with another shift assignment"
}
```

---

### 6. Get Available Employees
List employees not assigned to any shift during specified period

**Endpoint**: `GET /shifts/{shift_id}/available-employees`

**Query Parameters**:
- `effective_from` (required): Start date (YYYY-MM-DD)
- `effective_to` (optional): End date (YYYY-MM-DD)
- `search` (optional): Search by name or employee code
- `page` (optional): Page number
- `per_page` (optional): Items per page (default: 50)

**Example**:
```
GET /shifts/5/available-employees?effective_from=2024-01-01&effective_to=2024-12-31&search=john
```

**Response**:
```json
{
  "data": [
    {
      "id": 123,
      "employee_code": "EMP001",
      "first_name": "John",
      "last_name": "Doe",
      "email": "john.doe@company.com",
      "status": "active",
      "department": {
        "id": 1,
        "name": "IT Department"
      },
      "designation": {
        "id": 1,
        "title": "Software Engineer"
      }
    }
  ],
  "current_page": 1,
  "per_page": 50,
  "total": 15
}
```

---

### 7. Get Assignment History
View all past, present, and future assignments

**Endpoint**: `GET /shifts/{shift_id}/assignment-history`

**Query Parameters**:
- `page` (optional): Page number
- `per_page` (optional): Items per page (default: 20)

**Response**:
```json
{
  "data": [
    {
      "id": 1,
      "shift_id": 5,
      "employee_id": 123,
      "effective_from": "2023-01-01",
      "effective_to": "2023-12-31",
      "created_at": "2023-01-01T00:00:00.000000Z",
      "employee": {
        "id": 123,
        "employee_code": "EMP001",
        "first_name": "John",
        "last_name": "Doe",
        "department": {
          "id": 1,
          "name": "IT Department"
        }
      }
    },
    {
      "id": 2,
      "shift_id": 5,
      "employee_id": 456,
      "effective_from": "2024-01-01",
      "effective_to": null,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "employee": {
        "id": 456,
        "employee_code": "EMP002",
        "first_name": "Jane",
        "last_name": "Smith"
      }
    }
  ],
  "current_page": 1,
  "per_page": 20,
  "total": 45
}
```

---

## Conflict Detection Logic

### Date Range Overlap Detection
The system checks if the new assignment's date range overlaps with any existing assignments:

```sql
WHERE employee_id = :employee_id
  AND shift_id != :current_shift_id
  AND (
    -- Case 1: New assignment starts during existing assignment
    (:effective_from BETWEEN effective_from AND COALESCE(effective_to, '9999-12-31'))
    OR
    -- Case 2: New assignment ends during existing assignment
    (COALESCE(:effective_to, '9999-12-31') BETWEEN effective_from AND COALESCE(effective_to, '9999-12-31'))
    OR
    -- Case 3: New assignment completely contains existing assignment
    (:effective_from <= effective_from AND COALESCE(:effective_to, '9999-12-31') >= COALESCE(effective_to, '9999-12-31'))
  )
```

### Conflict Scenarios

**Scenario 1: Permanent Assignment Conflict**
```
Existing: Shift A (2024-01-01 to NULL)
New:      Shift B (2024-06-01 to 2024-12-31)
Result:   CONFLICT - Overlaps with permanent assignment
```

**Scenario 2: Partial Overlap**
```
Existing: Shift A (2024-01-01 to 2024-06-30)
New:      Shift B (2024-05-01 to 2024-08-31)
Result:   CONFLICT - Overlaps from May to June
```

**Scenario 3: No Overlap (Valid)**
```
Existing: Shift A (2024-01-01 to 2024-06-30)
New:      Shift B (2024-07-01 to 2024-12-31)
Result:   SUCCESS - No overlap
```

**Scenario 4: Same Shift Update (Valid)**
```
Existing: Shift A (2024-01-01 to 2024-12-31) - Assignment ID 123
Update:   Shift A (2024-02-01 to 2024-11-30) - Assignment ID 123
Result:   SUCCESS - Same assignment, dates modified
```

---

## Error Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Missing or invalid token |
| 403 | Forbidden - Insufficient role permissions |
| 404 | Not Found - Shift or assignment doesn't exist |
| 409 | Conflict - Date range overlap detected |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Internal Server Error |

---

## Testing Examples

### cURL Commands

**Assign Single Employee**:
```bash
curl -X POST http://localhost:8003/api/shifts/5/assignments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "employee_id": 123,
    "effective_from": "2024-01-01",
    "effective_to": "2024-12-31"
  }'
```

**Bulk Assign**:
```bash
curl -X POST http://localhost:8003/api/shifts/5/assignments/bulk \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "employee_ids": [123, 456, 789],
    "effective_from": "2024-01-01",
    "effective_to": null
  }'
```

**Get Available Employees**:
```bash
curl -X GET "http://localhost:8003/api/shifts/5/available-employees?effective_from=2024-01-01&search=john" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Remove Assignment**:
```bash
curl -X DELETE http://localhost:8003/api/shifts/5/assignments/123 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Performance Considerations

### Database Indexes
Ensure these indexes exist for optimal performance:

```sql
-- employee_shifts table
CREATE INDEX idx_employee_shifts_employee_id ON employee_shifts(employee_id);
CREATE INDEX idx_employee_shifts_shift_id ON employee_shifts(shift_id);
CREATE INDEX idx_employee_shifts_dates ON employee_shifts(effective_from, effective_to);
CREATE INDEX idx_employee_shifts_employee_dates ON employee_shifts(employee_id, effective_from, effective_to);
```

### Query Optimization
- Pagination is enforced on all list endpoints
- Conflict detection uses indexed queries
- Bulk operations use database transactions

### Recommended Limits
- Bulk assignment: Max 100 employees per request
- Pagination: 15-50 items per page
- History retention: Unlimited (use pagination)

---

## Migration SQL

```sql
-- employee_shifts table (already exists from migration)
CREATE TABLE employee_shifts (
    id BIGSERIAL PRIMARY KEY,
    shift_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Add indexes
CREATE INDEX idx_employee_shifts_employee_id ON employee_shifts(employee_id);
CREATE INDEX idx_employee_shifts_shift_id ON employee_shifts(shift_id);
CREATE INDEX idx_employee_shifts_dates ON employee_shifts(effective_from, effective_to);
```

---

## Support & Contact
- **Backend**: Laravel 11, PostgreSQL
- **API Version**: 1.0
- **Last Updated**: December 2024
