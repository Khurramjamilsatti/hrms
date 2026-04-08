<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TravelRequest;
use App\Models\ExpenseCategory;
use App\Models\ExpenseClaim;
use App\Models\AdvanceRequest;
use App\Models\User;

class TravelExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();
        $managers = User::where('role', 'manager')->get();
        if ($managers->isEmpty()) {
            $managers = User::where('role', 'admin')->get();
        }

        // Create Expense Categories
        $categories = [
            ['name' => 'Accommodation', 'description' => 'Hotel and lodging expenses', 'is_active' => true],
            ['name' => 'Transportation', 'description' => 'Flights, trains, taxis, and other transport', 'is_active' => true],
            ['name' => 'Meals', 'description' => 'Food and beverage expenses', 'is_active' => true],
            ['name' => 'Client Entertainment', 'description' => 'Business meals and entertainment', 'is_active' => true],
            ['name' => 'Office Supplies', 'description' => 'Stationery and office materials', 'is_active' => true],
            ['name' => 'Training & Conferences', 'description' => 'Conference fees and training costs', 'is_active' => true],
            ['name' => 'Communication', 'description' => 'Phone and internet expenses', 'is_active' => true],
            ['name' => 'Miscellaneous', 'description' => 'Other business-related expenses', 'is_active' => true]
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $createdCategories[] = ExpenseCategory::create($categoryData);
        }

        // Create Travel Requests
        $cities = [
            ['from' => 'Karachi', 'to' => 'Lahore'],
            ['from' => 'Karachi', 'to' => 'Islamabad'],
            ['from' => 'Lahore', 'to' => 'Multan'],
            ['from' => 'Karachi', 'to' => 'Peshawar'],
            ['from' => 'Islamabad', 'to' => 'Faisalabad']
        ];

        $purposes = [
            'Client Meeting',
            'Project Kickoff',
            'Training Session',
            'Conference Attendance',
            'Site Visit',
            'Business Development',
            'Vendor Meeting',
            'Team Workshop'
        ];

        $travelModes = ['flight', 'train', 'bus', 'car'];
        $statuses = ['draft', 'submitted', 'approved', 'rejected'];

        foreach ($employees->take(15) as $employee) {
            for ($i = 0; $i < rand(2, 5); $i++) {
                $city = $cities[array_rand($cities)];
                $departureDate = now()->addDays(rand(5, 60))->format('Y-m-d');
                $returnDate = date('Y-m-d', strtotime($departureDate . ' +' . rand(1, 5) . ' days'));
                
                $travelRequest = TravelRequest::create([
                    'employee_id' => $employee->id,
                    'request_number' => 'TR-' . strtoupper(uniqid()),
                    'purpose' => $purposes[array_rand($purposes)],
                    'description' => 'Business travel for ' . $purposes[array_rand($purposes)],
                    'from_location' => $city['from'],
                    'to_location' => $city['to'],
                    'departure_date' => $departureDate,
                    'return_date' => $returnDate,
                    'travel_mode' => $travelModes[array_rand($travelModes)],
                    'estimated_cost' => rand(15000, 50000),
                    'status' => $statuses[array_rand($statuses)],
                    'approved_by' => rand(0, 1) ? $managers->random()->id : null,
                    'approved_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null
                ]);

                // Create expense claims for approved travels
                if ($travelRequest->status === 'approved' && rand(0, 1)) {
                    // Accommodation expense
                    ExpenseClaim::create([
                        'employee_id' => $employee->id,
                        'travel_request_id' => $travelRequest->id,
                        'category_id' => $createdCategories[0]->id,
                        'claim_number' => 'EXP-' . strtoupper(uniqid()),
                        'expense_date' => $departureDate,
                        'merchant_name' => ['Pearl Continental', 'Marriott', 'Serena Hotel', 'Avari Hotel'][array_rand(['Pearl Continental', 'Marriott', 'Serena Hotel', 'Avari Hotel'])],
                        'description' => 'Hotel accommodation for ' . $travelRequest->purpose,
                        'amount' => rand(8000, 25000),
                        'currency' => 'PKR',
                        'receipt_file' => null,
                        'status' => ['submitted', 'approved', 'paid'][array_rand(['submitted', 'approved', 'paid'])],
                        'approved_by' => $managers->random()->id,
                        'approved_at' => now()->subDays(rand(1, 5)),
                        'payment_date' => rand(0, 1) ? now()->subDays(rand(1, 3))->format('Y-m-d') : null
                    ]);

                    // Transportation expense
                    ExpenseClaim::create([
                        'employee_id' => $employee->id,
                        'travel_request_id' => $travelRequest->id,
                        'category_id' => $createdCategories[1]->id,
                        'claim_number' => 'EXP-' . strtoupper(uniqid()),
                        'expense_date' => $departureDate,
                        'merchant_name' => ['PIA', 'Airblue', 'SereneAir', 'Daewoo Express'][array_rand(['PIA', 'Airblue', 'SereneAir', 'Daewoo Express'])],
                        'description' => 'Flight/transport for ' . $travelRequest->purpose,
                        'amount' => rand(12000, 30000),
                        'currency' => 'PKR',
                        'receipt_file' => null,
                        'status' => ['submitted', 'approved', 'paid'][array_rand(['submitted', 'approved', 'paid'])],
                        'approved_by' => $managers->random()->id,
                        'approved_at' => now()->subDays(rand(1, 5)),
                        'payment_date' => rand(0, 1) ? now()->subDays(rand(1, 3))->format('Y-m-d') : null
                    ]);

                    // Meals expense
                    ExpenseClaim::create([
                        'employee_id' => $employee->id,
                        'travel_request_id' => $travelRequest->id,
                        'category_id' => $createdCategories[2]->id,
                        'claim_number' => 'EXP-' . strtoupper(uniqid()),
                        'expense_date' => $departureDate,
                        'merchant_name' => 'Various Restaurants',
                        'description' => 'Meals during travel',
                        'amount' => rand(3000, 8000),
                        'currency' => 'PKR',
                        'receipt_file' => null,
                        'status' => ['submitted', 'approved'][array_rand(['submitted', 'approved'])],
                        'approved_by' => rand(0, 1) ? $managers->random()->id : null,
                        'approved_at' => rand(0, 1) ? now()->subDays(rand(1, 5)) : null,
                        'payment_date' => null
                    ]);
                }
            }
        }

        // Create Advance Requests
        foreach ($employees->take(10) as $employee) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $travelRequest = TravelRequest::where('employee_id', $employee->id)->first();
                $status = ['pending', 'approved', 'paid', 'settled'][array_rand(['pending', 'approved', 'paid', 'settled'])];
                
                AdvanceRequest::create([
                    'employee_id' => $employee->id,
                    'travel_request_id' => $travelRequest ? $travelRequest->id : null,
                    'request_number' => 'ADV-' . strtoupper(uniqid()),
                    'purpose' => $travelRequest ? 'Travel advance for ' . $travelRequest->purpose : 'General business advance',
                    'amount' => rand(20000, 50000),
                    'required_date' => now()->addDays(rand(3, 15))->format('Y-m-d'),
                    'status' => $status,
                    'approved_by' => in_array($status, ['approved', 'paid', 'settled']) ? $managers->random()->id : null,
                    'approved_at' => in_array($status, ['approved', 'paid', 'settled']) ? now()->subDays(rand(1, 5)) : null,
                    'payment_date' => in_array($status, ['paid', 'settled']) ? now()->subDays(rand(1, 3))->format('Y-m-d') : null,
                    'settlement_date' => $status === 'settled' ? now()->subDays(rand(1, 2))->format('Y-m-d') : null,
                    'settled_amount' => $status === 'settled' ? rand(18000, 50000) : null
                ]);
            }
        }

        $this->command->info('Travel & Expense data created successfully!');
    }
}
