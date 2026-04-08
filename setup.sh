#!/bin/bash

# HRMS Setup Script

echo "==================================="
echo "HR Management System - Setup Script"
echo "==================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    echo "✓ .env file created"
else
    echo "✓ .env file already exists"
fi

echo ""
echo "Installing PHP dependencies..."
composer install

echo ""
echo "Installing Node dependencies..."
npm install

echo ""
echo "Generating application key..."
php artisan key:generate

echo ""
echo "==================================="
echo "Database Setup"
echo "==================================="
echo "Please ensure PostgreSQL is running and you have created a database named 'hrms'"
echo "Or update the DB_DATABASE value in .env file with your database name"
echo ""
read -p "Have you configured the database in .env? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo "Running migrations..."
    php artisan migrate
    
    echo ""
    read -p "Do you want to seed the database with sample data? (y/n) " -n 1 -r
    echo ""
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "Seeding database..."
        php artisan db:seed
        echo "✓ Database seeded successfully"
        echo ""
        echo "Default login credentials:"
        echo "Admin: admin@hrms.com / password"
        echo "Manager: manager@hrms.com / password"
        echo "Employee: employee@hrms.com / password"
    fi
fi

echo ""
echo "==================================="
echo "Setup Complete!"
echo "==================================="
echo ""
echo "To start the application:"
echo "1. Run: php artisan serve"
echo "2. In another terminal, run: npm run dev"
echo "3. Visit: http://localhost:8000"
echo ""
echo "Happy coding!"
