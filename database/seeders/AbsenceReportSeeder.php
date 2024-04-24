<?php

namespace Database\Seeders;

use App\Enums\AbsenceReportEnum;
use App\Models\AbsenceReport;
use App\Models\ClubSession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class AbsenceReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = ClubSession::all();
        foreach ($sessions as $session) {
            $students = $session->schedule->club->students;
            $numRandomStudents = 2;
            $randomStudents = $students->random($numRandomStudents);
            $absence_report_values = AbsenceReportEnum::values();
            foreach ($randomStudents as $student) {
                AbsenceReport::create([
                    'session_code' => $session->session_code,
                    'student_code' => $student->student_code,
                    'reason' => fake('vi_VN')->word(),
                    'status' => fake()->randomElement($absence_report_values)
                ]);
            }
        }
    }
}
