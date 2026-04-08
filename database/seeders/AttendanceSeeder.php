<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        
        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please run EmployeeSeeder first.');
            return;
        }

        // Get default shift
        $defaultShift = Shift::where('name', 'Morning')->first();
        if (!$defaultShift) {
            $defaultShift = Shift::first();
        }

        // Generate attendance for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Skip weekends (Saturday and Sunday)
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }

                // 90% attendance rate - 10% random absences/leaves
                $attendanceType = rand(1, 100);
                
                if ($attendanceType <= 85) {
                    // Present - on time
                    $this->createPresentAttendance($employee, $currentDate->copy());
                } elseif ($attendanceType <= 92) {
                    // Present - late arrival
                    $this->createLateAttendance($employee, $currentDate->copy());
                } elseif ($attendanceType <= 95) {
                    // Half day
                    $this->createHalfDayAttendance($employee, $currentDate->copy());
                } elseif ($attendanceType <= 98) {
                    // On leave
                    $this->createLeaveAttendance($employee, $currentDate->copy());
                } else {
                    // Absent
                    $this->createAbsentAttendance($employee, $currentDate->copy());
                }

                $currentDate->addDay();
            }
        }

        $this->command->info('Attendance records created successfully for ' . $employees->count() . ' employees over 30 days.');
    }

    private function createPresentAttendance(Employee $employee, Carbon $date): void
    {
        $shift = Shift::where('name', 'Morning')->first();
        if (!$shift) {
            $shift = Shift::first();
        }
        
        // Parse shift times
        $shiftStart = Carbon::parse($shift->start_time);
        $checkIn = $date->copy()->setTime($shiftStart->hour, $shiftStart->minute)
            ->addMinutes(rand(-10, 5)); // Arrive 10 min early to 5 min after shift
        
        $shiftEnd = Carbon::parse($shift->end_time);
        $checkOut = $date->copy()->setTime($shiftEnd->hour, $shiftEnd->minute)
            ->addMinutes(rand(-15, 30)); // Leave 15 min early to 30 min after shift
        
        $workedHours = $checkIn->floatDiffInHours($checkOut);

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $date->format('Y-m-d'),
            'check_in' => $checkIn->format('H:i:s'),
            'check_out' => $checkOut->format('H:i:s'),
            'status' => 'present',
            'working_hours' => round($workedHours, 2),
            'overtime_hours' => max(0, round($workedHours - 8, 2)),
            'remarks' => null,
        ]);
    }

    private function createLateAttendance(Employee $employee, Carbon $date): void
    {
        $shift = Shift::where('name', 'Morning')->first();
        if (!$shift) {
            $shift = Shift::first();
        }
        
        // Late by 20-60 minutes
        $shiftStart = Carbon::parse($shift->start_time);
        $lateMinutes = rand(20, 60);
        $checkIn = $date->copy()->setTime($shiftStart->hour, $shiftStart->minute)
            ->addMinutes($lateMinutes);
        
        $shiftEnd = Carbon::parse($shift->end_time);
        $checkOut = $date->copy()->setTime($shiftEnd->hour, $shiftEnd->minute)
            ->addMinutes(rand(0, 15));
        
        $workedHours = $checkIn->floatDiffInHours($checkOut);

        $reasons = [
            'Traffic jam',
            'Late waking up',
            'Personal emergency',
            'Public transport delay'
        ];

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $date->format('Y-m-d'),
            'check_in' => $checkIn->format('H:i:s'),
            'check_out' => $checkOut->format('H:i:s'),
            'status' => 'late',
            'working_hours' => round($workedHours, 2),
            'overtime_hours' => 0,
            'remarks' => $reasons[array_rand($reasons)] . ' (Late by ' . $lateMinutes . ' minutes)',
        ]);
    }

    private function createHalfDayAttendance(Employee $employee, Carbon $date): void
    {
        $shift = Shift::where('name', 'Morning')->first();
        if (!$shift) {
            $shift = Shift::first();
        }
        
        $shiftStart = Carbon::parse($shift->start_time);
        $checkIn = $date->copy()->setTime($shiftStart->hour, $shiftStart->minute)
            ->addMinutes(rand(-5, 10));
        
        // Leave after 4-5 hours
        $workedHours = rand(4, 5);
        $checkOut = $checkIn->copy()->addHours($workedHours);

        $reasons = [
            'Medical appointment',
            'Personal emergency',
            'Early leave approved',
            'Family matter',
            'Doctor appointment'
        ];

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $date->format('Y-m-d'),
            'check_in' => $checkIn->format('H:i:s'),
            'check_out' => $checkOut->format('H:i:s'),
            'status' => 'half_day',
            'working_hours' => round($workedHours, 2),
            'overtime_hours' => 0,
            'remarks' => $reasons[array_rand($reasons)],
        ]);
    }

    private function createLeaveAttendance(Employee $employee, Carbon $date): void
    {
        $leaveTypes = ['Annual Leave', 'Sick Leave', 'Casual Leave', 'Emergency Leave'];

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $date->format('Y-m-d'),
            'check_in' => null,
            'check_out' => null,
            'status' => 'on_leave',
            'working_hours' => 0,
            'overtime_hours' => 0,
            'remarks' => $leaveTypes[array_rand($leaveTypes)] . ' - Approved',
        ]);
    }

    private function createAbsentAttendance(Employee $employee, Carbon $date): void
    {
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $date->format('Y-m-d'),
            'check_in' => null,
            'check_out' => null,
            'status' => 'absent',
            'working_hours' => 0,
            'overtime_hours' => 0,
            'remarks' => 'Unmarked absence',
        ]);
    }
}
