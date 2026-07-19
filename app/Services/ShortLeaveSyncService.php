<?php

namespace App\Services;

use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\ShortLeave;

class ShortLeaveSyncService
{
    public const TYPE_NAMES = [
        'short_leave' => 'Short Leave',
        'exemption' => 'Attendance Exemption',
    ];

    public function syncLeaveApplication(ShortLeave $shortLeave): LeaveApplication
    {
        $leaveType = $this->leaveTypeFor($shortLeave);
        $label = $shortLeave->category === 'exemption' ? 'Exemption' : 'Short Leave';
        $timeRange = $shortLeave->from_time && $shortLeave->to_time
            ? " ({$shortLeave->from_time} - {$shortLeave->to_time})"
            : '';

        $approvalLevel = match ($shortLeave->status) {
            'approved' => 'final_approved',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            default => 'pending',
        };

        $approvalFields = [
            'first_approved_by' => null,
            'first_approval_remarks' => null,
            'first_approved_at' => null,
            'final_approved_by' => null,
            'final_approval_remarks' => null,
            'final_approved_at' => null,
        ];

        if (in_array($shortLeave->status, ['approved', 'rejected'], true)) {
            $approvalFields = [
                ...$approvalFields,
                'final_approved_by' => $shortLeave->approved_by,
                'final_approval_remarks' => $shortLeave->approval_remarks,
                'final_approved_at' => $shortLeave->approved_at,
            ];
        }

        return LeaveApplication::updateOrCreate(
            ['short_leave_id' => $shortLeave->id],
            [
                'employee_id' => $shortLeave->employee_id,
                'leave_type_id' => $leaveType->id,
                'start_date' => $shortLeave->date,
                'end_date' => $shortLeave->date,
                'total_days' => $shortLeave->category === 'short_leave'
                    ? number_format($shortLeave->duration_minutes / 480, 2, '.', '')
                    : '0.00',
                'reason' => "{$label}{$timeRange}: {$shortLeave->reason}",
                'status' => $shortLeave->status,
                'approval_level' => $approvalLevel,
                ...$approvalFields,
            ]
        );
    }

    public function syncShortLeave(LeaveApplication $leaveApplication): ?ShortLeave
    {
        if (! $leaveApplication->short_leave_id) {
            return null;
        }

        $shortLeave = $leaveApplication->shortLeave()->first();
        if (! $shortLeave) {
            return null;
        }

        $approverId = null;
        $remarks = null;
        $approvedAt = null;

        if (in_array($leaveApplication->status, ['approved', 'rejected'], true)) {
            $approverId = $leaveApplication->final_approved_by
                ?? $leaveApplication->first_approved_by;
            $remarks = $leaveApplication->final_approval_remarks
                ?? $leaveApplication->first_approval_remarks;
            $approvedAt = $leaveApplication->final_approved_at
                ?? $leaveApplication->first_approved_at;
        }

        $shortLeave->update([
            'status' => $leaveApplication->status,
            'approved_by' => $approverId,
            'approval_remarks' => $remarks,
            'approved_at' => $approvedAt,
        ]);

        return $shortLeave->refresh();
    }

    public function isSyncedLeave(LeaveApplication $leaveApplication): bool
    {
        return $leaveApplication->short_leave_id !== null;
    }

    public function isSystemLeaveType(int $leaveTypeId): bool
    {
        return LeaveType::whereKey($leaveTypeId)
            ->whereIn('name', array_values(self::TYPE_NAMES))
            ->exists();
    }

    private function leaveTypeFor(ShortLeave $shortLeave): LeaveType
    {
        $category = array_key_exists($shortLeave->category, self::TYPE_NAMES)
            ? $shortLeave->category
            : 'short_leave';

        return LeaveType::firstOrCreate(
            ['name' => self::TYPE_NAMES[$category]],
            [
                'description' => $category === 'exemption'
                    ? 'System leave type used for synchronized attendance-exemption requests.'
                    : 'System leave type used for synchronized short-leave requests.',
                'days_per_year' => 0,
                'is_paid' => true,
                'is_carry_forward' => false,
                'max_carry_forward_days' => 0,
                'requires_document' => false,
                'is_active' => true,
            ]
        );
    }
}
