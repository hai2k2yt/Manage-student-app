<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'user_id' => User::where('role', RoleEnum::PARENT)->pluck('id')->random(),
            'class_id' => StudentClass::pluck('id')->random()
        ];
    }
}
