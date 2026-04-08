<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    private $firstNames = [
        'Ali', 'Ahmed', 'Hassan', 'Hussain', 'Usman', 'Bilal', 'Faisal', 'Imran', 'Kamran', 'Nadeem',
        'Omar', 'Rashid', 'Shahid', 'Tariq', 'Waqar', 'Yasir', 'Zubair', 'Aamir', 'Asif', 'Fahad',
        'Hamza', 'Junaid', 'Khalid', 'Majid', 'Naveed', 'Owais', 'Qamar', 'Rafiq', 'Salman', 'Tahir',
        'Zahid', 'Adnan', 'Azhar', 'Farhan', 'Hasan', 'Iqbal', 'Javed', 'Karim', 'Latif', 'Mansoor',
        'Ayesha', 'Fatima', 'Hina', 'Khadija', 'Maryam', 'Nadia', 'Rabia', 'Sana', 'Zainab', 'Amna',
        'Bushra', 'Farah', 'Huma', 'Lubna', 'Mehwish', 'Noor', 'Rukhsana', 'Saima', 'Tahira', 'Uzma',
        'Aisha', 'Farida', 'Hira', 'Komal', 'Madiha', 'Naila', 'Rafia', 'Saira', 'Zara', 'Asma',
        'Farzana', 'Humaira', 'Kiran', 'Maria', 'Nighat', 'Rehana', 'Samina', 'Tasneem', 'Yasmeen', 'Zaineb',
        'Danish', 'Ehsan', 'Fawad', 'Gulzar', 'Hameed', 'Ibrahim', 'Jamil', 'Kashif', 'Liaquat', 'Mohsin',
        'Nasir', 'Pervaiz', 'Qasim', 'Raza', 'Sadiq', 'Tanvir', 'Umair', 'Waheed', 'Yaseen', 'Zakir'
    ];

    private $lastNames = [
        'Khan', 'Ali', 'Ahmed', 'Hussain', 'Shah', 'Malik', 'Butt', 'Chaudhry', 'Sheikh', 'Iqbal',
        'Rafiq', 'Siddiqui', 'Mirza', 'Raza', 'Haider', 'Naqvi', 'Zaidi', 'Ansari', 'Qureshi', 'Rizvi',
        'Hassan', 'Abbas', 'Jaffar', 'Akbar', 'Aziz', 'Bashir', 'Farooq', 'Ghani', 'Hamid', 'Javed',
        'Karim', 'Latif', 'Mahmood', 'Naeem', 'Rasheed', 'Saleem', 'Tariq', 'Usman', 'Wahab', 'Yousuf'
    ];

    private $cities = [
        'Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar', 'Quetta',
        'Sialkot', 'Gujranwala', 'Hyderabad', 'Bahawalpur', 'Sargodha', 'Sukkur', 'Larkana', 'Mardan'
    ];

    public function run(): void
    {
        $departments = Department::all();
        $designations = Designation::all();
        
        if ($departments->isEmpty() || $designations->isEmpty()) {
            $this->command->error('Please seed Departments and Designations first!');
            return;
        }

        // Create 1000 employees
        $this->command->info('Creating 1000+ employees...');
        
        for ($i = 1; $i <= 1000; $i++) {
            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];
            $email = strtolower($firstName . '.' . $lastName . $i . '@hrms.com');
            
            // Create user
            $user = User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $i <= 10 ? 'admin' : ($i <= 100 ? 'manager' : 'employee'),
            ]);

            // Create employee
            $employmentStatuses = ['active', 'active', 'active', 'active', 'active', 'active', 'active', 'active', 'on_leave', 'terminated'];
            
            Employee::create([
                'user_id' => $user->id,
                'employee_code' => 'EMP' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'department_id' => $departments->random()->id,
                'designation_id' => $designations->random()->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => '03' . rand(10, 49) . rand(1000000, 9999999),
                'joining_date' => now()->subDays(rand(30, 1095)),
                'employment_type' => rand(1, 10) <= 8 ? 'full_time' : (rand(0, 1) ? 'part_time' : 'contract'),
                'employment_status' => $employmentStatuses[array_rand($employmentStatuses)],
                'country' => 'Pakistan',
                'city' => $this->cities[array_rand($this->cities)],
                'address' => 'House ' . rand(1, 999) . ', Block ' . chr(rand(65, 90)),
                'date_of_birth' => now()->subYears(rand(22, 55))->subDays(rand(0, 364)),
            ]);

            if ($i % 100 == 0) {
                $this->command->info("Created {$i} employees...");
            }
        }

        $this->command->info('Successfully created 1000 employees!');
    }
}
