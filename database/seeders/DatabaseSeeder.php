<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ClubEnrollmentHistory;
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
            TeacherSeeder::class,
            StudentClassSeeder::class,
            StudentSeeder::class,
            ClubSeeder::class,
            ClubEnrollmentSeeder::class,
            ClubEnrollmentHistorySeeder::class,
            ClubScheduleSeeder::class,
            ClubScheduleFeeSeeder::class,
            ClubSessionSeeder::class,
            AttendanceSeeder::class,
            AbsenceReportSeeder::class,
            CommentSeeder::class,
            NotificationSeeder::class
        ]);
    }
}
