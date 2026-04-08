<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Employee;
use App\Models\User;

class LoanSeeder extends Seeder
{
    public function run()
    {
        $employees = Employee::with('user')->get();
        
        if ($employees->count() < 3) {
            $this->command->info('Not enough employees to seed loans. Please run EmployeeSeeder first.');
            return;
        }

        $loanTypes = ['personal', 'medical', 'education', 'housing', 'emergency'];
        $statuses = ['pending', 'approved', 'disbursed', 'active', 'completed'];
        
        // Create 20 loans with varied statuses
        foreach ($employees->random(min(10, $employees->count())) as $index => $employee) {
            $loanNumber = 100001 + $index;
            $amount = rand(25000, 200000);
            $interestRate = rand(3, 10);
            $installments = [6, 12, 18, 24, 36][rand(0, 4)];
            $installmentAmount = $amount * (1 + $interestRate / 100) / $installments;
            $balanceAmount = $amount;
            
            // Vary the status based on index
            $status = $statuses[$index % count($statuses)];
            
            $startDate = now()->subDays(rand(1, 60));
            $endDate = (clone $startDate)->addMonths($installments);
            
            $loan = Loan::create([
                'loan_number' => 'LN2026' . str_pad($loanNumber, 6, '0', STR_PAD_LEFT),
                'employee_id' => $employee->id,
                'loan_type' => $loanTypes[rand(0, 4)],
                'amount' => $amount,
                'interest_rate' => $interestRate,
                'installments' => $installments,
                'installment_amount' => $installmentAmount,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'purpose' => $this->getPurpose($loanTypes[rand(0, 4)]),
                'status' => $status,
                'approved_by' => $status !== 'pending' ? User::where('role', 'admin')->first()?->id : null,
                'approved_at' => $status !== 'pending' ? now()->subDays(rand(1, 50)) : null,
                'disbursed_by' => in_array($status, ['disbursed', 'active', 'completed']) ? User::where('role', 'admin')->first()?->id : null,
                'disbursed_date' => in_array($status, ['disbursed', 'active', 'completed']) ? now()->subDays(rand(1, 40)) : null,
                'payment_method' => in_array($status, ['disbursed', 'active', 'completed']) ? 'bank_transfer' : null,
                'total_paid' => 0,
                'balance_amount' => $balanceAmount,
                'remarks' => $status !== 'pending' ? 'Approved based on employment history' : null,
            ]);

            // Add payments for active and completed loans
            if (in_array($status, ['active', 'completed'])) {
                $paymentsCount = $status === 'completed' ? $installments : rand(1, min(5, $installments));
                $totalPaid = 0;
                
                for ($i = 0; $i < $paymentsCount; $i++) {
                    $principalAmount = $installmentAmount * 0.90;
                    $interestAmount = $installmentAmount * 0.10;
                    
                    LoanPayment::create([
                        'loan_id' => $loan->id,
                        'payment_date' => now()->subDays(rand(1, 30)),
                        'amount' => $installmentAmount,
                        'principal_amount' => $principalAmount,
                        'interest_amount' => $interestAmount,
                        'payment_method' => 'salary_deduction',
                        'reference_number' => 'PAY' . date('Ymd') . rand(1000, 9999),
                        'processed_by' => User::where('role', 'admin')->first()?->id,
                    ]);
                    
                    $totalPaid += $installmentAmount;
                }
                
                $loan->update([
                    'total_paid' => $totalPaid,
                    'balance_amount' => ($amount * (1 + $interestRate / 100)) - $totalPaid,
                    'status' => $status === 'completed' ? 'completed' : 'active',
                    'closed_by' => $status === 'completed' ? User::where('role', 'admin')->first()?->id : null,
                    'closed_date' => $status === 'completed' ? now()->subDays(rand(1, 10)) : null,
                ]);
            }
        }

        $this->command->info('Loans seeded successfully!');
    }

    private function getPurpose($type)
    {
        $purposes = [
            'personal' => ['Home renovation', 'Wedding expenses', 'Family emergency', 'Debt consolidation'],
            'medical' => ['Medical treatment', 'Surgery expenses', 'Hospitalization', 'Medical emergency'],
            'education' => ['Higher education', 'Professional certification', 'Children education', 'Training course'],
            'housing' => ['House purchase', 'Home construction', 'Property down payment', 'Home improvement'],
            'emergency' => ['Urgent family need', 'Accident expenses', 'Funeral expenses', 'Natural disaster'],
        ];

        return $purposes[$type][rand(0, count($purposes[$type]) - 1)];
    }
}
