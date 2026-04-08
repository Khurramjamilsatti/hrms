# New Features API Documentation

## Overview
This document provides detailed API documentation for the newly added features:
- **Loan Management** - Employee loans with interest, installments, and payment tracking
- **CV Bank** - Employee CV repository with versioning and file uploads
- **Employee Deployments** - Deployment tracking with extensions and long leave support
- **Sunday Attendance** - Enhanced attendance tracking with Sunday allowances
- **Special Leave Types** - 10 new leave types including Gratuity Leave

---

## 1. Loan Management

### Loan Types
- `personal` - Personal loans
- `medical` - Medical emergency loans
- `education` - Education loans
- `housing` - Housing/accommodation loans
- `emergency` - Emergency loans

### Loan Statuses
- `pending` - Application submitted, awaiting approval
- `approved` - Approved by manager/admin
- `rejected` - Application rejected
- `disbursed` - Loan amount disbursed to employee
- `active` - Loan being repaid via installments
- `completed` - Loan fully repaid
- `defaulted` - Payment defaults
- `cancelled` - Loan cancelled

### Endpoints

#### List Loans
```http
GET /api/loans
```
**Query Parameters:**
- `status` - Filter by status (optional)
- `employee_id` - Filter by employee (optional)
- `loan_type` - Filter by type (optional)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "loan_number": "LN2026000001",
      "employee_id": 5,
      "employee": {
        "id": 5,
        "first_name": "John",
        "last_name": "Doe"
      },
      "loan_type": "personal",
      "amount": "50000.00",
      "interest_rate": "5.00",
      "total_amount": "52500.00",
      "installments": 12,
      "installment_amount": "4375.00",
      "total_paid": "8750.00",
      "balance_amount": "43750.00",
      "status": "active",
      "application_date": "2026-02-15",
      "approval_date": "2026-02-18",
      "disbursement_date": "2026-02-20",
      "purpose": "Home renovation",
      "guarantor_name": "Jane Smith",
      "guarantor_employee_id": 8,
      "created_at": "2026-02-15T10:30:00.000000Z"
    }
  ]
}
```

#### Create Loan Application
```http
POST /api/loans
```
**Request Body:**
```json
{
  "loan_type": "personal",
  "amount": 50000,
  "interest_rate": 5.0,
  "installments": 12,
  "purpose": "Home renovation",
  "guarantor_name": "Jane Smith",
  "guarantor_employee_id": 8,
  "guarantor_phone": "+92-300-1234567",
  "application_date": "2026-02-15"
}
```

#### Get Loan Details
```http
GET /api/loans/{id}
```

#### Update Loan
```http
PUT /api/loans/{id}
```

#### Approve Loan (Admin/Manager only)
```http
POST /api/loans/{id}/approve
```
**Request Body:**
```json
{
  "remarks": "Approved based on employment history"
}
```

#### Reject Loan (Admin/Manager only)
```http
POST /api/loans/{id}/reject
```
**Request Body:**
```json
{
  "remarks": "Insufficient employment tenure"
}
```

#### Disburse Loan (Admin/Manager only)
```http
POST /api/loans/{id}/disburse
```
**Request Body:**
```json
{
  "disbursement_date": "2026-02-20",
  "disbursement_method": "bank_transfer",
  "remarks": "Transferred to employee account"
}
```

#### Add Payment (Admin/Manager only)
```http
POST /api/loans/{id}/payments
```
**Request Body:**
```json
{
  "payment_date": "2026-03-01",
  "amount": 4375,
  "principal_amount": 4156.25,
  "interest_amount": 218.75,
  "payment_method": "salary_deduction",
  "reference_number": "PAY20260301",
  "remarks": "Monthly installment deducted from salary"
}
```
**Note:** Loan automatically closes when `balance_amount` reaches zero.

#### Delete Loan
```http
DELETE /api/loans/{id}
```

---

## 2. CV Bank

### Endpoints

#### List CVs (Admin/Manager only)
```http
GET /api/cvs
```
**Query Parameters:**
- `employee_name` - Search by employee name (optional)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "employee_id": 5,
      "employee": {
        "id": 5,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@hrms.com"
      },
      "file_name": "john_doe_cv_2026.pdf",
      "file_path": "cvs/5/john_doe_cv_2026.pdf",
      "file_size": 524288,
      "version": 3,
      "is_current": true,
      "summary": "Senior Software Engineer with 8 years experience",
      "experience_years": 8,
      "education_level": "Masters",
      "skills": ["PHP", "Laravel", "Vue.js", "PostgreSQL"],
      "certifications": ["AWS Certified Developer"],
      "languages": ["English", "Urdu"],
      "uploaded_at": "2026-02-20T14:30:00.000000Z"
    }
  ]
}
```

#### Upload CV (Admin/Manager only)
```http
POST /api/cvs
Content-Type: multipart/form-data
```
**Request Body (Form Data):**
- `employee_id` (required) - Employee ID
- `cv_file` (required) - PDF/DOC/DOCX file (max 5MB)
- `summary` (optional) - CV summary
- `experience_years` (optional) - Years of experience
- `education_level` (optional) - Highest education level
- `skills` (optional) - Array of skills
- `certifications` (optional) - Array of certifications
- `languages` (optional) - Array of languages

**Example:**
```bash
curl -X POST http://localhost:8001/api/cvs \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "employee_id=5" \
  -F "cv_file=@/path/to/cv.pdf" \
  -F "summary=Senior Software Engineer with 8 years experience" \
  -F "experience_years=8" \
  -F "education_level=Masters" \
  -F "skills[]=PHP" \
  -F "skills[]=Laravel" \
  -F "skills[]=Vue.js"
```

**Note:** When a new CV is uploaded, previous CVs are automatically marked as `is_current = false`, and the new CV gets the next version number.

#### Get CV Details (Admin/Manager only)
```http
GET /api/cvs/{id}
```

#### Update CV (Admin/Manager only)
```http
POST /api/cvs/{id}
Content-Type: multipart/form-data
```
**Request Body:** Same as upload (file optional for updating metadata only)

#### Download CV (Admin/Manager only)
```http
GET /api/cvs/{id}/download
```
**Response:** File download with original filename

#### Get Employee CV History
```http
GET /api/cvs/employees/{employeeId}/history
```
**Response:**
```json
{
  "data": [
    {
      "id": 3,
      "version": 3,
      "is_current": true,
      "file_name": "john_doe_cv_2026.pdf",
      "uploaded_at": "2026-02-20T14:30:00.000000Z"
    },
    {
      "id": 2,
      "version": 2,
      "is_current": false,
      "file_name": "john_doe_cv_2025.pdf",
      "uploaded_at": "2025-06-15T10:00:00.000000Z"
    },
    {
      "id": 1,
      "version": 1,
      "is_current": false,
      "file_name": "john_doe_cv_2024.pdf",
      "uploaded_at": "2024-01-10T09:00:00.000000Z"
    }
  ]
}
```

#### Delete CV (Admin/Manager only)
```http
DELETE /api/cvs/{id}
```

---

## 3. Employee Deployments

### Deployment Types
- `domestic` - Within country
- `international` - Outside country
- `project` - Project-based deployment
- `temporary` - Temporary assignment
- `permanent` - Permanent transfer

### Deployment Statuses
- `draft` - Initial creation
- `pending` - Awaiting approval
- `approved` - Approved by management
- `active` - Employee currently deployed
- `completed` - Deployment finished
- `extended` - Deployment extended
- `cancelled` - Deployment cancelled

### Endpoints

#### List Deployments
```http
GET /api/deployments
```
**Query Parameters:**
- `status` - Filter by status (optional)
- `employee_id` - Filter by employee (optional)
- `deployment_type` - Filter by type (optional)
- `departure_from_long_leave` - Filter by long leave departure (true/false)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "deployment_number": "DEP2026000001",
      "employee_id": 5,
      "employee": {
        "id": 5,
        "first_name": "John",
        "last_name": "Doe"
      },
      "deployment_type": "international",
      "location": "Dubai Office",
      "country": "UAE",
      "city": "Dubai",
      "start_date": "2026-03-01",
      "end_date": "2026-09-01",
      "current_extension_end_date": null,
      "departure_from_long_leave": true,
      "long_leave_start_date": "2025-12-01",
      "long_leave_end_date": "2026-02-28",
      "project_name": "ERP Implementation",
      "visa_status": "approved",
      "visa_expiry_date": "2027-03-01",
      "insurance_status": "active",
      "travel_ticket_status": "booked",
      "accommodation_details": "Company apartment provided",
      "transport_details": "Company car assigned",
      "allowance_amount": "5000.00",
      "allowance_currency": "AED",
      "status": "approved",
      "extension_count": 0,
      "created_at": "2026-02-10T09:00:00.000000Z"
    }
  ]
}
```

#### Create Deployment (Admin/Manager only)
```http
POST /api/deployments
```
**Request Body:**
```json
{
  "employee_id": 5,
  "deployment_type": "international",
  "location": "Dubai Office",
  "country": "UAE",
  "city": "Dubai",
  "start_date": "2026-03-01",
  "end_date": "2026-09-01",
  "departure_from_long_leave": true,
  "long_leave_start_date": "2025-12-01",
  "long_leave_end_date": "2026-02-28",
  "project_name": "ERP Implementation",
  "project_description": "Implement ERP system for Dubai branch",
  "reporting_manager": "Ahmed Ali",
  "reporting_manager_email": "ahmed@company.com",
  "visa_status": "applied",
  "insurance_status": "pending",
  "accommodation_details": "Company apartment - Building A, Unit 205",
  "transport_details": "Company car assigned",
  "allowance_amount": 5000,
  "allowance_currency": "AED",
  "special_instructions": "Medical checkup required before departure",
  "remarks": "Replacing previous employee who returned"
}
```

#### Get Deployment Details
```http
GET /api/deployments/{id}
```

#### Update Deployment (Admin/Manager only)
```http
PUT /api/deployments/{id}
```

#### Approve Deployment (Admin/Manager only)
```http
POST /api/deployments/{id}/approve
```
**Request Body:**
```json
{
  "remarks": "Approved - all documents verified"
}
```

#### Activate Deployment (Admin/Manager only)
```http
POST /api/deployments/{id}/activate
```
**Request Body:**
```json
{
  "remarks": "Employee has reached deployment location"
}
```

#### Complete Deployment (Admin/Manager only)
```http
POST /api/deployments/{id}/complete
```
**Request Body:**
```json
{
  "actual_end_date": "2026-09-05",
  "remarks": "Project completed successfully"
}
```

#### Request Extension (Admin/Manager only)
```http
POST /api/deployments/{id}/extend
```
**Request Body:**
```json
{
  "new_end_date": "2026-12-01",
  "reason": "Project phase 2 requires 3 more months",
  "remarks": "Client requested extension for phase 2"
}
```
**Note:** Creates a `DeploymentExtension` record with `pending` status.

#### Approve Extension (Admin/Manager only)
```http
POST /api/deployments/extensions/{extensionId}/approve
```
**Request Body:**
```json
{
  "remarks": "Extension approved - updated visa accordingly"
}
```
**Note:** Updates deployment's `end_date`, increments `extension_count`, and sets `current_extension_end_date`.

#### Get Employee Deployment History
```http
GET /api/deployments/employees/{employeeId}/history
```
**Response:**
```json
{
  "data": [
    {
      "id": 3,
      "deployment_number": "DEP2026000003",
      "location": "Dubai Office",
      "country": "UAE",
      "start_date": "2026-03-01",
      "end_date": "2026-12-01",
      "status": "active",
      "extension_count": 1
    },
    {
      "id": 1,
      "deployment_number": "DEP2024000015",
      "location": "Karachi Branch",
      "country": "Pakistan",
      "start_date": "2024-01-01",
      "end_date": "2024-12-31",
      "status": "completed",
      "extension_count": 0
    }
  ]
}
```

#### Delete Deployment (Admin/Manager only)
```http
DELETE /api/deployments/{id}
```

---

## 4. Sunday Attendance Tracking

### Enhanced Attendance Fields
The `attendances` table now includes:
- `is_weekend` (boolean) - True if Saturday or Sunday
- `is_sunday` (boolean) - True if Sunday
- `is_holiday` (boolean) - True if public holiday
- `day_type` (enum) - 'regular', 'sunday', 'saturday', 'holiday'
- `sunday_allowance` (decimal) - Additional allowance for Sunday work
- `holiday_allowance` (decimal) - Additional allowance for holiday work

### Automatic Detection
When attendance is marked, the system automatically:
1. Checks if the date is a Sunday (day of week = 0 in PostgreSQL)
2. Sets `is_sunday = true` if Sunday
3. Sets `is_weekend = true` if Saturday or Sunday
4. Sets `day_type = 'sunday'` if Sunday
5. Calculates and applies `sunday_allowance` if configured

### Usage with Existing Endpoints
All existing attendance endpoints continue to work. The new fields are automatically populated.

**Example Response:**
```json
{
  "id": 150,
  "employee_id": 5,
  "date": "2026-02-23",
  "check_in": "09:00:00",
  "check_out": "18:00:00",
  "is_weekend": true,
  "is_sunday": true,
  "is_holiday": false,
  "day_type": "sunday",
  "sunday_allowance": "1500.00",
  "holiday_allowance": null,
  "worked_hours": 9,
  "status": "present"
}
```

---

## 5. Special Leave Types

### New Leave Types Seeded
The following special leave types are now available:

1. **Gratuity Leave**
   - Days per year: 30
   - Paid: Yes
   - Carry forward: No
   - For employees with long service

2. **Study Leave**
   - Days per year: 60
   - Paid: No
   - Carry forward: No
   - Document required: Yes

3. **Compassionate Leave**
   - Days per year: 10
   - Paid: Yes
   - Carry forward: No
   - For family emergencies

4. **Sabbatical Leave**
   - Days per year: 90
   - Paid: No
   - Carry forward: No
   - For extended break/research

5. **Hajj/Pilgrimage Leave**
   - Days per year: 45
   - Paid: Yes
   - Carry forward: No
   - For religious pilgrimage

6. **Long Service Leave**
   - Days per year: 15
   - Paid: Yes
   - Carry forward: Yes (30 days max)
   - Requires 5 years service

7. **Military Service Leave**
   - Days per year: 365
   - Paid: No
   - Carry forward: No

8. **Jury Duty Leave**
   - Days per year: 10
   - Paid: Yes
   - Document required: Yes

9. **Adoption Leave**
   - Days per year: 60
   - Paid: Yes
   - Document required: Yes

10. **Volunteer Leave**
    - Days per year: 5
    - Paid: Yes
    - For community service

### Usage
Use existing Leave Application endpoints. Just select the appropriate leave type:

```http
POST /api/leave-applications
```
```json
{
  "employee_id": 5,
  "leave_type_id": 15,  // ID of Gratuity Leave
  "start_date": "2026-03-01",
  "end_date": "2026-03-30",
  "reason": "Long service break after 10 years",
  "document_path": "/uploads/gratuity-approval.pdf"
}
```

---

## Testing the APIs

### 1. Start the Backend
```bash
php artisan serve --port=8001
```

### 2. Login to Get Token
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@hrms.com",
    "password": "password"
  }'
```

### 3. Test Loan Creation
```bash
curl -X POST http://localhost:8001/api/loans \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "loan_type": "personal",
    "amount": 50000,
    "interest_rate": 5.0,
    "installments": 12,
    "purpose": "Home renovation"
  }'
```

### 4. Test CV Upload
```bash
curl -X POST http://localhost:8001/api/cvs \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "employee_id=5" \
  -F "cv_file=@/path/to/cv.pdf" \
  -F "experience_years=8"
```

### 5. Test Deployment Creation
```bash
curl -X POST http://localhost:8001/api/deployments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "employee_id": 5,
    "deployment_type": "international",
    "location": "Dubai Office",
    "country": "UAE",
    "start_date": "2026-03-01",
    "end_date": "2026-09-01"
  }'
```

---

## Next Steps

### Frontend Integration
1. Create Vue components:
   - `LoanManagement.vue` - List loans, apply, approve
   - `CvBank.vue` - CV repository with upload
   - `DeploymentHistory.vue` - Deployment tracking
   
2. Add navigation menu items in `DashboardLayout.vue`:
   - Loans (icon: currency-dollar)
   - CV Bank (icon: document-text)
   - Deployments (icon: globe-alt)

3. Update existing components:
   - `AttendanceList.vue` - Show Sunday indicator and allowance
   - `LeaveList.vue` - Add special leave type selector

### File Storage
CV files are stored in:
- Path: `storage/app/public/cvs/{employee_id}/`
- Publicly accessible via: `http://localhost:8001/storage/cvs/{employee_id}/filename.pdf`
- Symbolic link created: `public/storage` → `storage/app/public`

### Authorization
- All routes protected with `auth:sanctum` middleware
- Admin/Manager only routes protected with `role:admin,manager` middleware
- Employees can apply for loans but cannot approve/disburse
- Employees can view their own deployment history

---

## Summary

✅ **Loan Management** - Complete system with approval workflow, interest calculations, and payment tracking  
✅ **CV Bank** - Version-controlled CV repository with file uploads  
✅ **Deployments** - Full deployment lifecycle with extensions and long leave support  
✅ **Sunday Attendance** - Automatic detection and allowance calculation  
✅ **Special Leaves** - 10 new leave types including Gratuity Leave  

All backend APIs are **READY FOR USE**. Frontend components are the next priority.
