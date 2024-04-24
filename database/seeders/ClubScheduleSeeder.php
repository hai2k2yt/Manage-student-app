<?php

namespace Database\Seeders;

use App\Enums\DayOfWeek;
use App\Models\Club;
use App\Models\ClubSchedule;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $club_codes = Club::pluck('club_code')->all();
        foreach ($club_codes as $code) {
            $day_of_weeks = DayOfWeek::values();

            $first_day = fake()->randomElement($day_of_weeks);

            $day_of_weeks = array_diff($day_of_weeks, [$first_day]);

            $second_day = fake()->randomElement($day_of_weeks);

            for ($i = 0; $i < 2; $i++) {
                $day = $i == 0 ? $first_day : $second_day;
                ClubSchedule::create([
                    'schedule_code' => $code . '_DAY' . $day,
                    'schedule_name' => 'Buổi học ' . $day . 'clb ' . $code,
                    'club_code' => $code,
                    'teacher_code' => Teacher::pluck('teacher_code')->random(),
                    'day_of_week' => $day,
                ]);
            }
        }

//        ClubSchedule::factory(20)->create();
    }
}
