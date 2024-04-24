<?php

namespace Database\Seeders;

use App\Models\ClubSession;
use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
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
            foreach ($randomStudents as $student) {
                Comment::create([
                    'session_code' => $session->session_code,
                    'student_code' => $student->student_code,
                    'content' => fake('vi_VN')->word(),
                ]);
            }
        }
    }
}
