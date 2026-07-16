<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Employee;
use Illuminate\Http\Request;

trait AuthorizesEmployeeResource
{
    /**
     * Ensure the current user can access a record belonging to the given employee.
     * Mirrors typical index scoping: own / team / department / HR+admin.
     */
    protected function assertCanAccessEmployeeRecord(Request $request, ?int $employeeId): void
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if (in_array($user->role, ['super_admin', 'hr_admin', 'admin'], true)) {
            return;
        }

        if ($employeeId && $user->employee && (int) $user->employee->id === (int) $employeeId) {
            return;
        }

        if ($user->hasRole('manager') && $employeeId) {
            $onTeam = Employee::where('id', $employeeId)
                ->where('manager_id', $user->id)
                ->exists();
            if ($onTeam) {
                return;
            }
        }

        if ($user->hasRole('section_head') && $employeeId && $user->employee?->department_id) {
            $sameDept = Employee::where('id', $employeeId)
                ->where('department_id', $user->employee->department_id)
                ->exists();
            if ($sameDept) {
                return;
            }
        }

        abort(403, 'You are not authorized to access this record.');
    }

    /**
     * Helpdesk tickets: creators own them; assignees and staff can access.
     */
    protected function assertCanAccessHelpdeskTicket(Request $request, object $ticket): void
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if (in_array($user->role, ['super_admin', 'hr_admin', 'admin', 'manager', 'section_head'], true)) {
            return;
        }

        if (!empty($ticket->assigned_to) && (int) $ticket->assigned_to === (int) $user->id) {
            return;
        }

        if ($user->employee && (int) $user->employee->id === (int) $ticket->employee_id) {
            return;
        }

        abort(403, 'You are not authorized to access this ticket.');
    }
}
