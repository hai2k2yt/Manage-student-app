<?php

namespace Database\Seeders;

use App\Models\ClubSchedule;
use App\Models\ClubScheduleFee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubScheduleFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedule_codes = ClubSchedule::pluck('schedule_code')->all();
        foreach ($schedule_codes as $schedule_code) {
            ClubScheduleFee::create([
                'schedule_code' => $schedule_code,
                'teacher_fee' => random_int(20,40) * 10000,
                'student_fee' => random_int(20,40) * 1000,
            ]);
        }
    }
}
