<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentClass>
 */
class StudentClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_code' => fake()->unique()->words(),
            'class_name' => 'class' . fake()->time(),
            'teacher_code' => Teacher::pluck('teacher_code')->random()
        ];
    }
}
