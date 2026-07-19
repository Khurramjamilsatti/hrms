<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->foreignId('short_leave_id')
                ->nullable()
                ->unique()
                ->after('id')
                ->constrained('short_leaves')
                ->cascadeOnDelete();
        });

        $now = now();
        $typeIds = [];

        foreach ([
            'short_leave' => [
                'name' => 'Short Leave',
                'description' => 'System leave type used for synchronized short-leave requests.',
            ],
            'exemption' => [
                'name' => 'Attendance Exemption',
                'description' => 'System leave type used for synchronized attendance-exemption requests.',
            ],
        ] as $category => $type) {
            $existingId = DB::table('leave_types')->where('name', $type['name'])->value('id');

            if (! $existingId) {
                $existingId = DB::table('leave_types')->insertGetId([
                    ...$type,
                    'days_per_year' => 0,
                    'is_paid' => true,
                    'is_carry_forward' => false,
                    'max_carry_forward_days' => 0,
                    'requires_document' => false,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $typeIds[$category] = $existingId;
        }

        DB::table('short_leaves')->orderBy('id')->each(function (object $shortLeave) use ($typeIds, $now) {
            $status = in_array($shortLeave->status, ['pending', 'approved', 'rejected', 'cancelled'], true)
                ? $shortLeave->status
                : 'pending';

            $approvalLevel = match ($status) {
                'approved' => 'final_approved',
                'rejected' => 'rejected',
                'cancelled' => 'cancelled',
                default => 'pending',
            };

            $label = $shortLeave->category === 'exemption' ? 'Exemption' : 'Short Leave';
            $timeRange = $shortLeave->from_time && $shortLeave->to_time
                ? " ({$shortLeave->from_time} - {$shortLeave->to_time})"
                : '';

            DB::table('leave_applications')->insert([
                'short_leave_id' => $shortLeave->id,
                'employee_id' => $shortLeave->employee_id,
                'leave_type_id' => $typeIds[$shortLeave->category] ?? $typeIds['short_leave'],
                'start_date' => $shortLeave->date,
                'end_date' => $shortLeave->date,
                'total_days' => $shortLeave->category === 'short_leave'
                    ? round(((int) $shortLeave->duration_minutes) / 480, 2)
                    : 0,
                'reason' => "{$label}{$timeRange}: {$shortLeave->reason}",
                'status' => $status,
                'approval_level' => $approvalLevel,
                'final_approved_by' => $shortLeave->approved_by,
                'final_approval_remarks' => $shortLeave->approval_remarks,
                'final_approved_at' => $shortLeave->approved_at,
                'created_at' => $shortLeave->created_at ?? $now,
                'updated_at' => $shortLeave->updated_at ?? $now,
            ]);
        });
    }

    public function down(): void
    {
        DB::table('leave_applications')->whereNotNull('short_leave_id')->delete();

        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropUnique(['short_leave_id']);
            $table->dropConstrainedForeignId('short_leave_id');
        });

        foreach (['Short Leave', 'Attendance Exemption'] as $name) {
            $typeId = DB::table('leave_types')->where('name', $name)->value('id');

            if ($typeId && ! DB::table('leave_applications')->where('leave_type_id', $typeId)->exists()) {
                DB::table('leave_types')->where('id', $typeId)->delete();
            }
        }
    }
};
