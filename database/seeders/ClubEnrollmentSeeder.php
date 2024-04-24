<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\ClubEnrollment;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ClubEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student_codes = Student::pluck('student_code')->all();
        $club_codes = Club::pluck('club_code')->all();
        foreach ($student_codes as $code) {
            $random_count = rand(2, 4);
            $random_club_ids = Arr::random($club_codes, $random_count);
            foreach ($random_club_ids as $club_id) {
                ClubEnrollment::create(
                    array(
                        'student_code' => $code,
                        'club_code' => $club_id,
                    ));
            }
        }
    }
}
