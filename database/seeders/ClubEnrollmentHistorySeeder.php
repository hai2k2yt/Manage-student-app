<?php

namespace Database\Seeders;

use App\Enums\ClubEnrollmentStatusEnum;
use App\Models\ClubEnrollment;
use App\Models\ClubEnrollmentHistory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubEnrollmentHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $club_enrollments = ClubEnrollment::pluck('id')->all();
        foreach ($club_enrollments as $enrollment) {
            $i = random_int(2,4);
            $from = Carbon::now()->subWeeks($i + 1)->format('Y-m-d');

            ClubEnrollmentHistory::create(
                array(
                    'club_enrollment_id' => $enrollment,
                    'from' => $from,
                    'to' => null,
                    'status' => ClubEnrollmentStatusEnum::STUDY
                ));
        }
    }
}
