# Employee Shift Assignment System - User Guide

## Overview
The HRMS now features a comprehensive employee shift assignment system that follows universal HR Standard Operating Procedures (SOPs). This guide explains how to use the system effectively.

## Accessing Shift Assignments
1. Navigate to **Shift Management** (http://localhost:8003/shifts)
2. Locate the shift you want to manage
3. Click the **purple "Assign Employees" button** in the Actions column

## System Features

### 1. **Assign New Employees** Tab

#### Date Range Selection
- **Effective From** (Required): The start date when the assignment becomes active
- **Effective To** (Optional): The end date when the assignment expires
  - Leave empty for **permanent assignments** (no expiration)
  - Set a future date for **temporary/project-based assignments**

#### Employee Selection
- **Search Functionality**: Search employees by name or employee code
- **Multi-Select**: Click on employee cards to select multiple employees for bulk assignment
- **Visual Feedback**: Selected employees appear with purple background and checkmark
- **Available Only**: System automatically filters out employees already assigned to other shifts during the selected period

#### Auto-Conflict Detection
The system automatically prevents:
- **Double Booking**: Employees cannot be assigned to multiple shifts with overlapping date ranges
- **Date Overlap**: Conflicts are detected when assignment periods overlap
- **Permanent Conflict**: Existing permanent assignments block new assignments

#### Bulk Assignment
- Select multiple employees at once
- Assign all in one operation
- Get detailed results showing:
  - **Success**: Employees successfully assigned
  - **Conflicts**: Employees that couldn't be assigned due to conflicts
  - **Errors**: Any other issues encountered

### 2. **Current Assignments** Tab

#### View Active Assignments
- See all employees currently assigned to the shift
- View effective date ranges for each assignment
- Filter shows only current and future assignments

#### Assignment Details Display
For each assignment, you can see:
- Employee name and profile initial
- Employee code
- Department and designation
- Effective period (from date to end date or "Permanent")

#### Remove Assignments
- Click the **red trash icon** to remove an employee from the shift
- Confirmation dialog prevents accidental deletions
- Successful removal refreshes both current assignments and available employees

### 3. **Assignment History** Tab

#### Complete Assignment Records
- View ALL past, present, and future assignments
- Track assignment changes over time
- See completed assignments with clear status badges

#### Status Indicators
- **Active** (Green): Current or future assignments
- **Completed** (Gray): Past assignments that have expired

## Standard Operating Procedures (SOPs) Compliance

### 1. **Conflict Prevention**
✅ System automatically checks for date range overlaps  
✅ Prevents double-booking of employees  
✅ Real-time validation during assignment process

### 2. **Date Range Management**
✅ Support for both permanent and temporary assignments  
✅ Clear distinction between ongoing and time-bound assignments  
✅ Automatic expiration tracking

### 3. **Audit Trail**
✅ Complete assignment history maintained  
✅ Track all changes and modifications  
✅ View past assignments for compliance

### 4. **Bulk Operations**
✅ Efficient multi-employee assignment  
✅ Individual conflict tracking in bulk operations  
✅ Detailed success/failure reporting

### 5. **User-Friendly Interface**
✅ Visual feedback for selections  
✅ Clear error messages  
✅ Confirmation dialogs for destructive actions  
✅ Professional gradient UI design

### 6. **Data Integrity**
✅ Database-level validation  
✅ Transaction safety for bulk operations  
✅ Automatic rollback on errors

### 7. **Role-Based Access Control**
✅ Only Admins and Managers can assign employees  
✅ Protected API endpoints with middleware  
✅ Secure authentication using Laravel Sanctum

## Common Use Cases

### Use Case 1: Permanent Shift Assignment
**Scenario**: Assign a new employee to the Morning Shift permanently

1. Click "Assign Employees" on Morning Shift
2. Select today's date as "Effective From"
3. Leave "Effective To" empty
4. Search and select the employee
5. Click "Assign 1 Employee(s)"

**Result**: Employee is permanently assigned to Morning Shift starting today

### Use Case 2: Temporary Project Assignment
**Scenario**: Assign 5 employees to Night Shift for a 2-month project

1. Click "Assign Employees" on Night Shift
2. Set "Effective From" to project start date
3. Set "Effective To" to project end date (2 months later)
4. Search and select all 5 employees
5. Click "Assign 5 Employee(s)"

**Result**: All 5 employees assigned to Night Shift for the specified period. After the end date, they automatically become available for other shifts.

### Use Case 3: Replace Shift Assignment
**Scenario**: Move an employee from Evening Shift to Morning Shift

1. Go to Evening Shift → "Assign Employees" → "Current Assignments" tab
2. Remove the employee from Evening Shift
3. Go to Morning Shift → "Assign Employees" → "Assign New Employees" tab
4. Select the employee (now available)
5. Click "Assign 1 Employee(s)"

**Result**: Employee successfully moved from Evening to Morning Shift

### Use Case 4: Handle Assignment Conflicts
**Scenario**: Attempting to assign an employee who's already on another shift

1. Try to assign the employee using bulk assignment
2. System detects conflict automatically
3. Shows alert: "X employee(s) could not be assigned due to conflicts"
4. Check assignment history to see existing assignments
5. Remove conflicting assignment first, then reassign

**Result**: Conflict prevented, data integrity maintained

## API Endpoints Reference

### 1. Get Assigned Employees
```
GET /api/shifts/{shift}/assignments
```
Returns paginated list of current assignments

### 2. Assign Single Employee
```
POST /api/shifts/{shift}/assignments
Body: {
  employee_id: 123,
  effective_from: "2024-01-01",
  effective_to: "2024-12-31" (optional)
}
```

### 3. Bulk Assign Employees
```
POST /api/shifts/{shift}/assignments/bulk
Body: {
  employee_ids: [123, 456, 789],
  effective_from: "2024-01-01",
  effective_to: "2024-12-31" (optional)
}
Response: {
  assignments: [...],
  conflicts: [...],
  errors: [...],
  success_count: 2,
  conflict_count: 1,
  error_count: 0
}
```

### 4. Remove Assignment
```
DELETE /api/shifts/{shift}/assignments/{assignment}
```

### 5. Get Available Employees
```
GET /api/shifts/{shift}/available-employees?effective_from=2024-01-01&effective_to=2024-12-31&search=john
```
Returns employees not assigned to any shift during the specified period

### 6. Get Assignment History
```
GET /api/shifts/{shift}/assignment-history
```
Returns all assignments (past, present, future) with pagination

## Database Schema

### `employee_shifts` Table
```sql
- id: Primary key
- shift_id: Foreign key to shifts table
- employee_id: Foreign key to employees table  
- effective_from: Date (NOT NULL)
- effective_to: Date (NULL for permanent)
- created_at: Timestamp
- updated_at: Timestamp
```

### Relationships
- Each shift can have multiple employee assignments
- Each employee can have multiple shift assignments (but not overlapping)
- Soft deletes not used (permanent deletion for data integrity)

## Best Practices

### 1. Planning Shift Assignments
- Review existing assignments before making changes
- Check assignment history to understand patterns
- Plan temporary assignments in advance

### 2. Handling Conflicts
- Always check "Current Assignments" before bulk operations
- Use the search feature to find specific employees
- Review conflict reports after bulk assignments

### 3. Data Cleanup
- Regularly review expired assignments
- Remove assignments that are no longer needed
- Keep assignment history for audit purposes

### 4. Communication
- Notify employees before shift changes
- Document reasons for temporary assignments
- Maintain clear records in assignment history

## Troubleshooting

### Issue: Employee not appearing in available list
**Solution**: 
- Check if employee is already assigned to another shift
- Verify the date range doesn't overlap with existing assignments
- Ensure employee status is "Active"

### Issue: Bulk assignment partially fails
**Solution**:
- Review the conflict list in the response
- Check conflicting employees individually
- Remove conflicting assignments first

### Issue: Cannot remove assignment
**Solution**:
- Verify you have Admin or Manager role
- Check if assignment exists in "Current Assignments"
- Refresh the page and try again

## Technical Implementation

### Frontend Components
- **ShiftManagement.vue**: Main shift management interface
- **ShiftAssignments.vue**: Complete assignment management modal with 3 tabs

### Backend Controllers
- **ShiftController**: 7 employee assignment methods with conflict detection

### Security
- Role-based middleware: `role:admin,manager`
- Laravel Sanctum token authentication
- Input validation on all requests

### Performance
- Pagination for large datasets
- Indexed database queries
- Efficient conflict detection algorithm

## Future Enhancements
- Email notifications for shift assignments
- Calendar view for shift schedules
- Shift swap requests between employees
- Automated shift rotation scheduling
- Mobile app support
- Export assignment reports to Excel/PDF

## Support
For technical issues or questions:
1. Check the assignment history for audit trail
2. Review API error responses for specific issues
3. Contact system administrator for role/permission issues

---

**System Version**: 1.0  
**Last Updated**: December 2024  
**Compliance**: Universal HR SOPs
