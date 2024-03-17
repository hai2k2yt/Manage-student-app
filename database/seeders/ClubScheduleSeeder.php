<?php

namespace Database\Seeders;

use App\Models\ClubSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClubSchedule::factory(20)->create();
    }
}
