<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherUser = User::where('role', 3)->get();
        foreach ($teacherUser as $index => $user) {
            Teacher::create([
                'user_id' => $user->id,
                'teacher_name' => $user->name,
                'teacher_code' => 'TEACHER_ ' . ($index + 1)
            ]);
        }
    }
}
