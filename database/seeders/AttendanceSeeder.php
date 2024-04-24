<?php

namespace Database\Seeders;

use App\Enums\AttendanceEnum;
use App\Models\Attendance;
use App\Models\ClubSession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = ClubSession::all();
        foreach ($sessions as $session) {
            $students = $session->schedule->club->students;
            $attendance_statuses = AttendanceEnum::values();
            foreach ($students as $student) {
                Attendance::create([
                    'session_code' => $session->session_code,
                    'student_code' => $student->student_code,
                    'present' => fake()->randomElement($attendance_statuses)
                ]);
            }
        }
        //
    }
}
