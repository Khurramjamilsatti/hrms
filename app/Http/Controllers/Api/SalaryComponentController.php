<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalaryComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryComponentController extends Controller
{
    // Get all salary components (master list)
    public function index()
    {
        $components = SalaryComponent::orderBy('type')
            ->orderBy('name')
            ->get();

        return response()->json($components);
    }

    // Create new salary component
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:salary_components,code|max:50',
            'name' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction',
            'calculation_type' => 'required|in:fixed,percentage',
            'is_taxable' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $component = SalaryComponent::create($validated);

        return response()->json([
            'message' => 'Salary component created successfully',
            'component' => $component
        ], 201);
    }

    // Update salary component
    public function updateMaster(Request $request, $id)
    {
        $component = SalaryComponent::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:salary_components,code,' . $id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction',
            'calculation_type' => 'required|in:fixed,percentage',
            'is_taxable' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $component->update($validated);

        return response()->json([
            'message' => 'Salary component updated successfully',
            'component' => $component
        ]);
    }

    // Delete salary component
    public function destroyMaster($id)
    {
        $component = SalaryComponent::findOrFail($id);
        
        // Check if component is used by any employee
        $isUsed = EmployeeSalaryComponent::where('salary_component_id', $id)->exists();
        
        if ($isUsed) {
            return response()->json([
                'message' => 'Cannot delete this component as it is being used by employees'
            ], 422);
        }

        $component->delete();

        return response()->json(['message' => 'Salary component deleted successfully']);
    }

    // Get employee's salary with components
    public function getEmployeeSalary($employeeId)
    {
        $employee = Employee::with([
            'salaries.components.salaryComponent',
            'salaries' => function($query) {
                $query->orderBy('effective_from', 'desc');
            }
        ])->findOrFail($employeeId);

        return response()->json($employee->salaries);
    }

    // Create or update employee salary with components
    public function storeEmployeeSalary(Request $request, $employeeId)
    {
        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'payment_mode' => 'nullable|in:cash,bank_transfer,cheque',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'remarks' => 'nullable|string',
            'components' => 'nullable|array',
            'components.*.salary_component_id' => 'required|exists:salary_components,id',
            'components.*.amount' => 'required|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($employeeId);

        DB::beginTransaction();
        try {
            // Close previous salary if exists
            $previousSalary = EmployeeSalary::where('employee_id', $employeeId)
                ->whereNull('effective_to')
                ->first();
            
            if ($previousSalary) {
                $previousSalary->update([
                    'effective_to' => now()->subDay()
                ]);
            }

            // Create new salary
            $salary = EmployeeSalary::create([
                'employee_id' => $employeeId,
                'basic_salary' => $validated['basic_salary'],
                'effective_from' => $validated['effective_from'],
                'effective_to' => $validated['effective_to'] ?? null,
                'payment_mode' => $validated['payment_mode'] ?? 'bank_transfer',
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Add components
            if (isset($validated['components'])) {
                foreach ($validated['components'] as $component) {
                    EmployeeSalaryComponent::create([
                        'employee_salary_id' => $salary->id,
                        'salary_component_id' => $component['salary_component_id'],
                        'amount' => $component['amount'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Employee salary updated successfully',
                'salary' => $salary->load('components.salaryComponent')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating salary: ' . $e->getMessage()], 500);
        }
    }

    // Update salary component amount
    public function updateComponent(Request $request, $componentId)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $component = EmployeeSalaryComponent::findOrFail($componentId);
        $component->update($validated);

        return response()->json([
            'message' => 'Component updated successfully',
            'component' => $component->load('salaryComponent')
        ]);
    }

    // Delete salary component
    public function deleteComponent($componentId)
    {
        $component = EmployeeSalaryComponent::findOrFail($componentId);
        $component->delete();

        return response()->json(['message' => 'Component deleted successfully']);
    }

    // Apply salary increment to an employee
    public function applyIncrement(Request $request, $employeeId)
    {
        $validated = $request->validate([
            'increment_type' => 'required|in:percentage,fixed',
            'increment_value' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $employee = Employee::findOrFail($employeeId);
            
            // Get current active salary
            $currentSalary = EmployeeSalary::where('employee_id', $employeeId)
                ->whereNull('effective_to')
                ->with('components')
                ->first();

            if (!$currentSalary) {
                return response()->json(['message' => 'No active salary found for employee'], 404);
            }

            // Calculate new basic salary
            $newBasicSalary = $currentSalary->basic_salary;
            if ($validated['increment_type'] === 'percentage') {
                $incrementAmount = ($currentSalary->basic_salary * $validated['increment_value']) / 100;
                $newBasicSalary += $incrementAmount;
            } else {
                $newBasicSalary += $validated['increment_value'];
            }

            // Close current salary record
            $currentSalary->update([
                'effective_to' => Carbon::parse($validated['effective_from'])->subDay()->format('Y-m-d'),
            ]);

            // Create new salary record
            $newSalary = EmployeeSalary::create([
                'employee_id' => $employeeId,
                'basic_salary' => $newBasicSalary,
                'effective_from' => $validated['effective_from'],
                'effective_to' => null,
                'remarks' => $validated['remarks'] ?? 'Salary increment applied',
            ]);

            // Copy components from previous salary and apply increment
            foreach ($currentSalary->components as $component) {
                $newAmount = $component->amount;
                
                // Apply increment to allowances (earnings) but not deductions
                if ($component->salaryComponent->type === 'earning') {
                    if ($validated['increment_type'] === 'percentage') {
                        $componentIncrement = ($component->amount * $validated['increment_value']) / 100;
                        $newAmount += $componentIncrement;
                    } else {
                        // For fixed increment, distribute proportionally
                        $proportion = $component->amount / $currentSalary->basic_salary;
                        $newAmount += ($validated['increment_value'] * $proportion);
                    }
                }

                EmployeeSalaryComponent::create([
                    'employee_salary_id' => $newSalary->id,
                    'salary_component_id' => $component->salary_component_id,
                    'amount' => round($newAmount, 2),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Salary increment applied successfully',
                'salary' => $newSalary->load('components.salaryComponent'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to apply increment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get salary increment history
    public function getIncrementHistory($employeeId)
    {
        $salaries = EmployeeSalary::where('employee_id', $employeeId)
            ->with('components.salaryComponent')
            ->orderBy('effective_from', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $history = [];
        for ($i = 0; $i < count($salaries); $i++) {
            $current = $salaries[$i];
            $previous = $i < count($salaries) - 1 ? $salaries[$i + 1] : null;

            $incrementAmount = 0;
            $incrementPercentage = 0;

            if ($previous) {
                $incrementAmount = $current->basic_salary - $previous->basic_salary;
                $incrementPercentage = ($incrementAmount / $previous->basic_salary) * 100;
            }

            $history[] = [
                'id' => $current->id,
                'effective_from' => $current->effective_from,
                'effective_to' => $current->effective_to,
                'basic_salary' => $current->basic_salary,
                'gross_salary' => $current->gross_salary,
                'previous_salary' => $previous ? $previous->basic_salary : null,
                'increment_amount' => $incrementAmount,
                'increment_percentage' => round($incrementPercentage, 2),
                'components' => $current->components,
                'remarks' => $current->remarks,
                'is_active' => $current->effective_to === null,
            ];
        }

        return response()->json($history);
    }
}
