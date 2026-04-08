<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDeployment;
use App\Models\DeploymentExtension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeploymentController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeDeployment::with(['employee.user', 'employee.department', 'approver']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('deployment_type')) {
            $query->where('deployment_type', $request->deployment_type);
        }

        if ($request->has('departure_from_long_leave')) {
            $query->where('departure_from_long_leave', $request->boolean('departure_from_long_leave'));
        }

        $deployments = $query->orderBy('start_date', 'desc')->paginate(20);

        return response()->json($deployments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'deployment_type' => 'required|in:domestic,international,project,temporary,permanent',
            'project_name' => 'nullable|string',
            'client_name' => 'nullable|string',
            'location' => 'required|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'expected_return_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'role' => 'nullable|string',
            'reporting_manager' => 'nullable|string',
            'accommodation_type' => 'nullable|string',
            'accommodation_details' => 'nullable|string',
            'transport_details' => 'nullable|string',
            'allowance_amount' => 'nullable|numeric|min:0',
            'departure_from_long_leave' => 'nullable|boolean',
            'long_leave_start_date' => 'nullable|required_if:departure_from_long_leave,true|date',
            'long_leave_end_date' => 'nullable|required_if:departure_from_long_leave,true|date',
            'notes' => 'nullable|string',
        ]);

        // Generate deployment number
        $lastDeployment = EmployeeDeployment::orderBy('id', 'desc')->first();
        $deploymentNumber = 'DEP' . date('Y') . str_pad(($lastDeployment ? $lastDeployment->id + 1 : 1), 5, '0', STR_PAD_LEFT);

        $validated['deployment_number'] = $deploymentNumber;
        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id();

        $deployment = EmployeeDeployment::create($validated);

        return response()->json([
            'message' => 'Deployment created successfully',
            'deployment' => $deployment->load(['employee.user', 'approver'])
        ], 201);
    }

    public function show(EmployeeDeployment $deployment)
    {
        return response()->json($deployment->load([
            'employee.user',
            'employee.department',
            'approver',
            'creator',
            'extensions.approver'
        ]));
    }

    public function update(Request $request, EmployeeDeployment $deployment)
    {
        $validated = $request->validate([
            'deployment_type' => 'sometimes|in:domestic,international,project,temporary,permanent',
            'project_name' => 'nullable|string',
            'client_name' => 'nullable|string',
            'location' => 'sometimes|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date',
            'expected_return_date' => 'nullable|date',
            'actual_return_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'role' => 'nullable|string',
            'reporting_manager' => 'nullable|string',
            'accommodation_details' => 'nullable|string',
            'transport_details' => 'nullable|string',
            'allowance_amount' => 'nullable|numeric|min:0',
            'travel_ticket_status' => 'nullable|in:pending,booked,issued,used,cancelled',
            'visa_status' => 'nullable|in:not_required,pending,in_process,approved,rejected,issued',
            'insurance_status' => 'nullable|in:pending,active,expired,not_required',
            'notes' => 'nullable|string',
        ]);

        $deployment->update($validated);

        return response()->json([
            'message' => 'Deployment updated successfully',
            'deployment' => $deployment->load(['employee.user'])
        ]);
    }

    public function approve(Request $request, EmployeeDeployment $deployment)
    {
        $deployment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Deployment approved successfully',
            'deployment' => $deployment
        ]);
    }

    public function activate(EmployeeDeployment $deployment)
    {
        $deployment->update(['status' => 'active']);

        return response()->json([
            'message' => 'Deployment activated successfully',
            'deployment' => $deployment
        ]);
    }

    public function complete(Request $request, EmployeeDeployment $deployment)
    {
        $validated = $request->validate([
            'actual_return_date' => 'required|date',
        ]);

        $deployment->update([
            'status' => 'completed',
            'actual_return_date' => $validated['actual_return_date'],
        ]);

        return response()->json([
            'message' => 'Deployment completed successfully',
            'deployment' => $deployment
        ]);
    }

    public function extend(Request $request, EmployeeDeployment $deployment)
    {
        $validated = $request->validate([
            'new_end_date' => 'required|date|after:' . $deployment->end_date,
            'reason' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $extension = DeploymentExtension::create([
                'deployment_id' => $deployment->id,
                'extension_number' => $deployment->extension_count + 1,
                'previous_end_date' => $deployment->end_date,
                'new_end_date' => $validated['new_end_date'],
                'reason' => $validated['reason'],
                'requested_by' => auth()->id(),
                'status' => 'pending',
            ]);

            $deployment->increment('extension_count');
            $deployment->update([
                'current_extension_end_date' => $validated['new_end_date'],
                'status' => 'extended',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Extension request created successfully',
                'extension' => $extension,
                'deployment' => $deployment
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create extension'], 500);
        }
    }

    public function approveExtension(Request $request, DeploymentExtension $extension)
    {
        DB::beginTransaction();
        try {
            $extension->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $extension->deployment->update([
                'end_date' => $extension->new_end_date,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Extension approved successfully',
                'extension' => $extension
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to approve extension'], 500);
        }
    }

    public function destroy(EmployeeDeployment $deployment)
    {
        if (in_array($deployment->status, ['active', 'extended'])) {
            return response()->json(['message' => 'Cannot delete active deployment'], 400);
        }

        $deployment->delete();

        return response()->json(['message' => 'Deployment deleted successfully']);
    }

    public function getEmployeeDeploymentHistory($employeeId)
    {
        $deployments = EmployeeDeployment::where('employee_id', $employeeId)
            ->with(['approver', 'creator', 'extensions'])
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json($deployments);
    }
}
