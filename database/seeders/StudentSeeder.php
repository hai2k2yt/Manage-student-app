<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 0; $i < 20; $i++) {
            Student::create([
                'student_code' => 'STUDENT_' . ($i + 1),
                'name' => fake('vi_VN')->name(),
                'user_id' =>  User::where('role', RoleEnum::PARENT)->pluck('id')->random(),
                'class_code' => StudentClass::pluck('class_code')->random()
            ]);
        }
//        Student::factory(20)->create();
    }
}
