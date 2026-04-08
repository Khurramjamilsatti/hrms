<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Morning Shift',
                'start_time' => '09:00',
                'end_time' => '17:00',
                'grace_period_minutes' => 15,
            ],
            [
                'name' => 'Evening Shift',
                'start_time' => '14:00',
                'end_time' => '22:00',
                'grace_period_minutes' => 15,
            ],
            [
                'name' => 'Night Shift',
                'start_time' => '22:00',
                'end_time' => '06:00',
                'grace_period_minutes' => 15,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
