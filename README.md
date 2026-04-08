# 🏢 HR Management System (HRMS)

A comprehensive, modern Human Resource Management System built with Laravel 11, VueJS 3, and PostgreSQL. This system provides complete HR functionality including employee management, attendance tracking, payroll processing (PKR), leave management, recruitment, and performance reviews.

![Status](https://img.shields.io/badge/Status-Production%20Ready-success)
![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![Vue](https://img.shields.io/badge/Vue.js-3.x-green)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-blue)
![License](https://img.shields.io/badge/License-MIT-yellow)

## ✨ Features

### 👥 Employee Management
- Complete employee profile management with CRUD operations
- Department and designation assignment
- Document management and storage
- Contract tracking and history
- Employment history and status tracking
- Salary history and current salary tracking
- Search, filter, and pagination

### ⏰ Attendance & Time Tracking
- Daily check-in/check-out functionality
- Shift management (Morning, Evening, Night)
- Overtime request and tracking
- Attendance reports and summaries
- Late arrival tracking and notifications
- Monthly attendance analytics
- Working hours calculation

### 💰 Payroll Management (PKR Currency)
- **Automated monthly payroll generation**
- Salary component management (earnings & deductions)
- Bonus and overtime calculations
- Payroll processing workflow (Draft → Processed → Paid)
- Payment tracking and history
- **Currency: Pakistani Rupees (Rs.)**
- Salary slip generation (ready for PDF export)

### 🏖️ Leave Management
- Multiple leave types (Annual, Sick, Casual, Maternity, Unpaid)
- Leave balance tracking per employee
- Leave application workflow
- Approval/rejection system with remarks
- Leave carry-forward functionality
- Leave history and reports
- Email notifications (configurable)

### 📋 Recruitment
- Job position management
- Application tracking and status
- Interview scheduling and feedback
- Offer management and acceptance tracking
- Complete hiring workflow
- Application history

### 📊 Performance Management
- Performance review cycles
- Goal setting and tracking
- Rating system (1-5 scale)
- Review history and comparisons
- Feedback management
- Performance analytics

### 🔧 Additional Features
- Asset management and assignment tracking
- Department hierarchy and management
- Announcements and notices
- Role-based access control (Admin/Manager/Employee)
- Responsive dashboard with analytics
- RESTful API with Sanctum authentication
- Modern, responsive UI with Tailwind CSS

## 🚀 Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: VueJS 3 (Composition API)
- **Database**: PostgreSQL 16
- **Authentication**: Laravel Sanctum (Token-based)
- **UI Framework**: Tailwind CSS
- **State Management**: Pinia
- **Build Tool**: Vite
- **HTTP Client**: Axios

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- PostgreSQL 14+
- Git

## 🔧 Installation

For detailed installation instructions, see [INSTALLATION.md](INSTALLATION.md)

### Quick Start

```bash
git clone <repository-url>
cd hrms
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Database

Edit `.env` file with your PostgreSQL credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hrms
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 6. Create Database

```bash
createdb hrms
```

Or using PostgreSQL client:

```sql
CREATE DATABASE hrms;
```

### 7. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

### 8. Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 9. Start the Application

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Default Login Credentials

After running seeders, you can login with:

**Admin:**
- Email: admin@hrms.com
- Password: password

**Manager:**
- Email: manager@hrms.com
- Password: password

**Employee:**
- Email: employee@hrms.com
- Password: password

## Database Schema

The system includes the following main tables:

- `users` - User authentication
- `employees` - Employee information
- `departments` - Department hierarchy
- `designations` - Job positions
- `attendances` - Daily attendance records
- `shifts` - Work shifts
- `leave_types` - Leave categories
- `leave_applications` - Leave requests
- `employee_leave_balances` - Leave balances
- `salary_components` - Salary structure
- `employee_salaries` - Employee salary details
- `payrolls` - Monthly payroll records
- `bonuses` - Bonus payments
- `job_positions` - Job openings
- `job_applications` - Candidate applications
- `interviews` - Interview schedules
- `offers` - Job offers
- `performance_reviews` - Performance evaluations
- `goals` - Employee goals/KPIs
- `assets` - Company assets
- `asset_assignments` - Asset allocation
- `announcements` - Company announcements

## API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/me` - Get current user
- `POST /api/change-password` - Change password

### Dashboard
- `GET /api/dashboard` - Dashboard statistics
- `GET /api/dashboard/stats` - Detailed stats

### Employees
- `GET /api/employees` - List employees
- `POST /api/employees` - Create employee
- `GET /api/employees/{id}` - Get employee details
- `PUT /api/employees/{id}` - Update employee
- `DELETE /api/employees/{id}` - Delete employee

### Attendance
- `GET /api/attendance` - List attendance
- `POST /api/attendance` - Create attendance record
- `POST /api/attendance/check-in` - Check in
- `POST /api/attendance/check-out` - Check out
- `GET /api/attendance/summary` - Attendance summary

### Leave Management
- `GET /api/leave-applications` - List leave applications
- `POST /api/leave-applications` - Apply for leave
- `POST /api/leave-applications/{id}/approve` - Approve leave
- `POST /api/leave-applications/{id}/reject` - Reject leave

### Payroll (Admin/Manager only)
- `GET /api/payroll` - List payrolls
- `POST /api/payroll/generate` - Generate monthly payroll
- `POST /api/payroll/{id}/process` - Process payroll
- `POST /api/payroll/{id}/mark-paid` - Mark as paid

### Departments (Admin/Manager only)
- `GET /api/departments` - List departments
- `POST /api/departments` - Create department
- `PUT /api/departments/{id}` - Update department
- `DELETE /api/departments/{id}` - Delete department

## Currency Configuration

The system uses PKR (Pakistani Rupee) as the default currency. This is configured in `config/app.php`:

```php
'currency' => 'PKR',
'currency_symbol' => 'Rs.',
```

## Role-Based Access Control

The system has three user roles:

1. **Admin**
   - Full system access
   - Manage all employees, departments, payroll
   - System configuration

2. **Manager**
   - Approve leave applications
   - View team attendance
   - Access payroll information
   - Manage department employees

3. **Employee**
   - View own profile
   - Mark attendance
   - Apply for leaves
   - View own payslips

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

Format code using Laravel Pint:

```bash
./vendor/bin/pint
```

### Frontend Development

```bash
npm run dev
```

This will start Vite dev server with hot module replacement.

## Deployment

### Production Build

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Variables

Ensure these are properly set in production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

## Docker Support (Optional)

Create `docker-compose.yml`:

```yaml
version: '3.8'
services:
  postgres:
    image: postgres:16
    environment:
      POSTGRES_DB: hrms
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: password
    ports:
      - "5432:5432"
    volumes:
      - pgdata:/var/lib/postgresql/data

volumes:
  pgdata:
```

Run with:
```bash
docker-compose up -d
```

## Troubleshooting

### Database Connection Issues

Ensure PostgreSQL is running:
```bash
sudo service postgresql status
```

### Permission Issues

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Support

For issues and questions, please create an issue in the repository.

## License

This project is open-sourced software licensed under the MIT license.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Roadmap

- [ ] Email notifications
- [ ] PDF report generation
- [ ] Employee self-service portal enhancements
- [ ] Mobile app
- [ ] Advanced analytics and insights
- [ ] Integration with biometric devices
- [ ] Document approval workflow
- [ ] Training management module
