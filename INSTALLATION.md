# HRMS Installation Guide

## Quick Start with Docker

The easiest way to get started is using Docker Compose:

```bash
# 1. Start PostgreSQL and pgAdmin
docker-compose up -d

# 2. Run the setup script
chmod +x setup.sh
./setup.sh

# 3. Start the development servers
# Terminal 1 - Laravel Backend
php artisan serve

# Terminal 2 - Vue Frontend
npm run dev
```

Visit http://localhost:8000 to access the application.

## Manual Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- PostgreSQL 14+

### Step-by-Step Installation

#### 1. Clone or Navigate to Project
```bash
cd /path/to/hrms
```

#### 2. Install PHP Dependencies
```bash
composer install
```

#### 3. Install Node Dependencies
```bash
npm install
```

#### 4. Environment Configuration
```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. Configure Database
Edit `.env` file and update database credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hrms
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

#### 6. Create Database
```sql
-- Connect to PostgreSQL
psql -U postgres

-- Create database
CREATE DATABASE hrms;

-- Exit
\q
```

#### 7. Run Migrations and Seeders
```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

#### 8. Start Development Servers

**Terminal 1 - Laravel Backend:**
```bash
php artisan serve
```

**Terminal 2 - Vue Frontend:**
```bash
npm run dev
```

The application will be available at:
- Frontend: http://localhost:5173 (Vite dev server)
- Backend API: http://localhost:8000

## Default Login Credentials

After seeding, you can login with these accounts:

### Admin Account
- Email: `admin@hrms.com`
- Password: `password`
- Access: Full system access

### Manager Account
- Email: `manager@hrms.com`
- Password: `password`
- Access: Department management, approvals

### Employee Account
- Email: `employee@hrms.com`
- Password: `password`
- Access: Self-service features

## Docker Setup Details

### Services Included
- **PostgreSQL 16**: Main database server (Port 5432)
- **pgAdmin 4**: Web-based database management (Port 5050)

### Access pgAdmin
1. Visit http://localhost:5050
2. Login with:
   - Email: `admin@hrms.com`
   - Password: `password`
3. Add server connection:
   - Host: `postgres`
   - Port: `5432`
   - Database: `hrms`
   - Username: `postgres`
   - Password: `password`

## Production Build

To build for production:

```bash
# Build frontend assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

## Troubleshooting

### Database Connection Issues
```bash
# Check if PostgreSQL is running
docker-compose ps

# Or for local installation
sudo service postgresql status

# Check database exists
psql -U postgres -c "\l"
```

### Permission Errors
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Frontend Build Issues
```bash
# Remove node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf node_modules/.vite
npm run dev
```

## Common Commands

```bash
# Database
php artisan migrate:fresh --seed  # Reset database
php artisan migrate:status         # Check migrations

# Development
php artisan tinker                 # Laravel REPL
php artisan route:list             # List all routes

# Queue (if using queues)
php artisan queue:work             # Process queue jobs

# Create admin user manually
php artisan tinker
>>> User::factory()->create(['email' => 'admin@hrms.com', 'role' => 'admin'])
```

## Testing

```bash
# Run PHP tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## System Requirements

### Minimum
- PHP 8.2+
- PostgreSQL 14+
- 2GB RAM
- 2GB disk space

### Recommended
- PHP 8.3+
- PostgreSQL 16+
- 4GB RAM
- 10GB disk space
- Redis for caching

## Support

For issues or questions:
1. Check the main [README.md](README.md)
2. Review error logs in `storage/logs/laravel.log`
3. Check browser console for frontend errors
4. Verify all environment variables are set correctly

## Security Notes

**Important for Production:**
- Change all default passwords
- Set `APP_DEBUG=false` in production
- Use strong `APP_KEY`
- Enable HTTPS
- Configure proper CORS settings
- Set up rate limiting
- Use environment-specific credentials
- Enable Laravel's security features
