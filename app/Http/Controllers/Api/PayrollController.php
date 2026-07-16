<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEmployeeResource;
use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Services\PayrollGenerationService;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    use AuthorizesEmployeeResource;

    public function __construct(protected PayrollGenerationService $payrollService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Payroll::with(['employee.user', 'processedBy']);

        if ($user->hasRole('employee')) {
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('manager') || $user->hasRole('section_head')) {
            return response()->json(['message' => 'Unauthorized access to payroll'], 403);
        }

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('month')) {
            $query->where('month', $request->month);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('email', 'ilike', "%{$search}%")
                                ->orWhere('name', 'ilike', "%{$search}%");
                  });
            });
        }

        $payrolls = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($payrolls);
    }

    public function generateMonthlyPayroll(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['hr_admin', 'super_admin', 'admin'], true)) {
            return response()->json(['message' => 'Unauthorized to generate payroll'], 403);
        }

        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'regenerate' => 'sometimes|boolean',
        ]);

        try {
            $result = $this->payrollService->generate(
                (int) $validated['month'],
                (int) $validated['year'],
                $user->id,
                (bool) ($validated['regenerate'] ?? false)
            );

            return response()->json([
                'message' => 'Payroll generated successfully from attendance, leaves, and timesheets',
                'count' => $result['count'],
                'payrolls' => $result['payrolls'],
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error generating payroll: '.$e->getMessage()], 500);
        }
    }

    public function show(Request $request, Payroll $payroll)
    {
        $this->assertCanAccessEmployeeRecord($request, $payroll->employee_id);

        return response()->json($payroll->load([
            'employee.user',
            'employee.department',
            'details.salaryComponent',
            'processedBy',
        ]));
    }

    public function processPayroll(Payroll $payroll)
    {
        if ($payroll->status !== 'draft') {
            return response()->json(['message' => 'Payroll already processed'], 400);
        }

        $payroll->update(['status' => 'processed']);

        return response()->json($payroll);
    }

    public function markAsPaid(Request $request, Payroll $payroll)
    {
        if ($payroll->status !== 'processed') {
            return response()->json(['message' => 'Payroll must be processed first'], 400);
        }

        $validated = $request->validate([
            'payment_date' => 'required|date',
        ]);

        $payroll->update([
            'status' => 'paid',
            'payment_date' => $validated['payment_date'],
        ]);

        return response()->json($payroll);
    }
}
