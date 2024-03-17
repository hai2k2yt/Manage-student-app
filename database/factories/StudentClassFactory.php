<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\StudentClass;
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
            'class_name' => 'class' . fake()->time(),
            'teacher_id' => User::where('role', RoleEnum::TEACHER)->pluck('id')->random()
        ];
    }
}
