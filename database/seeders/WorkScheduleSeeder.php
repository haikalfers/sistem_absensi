<?php

namespace Database\Seeders;

use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;

class WorkScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Senin-Jumat
        WorkSchedule::create([
            'name' => 'Senin-Jumat',
            'working_days' => [1, 2, 3, 4, 5],  // 1=Senin, 5=Jumat
            'check_in_time' => '08:30',
            'check_out_time' => '16:30',
            'late_tolerance_minutes' => 0,
        ]);

        // Sabtu
        WorkSchedule::create([
            'name' => 'Sabtu',
            'working_days' => [6],              // 6=Sabtu
            'check_in_time' => '08:00',
            'check_out_time' => '13:00',
            'late_tolerance_minutes' => 0,
        ]);
    }
}