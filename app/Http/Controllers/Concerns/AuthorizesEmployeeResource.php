<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait AuthorizesEmployeeResource
{
    /**
     * Permission map per module for list/show scoping.
     *
     * @return array{manage: string[], approve: string[], view_all: string[]}
     */
    protected function modulePermissionConfig(string $module): array
    {
        return match ($module) {
            'loans' => [
                'manage' => ['loans.manage'],
                'approve' => ['loans.approve'],
                'view_all' => [],
            ],
            'salary_advances' => [
                'manage' => [],
                'approve' => ['salary_advances.approve'],
                'view_all' => [],
            ],
            'leaves' => [
                'manage' => ['leaves.manage'],
                'approve' => ['leaves.approve', 'leaves.reject'],
                'view_all' => [],
            ],
            'payroll' => [
                'manage' => ['payroll.manage', 'payroll.generate', 'payroll.create', 'payroll.process'],
                'approve' => [],
                'view_all' => ['payroll.view'],
            ],
            'travel' => [
                'manage' => ['travel.manage'],
                'approve' => ['travel.approve'],
                'view_all' => [],
            ],
            'attendance' => [
                'manage' => ['attendance.manage'],
                'approve' => [],
                'view_all' => [],
            ],
            'overtime' => [
                'manage' => [],
                'approve' => ['overtime.approve'],
                'view_all' => [],
            ],
            'timesheets' => [
                'manage' => ['timesheets.manage'],
                'approve' => ['timesheets.approve'],
                'view_all' => [],
            ],
            'shifts' => [
                'manage' => ['shifts.manage', 'shifts.create'],
                'approve' => ['shifts.assign'],
                'view_all' => [],
            ],
            'files' => [
                'manage' => ['files.manage'],
                'approve' => [],
                'view_all' => [],
            ],
            default => [
                'manage' => [],
                'approve' => [],
                'view_all' => [],
            ],
        };
    }

    /**
     * Restrict an employees query to records the current user may see.
     */
    protected function scopeAccessibleEmployeesList(Builder $query, Request $request): void
    {
        $user = $request->user();

        if (! $user) {
            $query->whereRaw('1 = 0');

            return;
        }

        if (in_array($user->role, ['super_admin', 'hr_admin', 'admin'], true)) {
            return;
        }

        if ($user->hasRole('manager') || $user->hasRole('section_head')) {
            $ids = $this->accessibleEmployeeIds($user);
            if (empty($ids)) {
                $query->whereRaw('1 = 0');

                return;
            }
            $query->whereIn('id', $ids);

            return;
        }

        if ($user->employee) {
            $query->where('id', $user->employee->id);
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    /**
     * Restrict a query to employee-linked records the user may view.
     */
    protected function scopeToAccessibleEmployees(
        Builder $query,
        Request $request,
        string $module,
        string $employeeColumn = 'employee_id'
    ): void {
        $user = $request->user();

        if (!$user) {
            $query->whereRaw('1 = 0');

            return;
        }

        // Managers may only access their own files (upload/view/delete own)
        if ($module === 'files' && $user->hasRole('manager')) {
            if ($user->employee) {
                $query->where($employeeColumn, $user->employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }

            return;
        }

        $config = $this->modulePermissionConfig($module);

        if ($this->canViewAllEmployeeRecords($user, $module, $config)) {
            return;
        }

        if ($this->canViewTeamEmployeeRecords($user, $config)) {
            $employeeIds = $this->accessibleEmployeeIds($user);
            if (empty($employeeIds)) {
                $query->whereRaw('1 = 0');

                return;
            }
            $query->whereIn($employeeColumn, $employeeIds);

            return;
        }

        if ($user->employee) {
            $query->where($employeeColumn, $user->employee->id);
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    protected function canViewAllEmployeeRecords(User $user, string $module, array $config): bool
    {
        // Managers / section heads are always limited to their team / section
        if ($user->hasRole('manager') || $user->hasRole('section_head')) {
            return false;
        }

        if ($user->isSuperAdmin() && $user->role === 'super_admin') {
            return true;
        }

        foreach (array_merge($config['manage'], $config['view_all']) as $permission) {
            if ($permission && $user->hasPermission($permission)) {
                return true;
            }
        }

        // HR approvers can see all records in their module (e.g. loan approvals).
        if (in_array($user->role, ['hr_admin', 'admin'], true)) {
            foreach ($config['approve'] as $permission) {
                if ($permission && $user->hasPermission($permission)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function canViewTeamEmployeeRecords(User $user, array $config): bool
    {
        if ($user->hasRole('manager') || $user->hasRole('section_head')) {
            return true;
        }

        foreach ($config['approve'] as $permission) {
            if ($permission && $user->hasPermission($permission)) {
                return true;
            }
        }

        foreach ($config['manage'] as $permission) {
            if ($permission && $user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int[]
     */
    protected function accessibleEmployeeIds(User $user): array
    {
        $ids = [];

        if ($user->employee) {
            $ids[] = (int) $user->employee->id;
        }

        if ($user->hasRole('manager')) {
            $ids = array_merge(
                $ids,
                Employee::where('manager_id', $user->id)->pluck('id')->map(fn ($id) => (int) $id)->all()
            );
        }

        if ($user->hasRole('section_head') && $user->employee?->department_id) {
            $ids = array_merge(
                $ids,
                Employee::where('department_id', $user->employee->department_id)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->all()
            );
        }

        return array_values(array_unique($ids));
    }

    /**
     * Ensure the current user can access a record belonging to the given employee.
     */
    protected function assertCanAccessEmployeeRecord(Request $request, ?int $employeeId, ?string $module = null): void
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if ($module) {
            $config = $this->modulePermissionConfig($module);

            // Managers may only access their own files
            if ($module === 'files' && $user->hasRole('manager')) {
                if ($employeeId && $user->employee && (int) $user->employee->id === (int) $employeeId) {
                    return;
                }
                abort(403, 'You can only access your own files.');
            }

            if ($this->canViewAllEmployeeRecords($user, $module, $config)) {
                return;
            }

            if ($employeeId && $user->employee && (int) $user->employee->id === (int) $employeeId) {
                return;
            }

            if ($employeeId && $this->canViewTeamEmployeeRecords($user, $config)) {
                if (in_array((int) $employeeId, $this->accessibleEmployeeIds($user), true)) {
                    return;
                }
            }

            abort(403, 'You are not authorized to access this record.');

            return;
        }

        // Legacy fallback for modules without an explicit permission map.
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
     * Resolve employee_id on create: use request value when provided, else current user's employee.
     */
    protected function resolveStoredEmployeeId(Request $request, ?int $employeeId, string $module): int
    {
        if ($employeeId) {
            $this->assertCanAccessEmployeeRecord($request, $employeeId, $module);

            return $employeeId;
        }

        $employee = $request->user()?->employee;
        if (!$employee) {
            abort(422, 'No employee profile is linked to your account.');
        }

        return (int) $employee->id;
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

        if ($user->hasPermission('helpdesk.manage')) {
            return;
        }

        if (in_array($user->role, ['super_admin', 'hr_admin', 'admin', 'manager', 'section_head'], true)) {
            if ($user->hasPermission('helpdesk.view') || $user->hasPermission('helpdesk.update')) {
                return;
            }
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
