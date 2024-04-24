<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;



class ClubSeeder extends Seeder
{

    private $club_names = array(
        "Đội Thiên Văn Học",
        "Câu Lạc Bộ Âm Nhạc Độc Đáo",
        "Hội Văn Học Sáng Tạo",
        "Gia Đình Nghệ Thuật Đa Dạng",
        "Câu Lạc Bộ Khoa Học Tương Lai",
        "Nhóm Thể Thao Đam Mê",
        "Câu Lạc Bộ Kỹ Năng Sống",
        "Liên Đoàn Ngôn Ngữ và Văn Hóa",
        "Hội Mô Phỏng Công Nghệ",
        "Cộng Đồng Tiếng Anh Sôi Động"
    );
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Club::create(
                [
                    'club_code' => 'CLUB_' . ($i + 1),
                    'name' => $this->club_names[$i],
                    'teacher_code' => Teacher::pluck('teacher_code')->random()
                ]
            );
        }
//        Club::factory(5)->create();
    }
}
