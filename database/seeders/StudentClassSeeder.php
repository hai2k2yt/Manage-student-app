<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            StudentClass::create([
                'class_code' => 'CLASS_' . ($i + 1),
                'class_name' => 'Lớp ' . chr($i + 65),
                'teacher_code' => Teacher::pluck('teacher_code')->random()
            ]);
        }
//        StudentClass::factory(5)->create();
    }
}
