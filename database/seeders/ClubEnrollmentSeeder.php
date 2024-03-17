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
        $student_ids = Student::pluck('id')->all();
        $club_ids = Club::pluck('id')->all();
        foreach ($student_ids as $id) {
            $random_club_ids = Arr::random($club_ids, 3);
            foreach ($random_club_ids as $club_id) {
                ClubEnrollment::create(
                    array(
                        'student_id' => $id,
                        'club_id' => $club_id,
                    ));
            }
        }
    }
}
