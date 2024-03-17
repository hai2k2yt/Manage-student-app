<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StudentClassSeeder::class,
            StudentSeeder::class,
            ClubSeeder::class,
            ClubEnrollmentSeeder::class,
            ClubScheduleSeeder::class,
            ClubSessionSeeder::class,
            AttendanceSeeder::class,
            AbsenceReportSeeder::class,
            CommentSeeder::class,
            NotificationSeeder::class
        ]);
    }
}
