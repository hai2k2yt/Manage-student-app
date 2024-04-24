<?php

namespace Database\Seeders;

use App\Models\ClubSchedule;
use App\Models\ClubSession;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedule_items = ClubSchedule::all(['day_of_week', 'schedule_code']);
        foreach ($schedule_items as $schedule) {
            for ($i = 0; $i < 3; $i++) {
                $date = Carbon::now()->subWeeks($i + 1)->startOfWeek($schedule->day_of_week + 1)->format('Y-m-d');
                ClubSession::create([
                    'session_code' => $schedule->schedule_code . '_week ' . (3 - $i),
                    'schedule_code' => $schedule->schedule_code,
                    'session_name' => 'Buổi học '. $schedule->schedule_code . '_week ' . (3 - $i),
                    'date' => $date
                ]);
            }

        }
//        ClubSession::factory(40)->create();
    }
}
