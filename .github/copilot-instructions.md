# HR Management System - Copilot Instructions

## Project Overview
Complete HR Management System with Laravel backend, VueJS frontend, and PostgreSQL database.
Currency: PKR (Pakistani Rupee)

## Current Status: ✅ FULLY OPERATIONAL

### Running Services
- **Backend API**: http://localhost:8001/api
- **Frontend App**: http://localhost:5173
- **Database**: PostgreSQL (hrms database, fully migrated and seeded)

### Default Credentials
- **Admin**: admin@hrms.com / password
- **Manager**: manager@hrms.com / password  
- **Employee**: employee@hrms.com / password

## Tech Stack
- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: VueJS 3 with Composition API
- **Database**: PostgreSQL
- **Authentication**: Laravel Sanctum (Token-based API)
- **Styling**: Tailwind CSS
- **State Management**: Pinia
- **Build Tool**: Vite
- **HTTP Client**: Axios

## ✅ Completed Features

### Core Modules (100% Complete)
- [x] **Employee Management**
  - Full CRUD operations with pagination
  - Employee profiles with personal & employment info
  - Document management
  - Contract tracking
  - Salary history
  
- [x] **Attendance Tracking**
  - Check-in/check-out functionality
  - Shift management (Morning, Evening, Night)
  - Overtime request and tracking
  - Attendance reports and summaries
  - Late arrival tracking
  
- [x] **Payroll Management** (PKR Currency)
  - Automated monthly payroll generation
  - Salary components (Basic, HRA, Medical, Transport, etc.)
  - Bonus and deduction management
  - Payroll processing workflow (Draft → Processed → Paid)
  - Payment tracking with PKR formatting (Rs. 50,000)
  
- [x] **Leave Management**
  - Multiple leave types (Annual, Sick, Casual, Maternity, Unpaid)
  - Leave application workflow
  - Approval/rejection with remarks
  - Leave balance tracking
  - Carry-forward functionality
  
- [x] **Recruitment Module**
  - Job position management
  - Application tracking
  - Interview scheduling
  - Offer management
  - Hiring workflow
  
- [x] **Performance Management**
  - Performance review cycles
  - Goal setting and tracking
  - Rating system
  - Review history
  
- [x] **Asset Management**
  - Asset catalog
  - Assignment tracking
  - Return management
  - Asset history
  
- [x] **Department Management**
  - Department hierarchy
  - Manager assignment
  - Employee count tracking
  - Active/inactive status

### Authentication & Authorization (100% Complete)
- [x] Laravel Sanctum token-based authentication
- [x] Comprehensive permission system (80 permissions, module.action pattern)
- [x] CheckPermission middleware on all API routes
- [x] Permission-based menu filtering (UI level)
- [x] Router guards for URL access control (Navigation level)
- [x] Protected routes with permission validation
- [x] Login/logout functionality
- [x] Token refresh and validation
- [x] 401 error handling with auto-redirect
- [x] 403 access denied with user-friendly notifications
- [x] Automatic permission loading on authentication

### Frontend Components (100% Complete)
- [x] Dashboard with role-based statistics
- [x] Employee list with search & filters
- [x] Employee form (create/edit)
- [x] Employee details view
- [x] Attendance list & check-in/out
- [x] Leave application list & forms
- [x] Payroll list & generation
- [x] Department list
- [x] User profile view
- [x] Responsive layout with sidebar navigation
- [x] Loading states and error handling

### API Endpoints (100% Complete)
- [x] Authentication (login, logout, me)
- [x] Dashboard stats
- [x] Employees CRUD
- [x] Attendance (check-in, check-out, summary)
- [x] Leave applications (CRUD, approve, reject)
- [x] Payroll (generate, process, details)
- [x] Departments CRUD

## Project Structure

### Backend (Laravel)
```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── EmployeeController.php
│   │   ├── AttendanceController.php
│   │   ├── LeaveApplicationController.php
│   │   ├── PayrollController.php
│   │   └── DepartmentController.php
│   └── Middleware/
│       ├── CheckRole.php
│       └── EnsureEmailIsVerified.php
├── Models/
│   ├── User.php
│   ├── Employee.php
│   ├── Department.php
│   ├── Designation.php
│   ├── Attendance.php
│   ├── LeaveApplication.php
│   ├── Payroll.php
│   └── [20+ other models]
database/
├── migrations/ (8 migration files, all executed)
└── seeders/ (7 seeder files, all executed)
routes/
└── api.php (all routes configured)
```

### Frontend (Vue)
```
resources/js/
├── app.js (Vue app initialization)
├── bootstrap.js (Axios configuration)
├── router/
│   └── index.js (all routes with guards)
├── stores/
│   ├── auth.js
│   ├── dashboard.js
│   └── employee.js
├── layouts/
│   └── DashboardLayout.vue
└── views/
    ├── auth/
    │   └── Login.vue
    ├── Dashboard.vue
    ├── Profile.vue
    ├── employees/
    │   ├── EmployeeList.vue
    │   ├── EmployeeForm.vue
    │   └── EmployeeDetails.vue
    ├── attendance/
    │   └── AttendanceList.vue
    ├── leaves/
    │   └── LeaveList.vue
    ├── payroll/
    │   └── PayrollList.vue
    └── departments/
        └── DepartmentList.vue
```

## Development Guidelines

### Starting Development Servers
```bash
# Terminal 1 - Laravel Backend
php artisan serve --port=8001

# Terminal 2 - Vue Frontend  
npm run dev
```

### Database Operations
```bash
# Run fresh migration with seed
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=EmployeeSeeder
```

### Code Style
- **Laravel**: Follow PSR-12 standards
- **Vue**: Use Composition API with `<script setup>`
- **CSS**: Use Tailwind utility classes
- **Currency**: Always format as PKR (Rs. 50,000)

### API Development
- All API routes in `routes/api.php`
- Use `auth:sanctum` middleware for protected routes
- Return JSON responses with proper HTTP status codes
- Include pagination for list endpoints

### Frontend Development
- All routes in `resources/js/router/index.js`
- Use Pinia stores for state management
- Implement loading states and error handling
- Format currency as PKR (Rs. with comma separators)

## Important Notes

### Currency Handling
- System uses PKR (Pakistani Rupees)
- Format: Rs. 50,000 (with comma separators)
- Store as decimal in database
- Format on frontend using `Intl.NumberFormat('en-PK')`

### Authentication Flow
1. User logs in via POST `/api/login`
2. Token returned and stored in localStorage
3. Token sent in Authorization header for all API requests
4. 401 responses trigger logout and redirect to login

### Role-Based Access
- **Admin**: Full access to all features
- **Manager**: Department management, approvals, team reports
- **Employee**: Self-service (attendance, leaves, profile)

### Date/Time Format
- Database: YYYY-MM-DD (dates), HH:MM:SS (times)
- Display: Use `toLocaleDateString('en-PK')` for dates
- Timezone: Asia/Karachi (configured in config/app.php)

## Documentation Files
- **README.md** - Project overview and features
- **INSTALLATION.md** - Detailed setup guide
- **API.md** - Complete API documentation
- **SETUP-COMPLETE.md** - Current system status
- **docker-compose.yml** - PostgreSQL & pgAdmin setup
- **setup.sh** - Automated setup script

## Future Enhancements
- [ ] Email notifications (SMTP configuration needed)
- [ ] PDF payslip generation
- [ ] Advanced reporting and charts
- [ ] Document approval workflow
- [ ] Training and development module
- [ ] Employee self-service enhancements
- [ ] Export to Excel functionality
- [ ] Multi-language support
- [ ] Dark mode theme
- [ ] Mobile app (React Native)
